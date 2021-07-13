<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Select_model extends CI_Model
	{
        public function __construct()
        {
            parent::__construct();

            $this->legacy_db = $this->load->database('apicalls', TRUE);
        }

        public function dateValidater($date)
        {
            $sql = "SELECT COUNT(processdate) AS count FROM matrix_timesheet WHERE processdate = '".$date."'";
            $query = $this->legacy_db->query($sql);
         
            if($query->row()->count)
            {
               return true;
            }
            else
            {
                return false;
            }
        }
        
        
        /**
         * SARAL QUERIES
         */

        public function filter_attendance_on_unapproved_timesheets($from_date,$to_date)
        {
            $sql = "SELECT
                    emp.employee_id AS employee_number,
                    ot.start_date AS start_date,
                    ot.end_date AS end_date
                    FROM 
                    ohrm_timesheet AS ot
                    JOIN
                    hs_hr_employee AS emp
                    ON
                    emp.emp_number = ot.employee_id
                    WHERE 
                    ot.state = 'NOT SUBMITTED'
                    AND
                    start_date = '".$from_date."'
                    AND
                    end_date = '".$to_date."';
                    ";

            $query = $this->db->query($sql);
            return $query->result_array();       
        }

        public function filter_attendance_on_approved_timesheets($from_date,$to_date)
        {

                if(date('D',strtotime($from_date)) == 'Sun')
                {
                    $from_date = date('Y-m-d', strtotime('+1 day' , strtotime($from_date)));
                }

                if(date('D', strtotime($to_date)) == 'Sat')
                {
                    $to_date = date('Y-m-d', strtotime('-1 day' , strtotime($to_date)));
                }

                   

            $month_year = date("Y-m-d", strtotime($_SESSION['to_date']))." 00:00:00";
                     $sql = "SELECT 
                            att_date AS attendance_date,
                            emp_id AS employee_number,
                            monthyear,
                            date,
                            leave_type AS leave_type,
                            CASE
                            WHEN attendance REGEXP '(^|\W)+([P,]*$)+($|\W)' THEN 'P'
                            WHEN attendance REGEXP '(^|\W)+[L]OP,[L]OP+($|\W)' THEN 'LOP'
                            WHEN attendance REGEXP '(^|\W)+[L]OP,[P]|[P],[L]OP+($|\W)' THEN '1/2P+1/2LOP'
                            WHEN attendance REGEXP '(^|\W)+[L]OP+($|\W)' THEN 'LOP'
                            ELSE 'P'
                            END
                            AS
                            attendance
                            FROM
                        (
                            SELECT 
                                attendance_date AS att_date,
                                employee_number AS emp_id,
                                monthyear AS monthyear,
                                date as date,
                                GROUP_CONCAT(attendance) AS attendance,
                                leave_type AS leave_type
                                FROM
                                (
                            SELECT
                                oti.date AS attendance_date,
                                emp.employee_id AS employee_number,
                                CONCAT(DATE_FORMAT('".$month_year."','%b'),'/', DATE_FORMAT('".$month_year."','%Y')) AS monthyear,
                                CONCAT('DAY',DAY(oti.date)) AS date,
                                CASE
                                WHEN (oti.duration/3600) < 4 AND (olt.name = 'Paid Leave' 
                                OR olt.name = 'Sick Leave' OR olt.name = 'Floating Holidays' OR olt.name = 'Comp Off')
                                THEN 'P'
                                WHEN (oti.duration/3600) < 4 
                                THEN 'LOP'
                                WHEN (oti.duration/3600) >= 4 AND (oti.duration/3600) < 7 AND (olt.name = 'Paid Leave' 
                                OR olt.name = 'Sick Leave' OR olt.name = 'Floating Holidays' OR olt.name = 'Comp Off')
                                THEN 'P'
                                WHEN (oti.duration/3600) >= 4 AND (oti.duration/3600) < 7 
                                THEN '1/2P+1/2LOP'
                                WHEN (oti.duration/3600) >= 7 
                                THEN 'P'
                                ELSE 'P'
                                END AS attendance,
                                olt.name as leave_type
                                FROM
                                ohrm_timesheet_item AS oti
                                -- INNER JOIN
                                -- ohrm_timesheet AS ot
                                -- ON
                                -- oti.timesheet_id = ot.timesheet_id
                                RIGHT JOIN
                                hs_hr_employee AS emp
                                ON
                                emp.emp_number = oti.employee_id
                                LEFT JOIN
                                ohrm_leave AS ol
                                ON
                                ol.date = oti.date
                                LEFT JOIN
                                ohrm_leave_type AS olt
                                ON
                                ol.leave_type_id = olt.id
                                WHERE
                                ol.status = '3'
                                AND
                                ol.emp_number = oti.employee_id
                                AND
                                oti.date
                                BETWEEN '".$from_date."' AND '".$to_date."'
                                ORDER BY
                                oti.date,oti.timesheet_id
                            ) temp
                            WHERE
                            employee_number NOT LIKE '____US'
                            AND
                            employee_number NOT LIKE '____CO'
                            AND
                            attendance 
                            IN ('P','LOP','1/2P+1/2LOP')
                            GROUP BY 
                            attendance_date,employee_number
                        )
                        temp2";



                if(strtotime($to_date) > strtotime($_SESSION['to_date']))
                {
                    
                    return null;
                }
                else
                {
                    

                    $query = $this->db->query($sql);
                    return $query->result();
                }
        }

        

        public function select_user_data($month,$year,$departmentId)
        {
            //$month_year = date('F/m',strtotime($from_date))
            // monthyear = '".$month_year."' ".$employee_department;

            //todo : READ EXCLUDED EMPLOYEES FROM A FILE

            $excluded_employees = "'721','2444','2852','1699','1700'";
            $excluded_employee_query = "AND employee_id NOT IN (".$excluded_employees.")";

             $this->legacy_db = $this->load->database('apicalls', TRUE);
             
            if($departmentId == 0)
            {
                $employee_department = "";
            }
            else
            {
                $employee_department =  "AND employee_department = '".$departmentId."'";
            }
             
            $sql = "SELECT 
                    *
                    FROM 
                    att_day_to_day 
                    WHERE 
                    monthyear = '".$month."/".$year."' ".$employee_department.$excluded_employee_query;

            
            
            $query = $this->legacy_db->query($sql);
            return $query;
        }
    


 function select_employee_confirmation()
        {
        $starting_day_range = 174;
        $ending_day_range = 200;
         $sql ="SELECT GROUP_CONCAT(distinct employee_number) as employee_number,
         employee_id,
         employee_name,
         employee_email,
         employee_joined_date,
         employee_confirmation,
         GROUP_CONCAT(manager_name) AS manager_name,
         GROUP_CONCAT(manager_email) AS manager_email
         FROM
         (    
             SELECT 
             emp.emp_number AS employee_number,
             emp.employee_id AS employee_id,
             emp.emp_work_email AS employee_email,
             CONCAT(emp.emp_firstname,' ',emp.emp_lastname) AS employee_name,
             emp.joined_date AS employee_joined_date,
             emp.emp_confirmation AS employee_confirmation,
             report.erep_sup_emp_number AS employee_report_to,
             CONCAT(hr_emp.emp_firstname,' ',hr_emp.emp_lastname) AS manager_name,
             hr_emp.emp_work_email AS manager_email
             FROM
             hs_hr_employee AS emp
             LEFT JOIN
             hs_hr_emp_reportto AS report
             ON
             emp.emp_number = report.erep_sub_emp_number
             RIGHT JOIN 
             hs_hr_employee AS hr_emp
             ON
             hr_emp.emp_number = report.erep_sup_emp_number
             WHERE
             emp.emp_status = 4
             AND
             DATEDIFF(CURDATE(), emp.joined_date) <=  '". $ending_day_range ."'
             AND
             DATEDIFF(CURDATE(), emp.joined_date) >=  '". $starting_day_range ."'
             ORDER BY
             emp.emp_status , 
             report.erep_sup_emp_number,
             emp.emp_work_email
             DESC
         )
         AS temp_employee_data
         GROUP BY
         employee_id"; 

        $query= $this->db->query($sql);
         return $query;     
                             
        }

        function select_pre_onboarding_employee()
        {
            $sql = "SELECT pre.Candidate_number AS candidate_no,
            pre.issuing_date AS issuing_Date,
            pre.joined_Date As joined_Date,
            CONCAT(pre.firstname,' ',pre.middlename,' ',pre.lastname) AS full_name,
            job.job_title AS designation,
            dept.name AS department,
            pre.dedicated AS dedicated,
            pre.international AS international,
            pre.location AS locations,
            pre.workstation AS workstation_no,
            CONCAT(hr_emp.emp_firstname,' ',hr_emp.emp_lastname) AS manager_name,
            hr_emp.emp_work_email AS manager_email
            FROM 
            hs_hr_employee AS hr_emp, 
            ohrm_subunit AS dept
            INNER JOIN
            ohrm_preonboarding AS pre
            ON
            pre.department = dept.id
            RIGHT JOIN
            ohrm_job_title AS job
            ON
            pre.designation = job.id
            WHERE 
            hr_emp.emp_number=pre.reporting_manager";
    
            $query = $this->db->query($sql);
    
            return $query;
        }

        
        public function separation_process()
        {
            $sql="SELECT CONCAT(emp_firstname,' ',emp_lastname) AS employee_name ,`emp_resion_of_resignation`,`employee_id`,`emp_Date_of_resignation`,`emp_manager_id`,`emp_work_email` FROM `hs_hr_employee` WHERE doa=CURRENT_DATE";
            $query = $this->db->query($sql);

            return $query;

        }
        public function separation_manager_process()
        {
            $sql="SELECT CONCAT(emp_firstname,' ',emp_lastname) AS employee_name ,`emp_resion_of_resignation`,`employee_id`,`emp_Date_of_resignation`,`emp_manager_id`,`emp_work_email`,`Manager_Status`	 FROM `hs_hr_employee` WHERE doa=CURRENT_DATE AND Manager_Status is NOT NULL";
            $query = $this->db->query($sql);

            return $query;

        }

        public function separation_admin_process()
        {
            $sql="SELECT CONCAT(emp_firstname,' ',emp_lastname) AS employee_name ,`emp_resion_of_resignation`,`employee_id`,`emp_Date_of_resignation`,`emp_manager_id`,`emp_work_email`,`Manager_Status`,`HR_Status`,`work_station`	 FROM `hs_hr_employee` WHERE doa=CURRENT_DATE AND  HR_Status is NOT NULL" ;
            $query = $this->db->query($sql);

            return $query;

        }
		
		/* Queries for TimesheetCreation Job  */
		/* Start */
		public function get_recent_joiners($lweek,$cweek){
			$sql_employee_list = "SELECT emp_number,joined_date
			FROM hs_hr_employee where termination_id IS NULL and emp_status=4 and joined_date between '".$lweek."' AND '".$cweek."' order by emp_number desc";
			$query = $this->db->query($sql_employee_list);
			return $query->result();
			if ( $query->num_rows() > 0 )
			 {
				$row = $query->row_array();
				return $row;
			  }
		}

		public function get_active_employees(){

			$sql_employee_list = "SELECT emp_number,employee_id,joined_date,termination_id
					FROM hs_hr_employee where termination_id IS NULL and emp_status=4 order by emp_number desc";
			$query = $this->db->query($sql_employee_list);
			return $query->result();
		}

		public function getConfigCurrentTimeSheet($key){
			$sql_config_timesheet = "SELECT value FROM hs_hr_config WHERE hs_hr_config.key='$key' ";
			$query = $this->db->query($sql_config_timesheet);
			$QUERY_RESULT = $query->result();
			return $xml = simplexml_load_String($QUERY_RESULT[0]->value);        
		}

		public function getTimeSheetValueWithEmpAndDate($empId,$sdate,$endDate){

			$sql_timesheet_select_with_date_and_emplyoee ="SELECT * from ohrm_timesheet where (start_date ='".$sdate."' AND end_date='".$endDate."') AND employee_id='".$empId."' ";
			$query = $this->db->query($sql_timesheet_select_with_date_and_emplyoee);
			if ($query->num_rows() > 0 )
			 {
				 $row = $query->row_array();
				 return $row;
			 }else{
				 return null;
			 }
		}

		public function insertTimeSheet($sdate,$edate,$emp_id){
			$sql="select timesheet_id from ohrm_timesheet order by timesheet_id desc";
			$query = $this->db->query($sql);
			$tID = $query->result();
			$incremented_id = $tID[0]->timesheet_id+1;
			$sql_insert_create_timesheet = "insert into ohrm_timesheet (timesheet_id,state,start_date,end_date,employee_id) VALUES ($incremented_id,'NOT SUBMITTED','".$sdate."','".$edate."',$emp_id)";
			$query = $this->db->query($sql_insert_create_timesheet);
			return $this->db->insert_id();
			}

		public function updateUniqueId(){
			$sql="select timesheet_id from ohrm_timesheet order by timesheet_id desc";
			$query = $this->db->query($sql);
			$tID = $query->result();
			$sql_insert_create_timesheet = "update hs_hr_unique_id set last_id=".$tID[0]->timesheet_id." where table_name='ohrm_timesheet'";
			$query = $this->db->query($sql_insert_create_timesheet);
		}
		/* End */
    }