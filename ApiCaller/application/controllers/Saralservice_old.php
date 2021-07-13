<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

defined('BASEPATH') || exit('No direct script access allowed');

//* ADDING TRAIT TO FETCH TEMPLATE PATH
//TODO  :  create a function that accepts a flag and returns the respective template depending on flag
trait getTemplatePath
{
	public static function setTemplateOnDays($days)
	{
		return APPPATH."third_party\\".$days.".xlsx";
	}
}

class SaralService extends CI_Controller
{
	//init trait;
	use getTemplatePath;

	private $template_path;
	
	public function __construct()
	{
		parent::__construct();


		$this->load->model('create_model','create');
		$this->load->model('insert_model','insert');
		$this->load->model('select_model','select');
		$this->load->model('update_model','update');

		//$this->template_path = APPPATH."third_party\31 DAYS.xlsx";

	}
//
//	 public function create_table_attandance_day_to_day()
//	 {
//	 	$is_created = $this->create->create_att_day_to_day();
//		
//	 	if($is_created)
//	 	{
//	 		echo "Table Created";
//	 	}
//	 }

	//! CREATE A BATCH JOB TO INSERT MONTHLY ATTENDANCE
	
	public function insert_to_attendance_day_to_day($insert_date)
	{

			// $date = date('2019-12-01',strtotime($yearmonth));
			$date = date('Y-m-d',strtotime($insert_date));
			$is_inserted = $this->insert->insert_attr_day_to_day($date);
			
			if($is_inserted > 0)
			{
				$header = header($_SERVER['SERVER_PROTOCOL']." 200 OK");
				return $header;
			}
	}


	//? Dates Range
	// @param Date $from_date = 22-01-2019
	// @param Date $to_date = 21-01-2019
	// @return array $weeks_array

	
	//? INFO : FROM DATE SHOULD BE 22-JAN
	//? INFO : END DATE SHOULD BE 21-FEB

	//// NEED SUGGESTIONS 
	////CAN WE CREATE A BATCH JOB THAT DOWNLOADS EXCEL SHEETS 

	private function get_weeks_range($from_date, $to_date)
	{
        $weeks_array = array();

        $start_date = date('Y-m-d', strtotime($from_date));
        $end_date = date('Y-m-d', strtotime($to_date));

        $start_date_object = new DateTime($start_date);
        $end_date_object = new DateTime($end_date);

        if (($start_date_object->format("N") > 1) && ($start_date_object->format("D") != "Sun")) {
            $diffrence = "-".($start_date_object->format("N"))." Days";
            $start_date_object = $start_date_object->modify($diffrence);
        }

        if (($end_date_object->format("w") > 1) && ($end_date_object->format("D") != "Sun")) {
            $diffrence = 6 - $end_date_object->format("w");
            $date_diffrence = "+".$diffrence."Days";
            $end_date_object = $end_date_object->modify($date_diffrence);
        }
            
        $interval = $start_date_object->diff($end_date_object);
        $weeks = floor(($interval->days) / 7);
            
        
        for ($i = 0; $i <= $weeks; $i++) {
            if ($i == 0) {
                $start_date = $start_date_object->format('Y-m-d');
                $start_date_object->add(new DateInterval('P6D'));
            } else {
                $start_date_object->add(new DateInterval('P6D'));
            }
                            
            $weeks_array[$i]['start_date'] = $start_date;
            $weeks_array[$i]['end_date'] = $start_date_object->format('Y-m-d');
            $start_date_object->add(new DateInterval('P1D'));
            $start_date = $start_date_object->format('Y-m-d');
        }
		
        return $weeks_array;
    }

	/**
	 * * THIS FUNCTION CHECKS FOR THE START AND END DATES FOR ARRAY 
	 * * IF START DATE IS DIFFRENT FROM GIVEN 'FROM DATE' IT SETS TO 'FROM DATE'
	 */

