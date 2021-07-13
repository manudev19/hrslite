<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Update_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->legacy_db = $this->load->database('apicalls', TRUE);
    }


public function updateMatrixTimesheet($data)
{ 
        $this->legacy_db = $this->load->database('apicalls', TRUE);
        $appender = array();

        $update_query_sql_string = "UPDATE matrix_timesheet SET ";
        $where_condition = NULL;


        $this->legacy_db->trans_begin();
        $affected_rows  = 0;
        for($i = 0; $i < count($data); $i++)
        {
            array_shift($data[$i]);

            $get_keys = array_keys($data[$i]);
           $get_values = array_values($data[$i]);
           $where_condition = " WHERE ".$get_keys[0]." = '".$get_values[0]."' AND ".$get_keys[4]." = '".$get_values[4]."';";
          // $where_condition = " WHERE ".$get_keys[0]." = '".$get_values[0]."'";
            for($j = 0; $j < count($get_keys); $j++)
            {
                    $appender[$j]= $get_keys[$j]." = '".$get_values[$j]."'";
            }

          $this->legacy_db->query($update_query_sql_string.implode(",",$appender).$where_condition);
          $affected_rows += $this->db->affected_rows();
        }
         $this->legacy_db->trans_complete();
        // $this->db->trans_off();

        return true;
     
    }
    

    public function update_hs_hr_employee($txtEmployeeId,$resion,$DOR,$emp_manager_id,$empNumber)
    {  
        $resion1 = str_replace('%20', ' ', $resion);
        $resion2 = str_replace('%22', '"', $resion1);
        $resion3 = str_replace('%60', '"', $resion2);
        $resion4 = str_replace('%7C', '|', $resion3);
        $resion5 = str_replace('%3C', '<', $resion4);
        $resion6 = str_replace('%3E', '>', $resion5);
         $date=date('Y-m-d');
        $this->legacy_db = $this->load->database('apicalls', TRUE);
       $update_query = "UPDATE  sunhrm.hs_hr_employee SET emp_resion_of_resignation = '".$resion4."', emp_Date_of_resignation = '".$DOR."', emp_manager_id = '".$emp_manager_id."', doa = CURRENT_DATE 
        WHERE employee_id = '".$txtEmployeeId."';";
                           
        $query = $this->legacy_db->query($update_query);
        header("Location: /symfony/web/index.php/pim/viewExitDetails/empNumber/$empNumber");
        exit();
        return true;
    }
    
    public function update_manager_aproval($txtEmployeeId,$personal_Manager_Status,$empNumber)
    {           $date=date('Y-m-d');
        $this->legacy_db = $this->load->database('apicalls', TRUE);
       $update_query = "UPDATE  sunhrm.hs_hr_employee SET Manager_Status = '".$personal_Manager_Status."',doa = CURRENT_DATE 
        WHERE employee_id = '".$txtEmployeeId."';";
                           
        $query = $this->legacy_db->query($update_query);
        $this->legacy_db->trans_complete();
         $this->legacy_db->affected_rows();
         header("Location: /symfony/web/index.php/pim/viewExitDetails/empNumber/$empNumber");
         exit();
        
        return true;

    }
    public function update_hr_aproval($txtEmployeeId,$personal_Hr_Status,$empNumber)
    {   
        $date=date('Y-m-d');
        $this->legacy_db = $this->load->database('apicalls', TRUE);
       $update_query = "UPDATE  sunhrm.hs_hr_employee SET HR_Status = '".$personal_Hr_Status."',doa = CURRENT_DATE
        WHERE employee_id = '".$txtEmployeeId."';";
                           
        $query = $this->legacy_db->query($update_query);
        $this->legacy_db->trans_complete();
         $this->legacy_db->affected_rows();
         header("Location: /symfony/web/index.php/pim/viewExitDetails/empNumber/$empNumber");
         exit();
       
        return true;

    }


    public function updateHrmAttendance($date,$excludeEmployees = NULL)
    { 
        
        //Refactor : 30092019
    
        //@TODO : 24092019 - REQUIREMENT FROM IT SUPPORT TO REMOVE EXCLUDED EMPLOYEES FROM IDC-1
        //@TODO : CHECK THE NULL CONDITION FOR THE EXCLUDE EMPLOYEES
        //@TODO : IF NULL : 
        //@TODO : APPEND EMPTY STRING 
        //@TODO : ELSE : 
        //@TODO : APPEND  tr.userid NOT IN (".$excludeEmployees.")

        //@TODO : APPEND THE EMPLOYEE LIST IN THE WHERE tr.userid IN(-- APPEND LIST --)
        // var_dump($excludeEmployees);
        
        
        if(!is_null($excludeEmployees))
        {
            $query_condition = " AND tr.userid NOT IN (".$excludeEmployees.") ";
        }
        else
        {
            $query_condition = "";

        }

        $this->legacy_db->trans_begin();
        
//        $sql = "UPDATE `sunhrm`.`ohrm_attendance_record` AS `dest`,
//        (
//            SELECT
//                userid,
//                CASE
//                WHEN tr.punch1 ='' THEN  DATE_FORMAT(STR_TO_DATE(tr.processdate, '%d/%m/%Y'), '%Y-%m-%d')
//                ELSE DATE_FORMAT(STR_TO_DATE(tr.punch1, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
//                END AS punch1,
//                CASE
//                WHEN tr.outpunch ='' THEN  DATE_FORMAT(STR_TO_DATE(tr.processdate, '%d/%m/%Y'), '%Y-%m-%d')
//                ELSE DATE_FORMAT(STR_TO_DATE(tr.outpunch, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
//                END AS outpunch,
//                CASE
//                WHEN tr.gross_hours = '' THEN '00:00'
//                ELSE TIME_FORMAT(REPLACE(tr.gross_hours, '.', ':'),'%H:%i') 
//                END AS workhours,  
//                CASE
//                WHEN tr.n_punch_work_hours ='' THEN '00:00'  
//                ELSE TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i')
//                END AS networkhrs,
//                TIME_FORMAT(REPLACE(tr.outtime_hhmm,'.',':'),'%H:%i') AS breakhours,
//                workingshift,
//                processdate,
//                CASE
//                WHEN firsthalf = 'PR' AND secondhalf = 'PR' THEN 'PR'
//                WHEN firsthalf = 'AB' AND secondhalf = 'AB' THEN 'AB'
//                WHEN firsthalf = 'PR' AND secondhalf = 'AB' THEN 'First Half-PR'
//                WHEN firsthalf = 'AB' AND secondhalf = 'PR' THEN 'Second Half-PR'
//                ELSE firsthalf
//                END AS firsthalf,
//                CASE
//                WHEN CAST(REPLACE(tr.n_punch_work_hours,'.','') AS UNSIGNED INTEGER) > 800 THEN  TIME_FORMAT(TIMEDIFF(TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i'),'08:00'),'%H:%i')
//                ELSE '00:00'
//                END AS overtime_hhmm,
//                emp.emp_number AS emp_number
//            FROM
//                    `matrix_attendance`.`matrix_timesheet` AS tr
//            LEFT JOIN sunhrm.hs_hr_employee AS emp ON emp.employee_id = `tr`.userid
//            WHERE
//                    `tr`.`processdate` ='".$date."'
//            AND 
//                    tr.userid NOT IN (".$excludeEmployees.")        
//        ) AS `src`
//                SET
//                    `dest`.`employee_id` = `src`.`emp_number`,
//                    `dest`.`punch_in_user_time` = `src`.`punch1`,
//                    `dest`.`punch_out_user_time` = `src`.`outpunch`,
//                    `dest`.`state` = `src`.`firsthalf`,
//                    `dest`.`working_hours` = `src`.`workhours`,
//                    `dest`.`over_time` = `src`.`overtime_hhmm`,
//                    `dest`.`break_time` = `src`.`breakhours`,
//                    `dest`.`actual_working_hours` = `src`.`networkhrs`,
//                    `dest`.`shift` = `src`.`workingshift`
//                WHERE
//                    `dest`.`login_date` = '".$date."'
//                     AND `dest`.`employee_id` = `src`.`emp_number`";
        
        
        $sql = "UPDATE `sunhrm`.`ohrm_attendance_record` AS `dest`,
        (
            SELECT
                userid,
                CASE
                WHEN tr.punch1 ='' THEN  DATE_FORMAT(STR_TO_DATE(tr.processdate, '%d/%m/%Y'), '%Y-%m-%d')
                ELSE DATE_FORMAT(STR_TO_DATE(tr.punch1, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
                END AS punch1,
                CASE
                WHEN tr.outpunch ='' THEN  DATE_FORMAT(STR_TO_DATE(tr.processdate, '%d/%m/%Y'), '%Y-%m-%d')
                ELSE DATE_FORMAT(STR_TO_DATE(tr.outpunch, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
                END AS outpunch,
                CASE
                WHEN tr.gross_hours = '' THEN '00:00'
                ELSE TIME_FORMAT(REPLACE(tr.gross_hours, '.', ':'),'%H:%i') 
                END AS workhours,  
                CASE
                WHEN tr.n_punch_work_hours ='' THEN '00:00'  
                ELSE TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i')
                END AS networkhrs,
                TIME_FORMAT(REPLACE(tr.outtime_hhmm,'.',':'),'%H:%i') AS breakhours,
                workingshift,
                processdate,
                CASE
                WHEN firsthalf = 'PR' AND secondhalf = 'PR' THEN 'PR'
                WHEN firsthalf = 'AB' AND secondhalf = 'AB' THEN 'AB'
                WHEN firsthalf = 'PR' AND secondhalf = 'AB' THEN 'First Half-PR'
                WHEN firsthalf = 'AB' AND secondhalf = 'PR' THEN 'Second Half-PR'
                ELSE firsthalf
                END AS firsthalf,
                CASE
                WHEN CAST(REPLACE(tr.n_punch_work_hours,'.','') AS UNSIGNED INTEGER) > 800 THEN  TIME_FORMAT(TIMEDIFF(TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i'),'08:00'),'%H:%i')
                ELSE '00:00'
                END AS overtime_hhmm,
                emp.emp_number AS emp_number
            FROM
                    `matrix_attendance`.`matrix_timesheet` AS tr
            LEFT JOIN sunhrm.hs_hr_employee AS emp ON emp.employee_id = `tr`.userid
            WHERE
                    `tr`.`processdate` ='".$date."'
            ".$query_condition."       
        ) AS `src`
                SET
                    `dest`.`employee_id` = `src`.`emp_number`,
                    `dest`.`punch_in_user_time` = `src`.`punch1`,
                    `dest`.`punch_out_user_time` = `src`.`outpunch`,
                    `dest`.`state` = `src`.`firsthalf`,
                    `dest`.`working_hours` = `src`.`workhours`,
                    `dest`.`over_time` = `src`.`overtime_hhmm`,
                    `dest`.`break_time` = `src`.`breakhours`,
                    `dest`.`actual_working_hours` = `src`.`networkhrs`,
                    `dest`.`shift` = `src`.`workingshift`
                WHERE
                    `dest`.`login_date` = '".$date."'
                     AND `dest`.`employee_id` = `src`.`emp_number`";
            

        $query = $this->legacy_db->query($sql);
        $this->legacy_db->trans_complete();
        return $affected_rows = $this->legacy_db->affected_rows();

    }
   public function update_EmployeesOnId($employee)  {
    
       
    $updateData = urldecode(http_build_query($employee, '', ','));
       
        $update_values = http_build_query($employee, '', ',');

        $update_query = "UPDATE sunhrm.hs_hr_employee SET ".$updateData." WHERE employee_id = '".$employee['employee_id'];
        //'UPDATE hs_hr_employee SET '.$updateData.' WHERE employee_id ='.$employeeDataArray['employee_id'];        
        $query = $this->legacy_db->query($update_query);
        $this->legacy_db->trans_complete();

         $affected_rows = $this->legacy_db->affected_rows();
         header("Location: /symfony/web/index.php/pim/viewMyPreOnboarding?success=1", true, 301);
        

   } 

    
    public function update_attr_day_to_day($data,$is_for_submitted)
    {
         $update_query = "";
         //$this->legacy_db->trans_begin();
        if($is_for_submitted)
        {
            $count = 0;
       
            foreach($data as $user)
            {
               
              $update_query = "UPDATE matrix_attendance.att_day_to_day SET ".$user->date." = '".$user->attendance."' WHERE monthyear = '".$user->monthyear."' AND employee_id = '".$user->employee_number."';"; 
              
                $query = $this->legacy_db->query($update_query);
            }
        }
        else
        {
                $employee_id = array_shift($data);
                $monthyear = array_shift($data);

                $update_values = http_build_query($data, '', ',');

                $update_query = "UPDATE matrix_attendance.att_day_to_day SET ".urldecode($update_values)." WHERE monthyear = '".$monthyear."' AND employee_id = '".$employee_id."';";
                           
                $query = $this->legacy_db->query($update_query);
        }
       
        $this->legacy_db->trans_complete();
        return $affected_rows = $this->legacy_db->affected_rows();
    }
  

   //For US employees trainees(between 6 to 7 month)
   public function update_us_trainee_employees_paidleaves($trainee_empdata,$emp_status)
   {
        
        $temp = 10.50;
        $balance=$trainee_empdata['no_of_days'];
        $present_leave= $balance + $temp;
        
        if(($emp_status=='4'||$emp_status==NULL))
        {
        $update_query = "UPDATE  sunhrm.ohrm_leave_entitlement SET no_of_days = $present_leave WHERE emp_number = '".$trainee_empdata['emp_number']."' AND leave_type_id = '1' AND YEAR(from_date) = YEAR(CURDATE())";
        $query = $this->legacy_db->query($update_query);
        $this->legacy_db->trans_complete();
        $this->legacy_db->affected_rows();
        return true;
        }
    }

    //For US employees after 8 month onwards
    public function update_us_employees_paidleaves($us_empdata,$emp_status)
   {

        $temp = 1.50;
        $balance=$us_empdata['no_of_days'];     
        $present_leave= $balance + $temp;
      
        if($emp_status=='4'||$emp_status==NULL)
        {
        $update_query = "UPDATE  sunhrm.ohrm_leave_entitlement SET no_of_days = $present_leave WHERE emp_number = '".$us_empdata['emp_number']."' AND leave_type_id = '1' AND YEAR(from_date) = YEAR(CURDATE())";
        $query = $this->legacy_db->query($update_query);
        $this->legacy_db->trans_complete();
        $this->legacy_db->affected_rows();
        return true;
        }
    }

    // For Indian employees
    public function update_indian_employees_paidleaves($indian_empdata,$emp_status)
    {
         
         $temp = 1.50;
         $balance=$indian_empdata['no_of_days'];
         $present_leave= $balance + $temp;
     
         if($emp_status=='4'||$emp_status==NULL)
         {
         $update_query = "UPDATE sunhrm.ohrm_leave_entitlement SET no_of_days = $present_leave WHERE emp_number = '".$indian_empdata['emp_number']."' AND leave_type_id = '1' AND YEAR(from_date) = YEAR(CURDATE())";
         $query = $this->legacy_db->query($update_query);
         $this->legacy_db->trans_complete();
         $this->legacy_db->affected_rows();
         return true;
         }
    }

    

    
}