	public function checkAndResetWeeks($from_date, $to_date)
	{

		//print_r($_SESSION);
		// if(isset($_SESSION['from_date']) && isset($_SESSION['to_date']))
		// {
		 	$_SESSION['from_date'] = $from_date;
		 	$_SESSION['to_date'] = $to_date;
		// }

		$get_week_range = $this->get_weeks_range($from_date, $to_date);
		$counter = count($get_week_range);
	
		for($x = 0; $x < $counter; $x++)
		{
			$bool_reset_start_date = (strtotime($get_week_range[$x]['start_date']) < strtotime($_SESSION['from_date'])) && (strtotime($get_week_range[$x]['end_date']) > strtotime($_SESSION['from_date']));
			$bool_reset_end_date = (strtotime($get_week_range[$x]['start_date']) < strtotime($_SESSION['to_date'])) && ( strtotime($get_week_range[$x]['end_date']) > strtotime($_SESSION['to_date']));
			
			if(($x == 0) && ($bool_reset_start_date))
			{
				$get_week_range[$x]['start_date'] = date('Y-m-d',strtotime($_SESSION['from_date']));
			}
			
			if(($x == $counter - 1) && ($bool_reset_end_date))
			{
				$get_week_range[$x]['end_date'] = date('Y-m-d',strtotime($_SESSION['to_date']));
			}
		}
	
		return $get_week_range;
	}
	
	

	// private function update_attendance_unapproved($yearmonth)
	private function update_attendance_unapproved($from_date,$to_date)
	{
		//! REMOVED get_weeks
		// $dates_array = $this->get_weeks($yearmonth);

		$dates_array = $this->checkAndResetWeeks($from_date,$to_date);

		for($i = 0; $i < count($dates_array); $i++)
		{
			$unsubmitted_data = array();
			
			$result = $this->select->filter_attendance_on_unapproved_timesheets($dates_array[$i]['start_date'], $dates_array[$i]['end_date']);
			for($j = 0; $j < count($result); $j++)
			{

				$from_date = $result[$j]['start_date'];
				$to_date = $result[$j]['end_date'];


				$unsubmitted_data["employee_number"] = $result[$j]['employee_number'];	
				$unsubmitted_data["monthyear"] = date("M/Y",strtotime($from_date));

				
				$interval = new DateInterval('P1D'); 
    			$end_date = new DateTime($to_date); 
    			$end_date->add($interval); 
  
    			$period = new DatePeriod(new DateTime($from_date), $interval, $end_date); 
  
		    		// Use loop to store date into array 
		    	foreach($period as $date) 
		    	{              
		        	$unsubmitted_data["DAY".$date->format('j')] = "'"."LOP"."'";  
		    	} 

				$this->update->update_attr_day_to_day($unsubmitted_data,false);
			}
		}

	}

	// private function update_attendance_approved($yearmonth)
	private function update_attendance_approved($from_date,$to_date)
	{

		$dates_array = $this->checkAndResetWeeks($from_date,$to_date);
		$result = "";
		for($i = 0; $i < count($dates_array); $i++)
		{
			$result = $this->select->filter_attendance_on_approved_timesheets($dates_array[$i]['start_date'], $dates_array[$i]['end_date']);
			$this->update->update_attr_day_to_day($result,true);
		}
		
	}

	// public function update_attendance_records($yearmonth)
	public function update_attendance_records($from_date,$to_date)
	{ 
		//BENCHMARKING START
		//$this->benchmark->mark('code_start');
		//******************** */

			$this->update_attendance_unapproved($from_date,$to_date);
			$this->update_attendance_approved($from_date,$to_date);	
		
		//BENCHMARKING END
		// $this->benchmark->mark('code_end');
		
		// $this->output->enable_profiler(TRUE); 

		//******************** */

	
	}

	// public function download_attendance_data($month,$year,$departmentId = NULL)
		public function template($from_date, $to_date,$user_data,$date)
	{	
			$currentDate = new DateTime(date('Y-m-d',strtotime($_SESSION['from_date'])));
			$expectedDate = new DateTime(date('Y-m-d',strtotime($_SESSION['to_date'])));
			$expectedDate = $expectedDate->modify('+1 DAYS');
			$diff = $currentDate->diff($expectedDate);
			$days = $diff->format('%a DAYS');
			$this->template_path = $this->setTemplateOnDays($days);
			$objPHPExcel = $objPHPExcel = PHPExcel_IOFactory::load($this->template_path);
			$objPHPExcel->setActiveSheetIndex(1);
			$day=count($date);	
			$rows_array = null;
		for( $i = 'A',$j = 0; $i <= 'Z', $j < count($date); $i++,$j++ )
				{
					if( $i == 'AI')
					{
						exit;
					}
					else
					{	
						$rows_array[$i] = $date[$j];
					}   
				}
				$rowCount = 4;
				foreach ($user_data->result_array() as $data) 
				{			
					if($day==34)
					{
						$date=array_slice($data,0,36);
						$is_lop = true;
						$check_array = $rows_array;
						array_shift($check_array);
						array_shift($check_array);
						array_shift($check_array);
						$validate_count = 0;
					}
					else
					{
				     	$is_lop = true;
				     	$check_array = $rows_array;
				     	array_shift($check_array);
				     	array_shift($check_array);
				     	array_shift($check_array);
				     	$validate_count = 0;
					}
					foreach($check_array as $key => $value)
					{
						if($data[$value] == 'P')
						{
							$validate_count++;
						}
					}
					if($validate_count == count($check_array))
					{
						$is_lop = false;
					}
					if($is_lop)
					{
						foreach($rows_array as $key => $value)
						{
							
							$objPHPExcel->getActiveSheet()->SetCellValue($key.$rowCount, $data[$value]);
						}
						$rowCount++;
					}
				}
			
		$filename = "attendance_report_". date("Y-m-d").".xlsx";
		header('Content-Description: File Transfer');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
		ob_end_clean();
		$objWriter->save('php://output'); 
		unset($_SESSION);
	}

	public function download_attendance_data($from_date,$to_date,$departmentId)
	{
			$this->update_attendance_records($from_date, $to_date);
			$month = date('M', strtotime($_SESSION['to_date']));
			$year = date('Y', strtotime($_SESSION['to_date']));
			$to_date=$_SESSION['to_date'];
			$from_date=$_SESSION['from_date'];	
			$coloumn_array_for_31Days = array('employee_id','employee_name','monthyear','DAY22','DAY23','DAY24','DAY25','DAY26','DAY27','DAY28','DAY29','DAY30','DAY31','DAY1','DAY2','DAY3','DAY4','DAY5','DAY6','DAY7','DAY8','DAY9','DAY10','DAY11','DAY12','DAY13','DAY14','DAY15','DAY16','DAY17','DAY18','DAY19','DAY20','DAY21');
			$coloumn_array_for_30Days = array('employee_id','employee_name','monthyear','DAY22','DAY23','DAY24','DAY25','DAY26','DAY27','DAY28','DAY29','DAY30','DAY1','DAY2','DAY3','DAY4','DAY5','DAY6','DAY7','DAY8','DAY9','DAY10','DAY11','DAY12','DAY13','DAY14','DAY15','DAY16','DAY17','DAY18','DAY19','DAY20','DAY21');
			$coloumn_array_for_29Days = array('employee_id','employee_name','monthyear','DAY22','DAY23','DAY24','DAY25','DAY26','DAY27','DAY28','DAY29','DAY1','DAY2','DAY3','DAY4','DAY5','DAY6','DAY7','DAY8','DAY9','DAY10','DAY11','DAY12','DAY13','DAY14','DAY15','DAY16','DAY17','DAY18','DAY19','DAY20','DAY21');
			$coloumn_array_for_28Days = array('employee_id','employee_name','monthyear','DAY22','DAY23','DAY24','DAY25','DAY26','DAY27','DAY28','DAY1','DAY2','DAY3','DAY4','DAY5','DAY6','DAY7','DAY8','DAY9','DAY10','DAY11','DAY12','DAY13','DAY14','DAY15','DAY16','DAY17','DAY18','DAY19','DAY20','DAY21');
			$user_data = $this->select->select_user_data($month,$year,$departmentId);
			if($month=="May"||$month=="Jul"||$month=="Oct"||$month=="Dec")
			{			
			   if(!empty($user_data->result()))
			   {				
					$this->template($from_date,$to_date,$user_data,$coloumn_array_for_30Days);	
			   }
			}
			else if($month=="Feb"||$month=="Apr"||$month=="Jun"||$month=="Aug"||$month=="Sep"||$month=="Nov"||$month=="Jan")
			{			
				if(!empty($user_data->result()))
			    {
					$this->template($from_date,$to_date,$user_data,$coloumn_array_for_31Days);
			   }
			}
			else if($month=="Mar"&&($year%4==0))
			{			
				if(!empty($user_data->result()))
				{
					$this->template($from_date,$to_date,$user_data,$coloumn_array_for_29Days);	
				}
			}
			else
			{			
				if(!empty($user_data->result()))
				{
					$this->template($from_date,$to_date,$user_data,$coloumn_array_for_28Days);	
     			}
			 }	 
	}

}


