<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	class Insert_model extends CI_Model
	{
        public function insertMatrixTimesheet($data)
        {
            $this->legacy_db = $this->load->database('apicalls', TRUE);
            $rows = NULL;
            for($i = 0; $i < count($data); $i++)
            {
                array_shift($data[$i]);
                $get_keys = implode(",",array_keys($data[$i]));
                $get_values = "'".implode("','",array_values($data[$i]))."'";
                if($i == 0)
                {
                    $rows .= "(".$get_values.")";
                }
                else
                {
                    $rows .= ",(".$get_values.")";   
                } 
            }
               $query_string = "INSERT INTO matrix_timesheet(".$get_keys.") VALUES".$rows;
                $query = $this->legacy_db->query($query_string);
                return $affected_rows = $this->legacy_db->affected_rows();
        }

        public function insertHrmAttendance($date,$excludeEmployees = NULL)
        {
            //$excludeEmployees contain the excluded employees list coming from the controller
            //@TODO : APPEND THE EMPLOYEE LIST IN THE WHERE tr.userid IN(-- APPEND LIST --)
            
            //Refactor : 30092016
            //Added condition to check the null values in the $excludeEmployees
            
                        if(!is_null($excludeEmployees))
                        {
                            $query_condition = " AND tr.userid NOT IN (".$excludeEmployees.") ";
                        }
                        else
                        {
                            $query_condition = "";

                        }

//                   $sql = "INSERT INTO sunhrm.ohrm_attendance_record (employee_id,punch_in_user_time,punch_out_user_time,state,working_hours,over_time,break_time,actual_working_hours,shift,login_date)
//                     SELECT
//                             emp_det.emp_number,
//                             CASE
//                             WHEN tr.punch1 ='' THEN CAST(CAST(tr.processdate AS DATE) AS DATETIME)
//                             ELSE DATE_FORMAT(STR_TO_DATE(tr.punch1, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
//                             END AS punch1,
//                             CASE
//                             WHEN tr.outpunch ='' THEN CAST(CAST(tr.processdate AS DATE) AS DATETIME)
//                             ELSE DATE_FORMAT(STR_TO_DATE(tr.outpunch, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
//                             END AS punch2,
//                             tr.firsthalf,
//                             TIME_FORMAT(REPLACE(tr.gross_hours, '.', ':'),'%H:%i') AS workhours,
//                             CASE
//                             WHEN CAST(REPLACE(tr.n_punch_work_hours,'.','') AS UNSIGNED INTEGER) > 800 THEN  TIME_FORMAT(TIMEDIFF(TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i'),'08:00'),'%H:%i')
//                             END AS overtime_hhmm,
//                             TIME_FORMAT(REPLACE(tr.outtime_hhmm,'.',':'),'%H:%i') AS breakhours,
//                             TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i') AS networkhrs,
//                             tr.workingshift,
//                             tr.processdate
//                        FROM
//                             matrix_attendance.matrix_timesheet AS tr
//                                     LEFT JOIN
//                             sunhrm.hs_hr_employee AS emp_det ON tr.userid = emp_det.employee_id
//                     WHERE
//                                     tr.userid = emp_det.employee_id
//                     AND 
//                                    tr.userid NOT IN (".$excludeEmployees.")                               
//                     AND
//                                     tr.processdate='".$date."'";
            
            //Refactor : 30092019
            //Added query condition.
            
             $sql = "INSERT INTO sunhrm.ohrm_attendance_record (employee_id,punch_in_user_time,punch_out_user_time,state,working_hours,over_time,break_time,actual_working_hours,shift,login_date)
                     SELECT
                             emp_det.emp_number,
                             CASE
                             WHEN tr.punch1 ='' THEN CAST(CAST(tr.processdate AS DATE) AS DATETIME)
                             ELSE DATE_FORMAT(STR_TO_DATE(tr.punch1, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
                             END AS punch1,
                             CASE
                             WHEN tr.outpunch ='' THEN CAST(CAST(tr.processdate AS DATE) AS DATETIME)
                             ELSE DATE_FORMAT(STR_TO_DATE(tr.outpunch, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')
                             END AS punch2,
                             tr.firsthalf,
                             TIME_FORMAT(REPLACE(tr.gross_hours, '.', ':'),'%H:%i') AS workhours,
                             CASE
                             WHEN CAST(REPLACE(tr.n_punch_work_hours,'.','') AS UNSIGNED INTEGER) > 800 THEN  TIME_FORMAT(TIMEDIFF(TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i'),'08:00'),'%H:%i')
                             END AS overtime_hhmm,
                             TIME_FORMAT(REPLACE(tr.outtime_hhmm,'.',':'),'%H:%i') AS breakhours,
                             TIME_FORMAT(REPLACE(tr.n_punch_work_hours,'.',':'),'%H:%i') AS networkhrs,
                             tr.workingshift,
                             tr.processdate
                        FROM
                             matrix_attendance.matrix_timesheet AS tr
                                     LEFT JOIN
                             sunhrm.hs_hr_employee AS emp_det ON tr.userid = emp_det.employee_id
                     WHERE
                                     tr.userid = emp_det.employee_id
                                    ".$query_condition."                            
                     AND
                                     tr.processdate='".$date."'";
            
            
                     $query = $this->db->query($sql);
                     return $affected_rows = $this->legacy_db->affected_rows();

         }
        
        
         //INSERT FOR EACH MONTH

        /*
        * Insert data every month 
        * date: 27022020
        */

         public function insert_attr_day_to_day($date)
         {
            $this->legacy_db = $this->load->database('apicalls', TRUE);
            $sql = "INSERT IGNORE INTO matrix_attendance.att_day_to_day(monthyear,employee_id,employee_name,employee_department)
                    SELECT
                   -- (CAST(YEAR(CURDATE()) AS UNSIGNED INTEGER) * 12) + (CAST(MONTH(CURDATE()) AS UNSIGNED INTEGER)) AS monthyear,
                    CONCAT(DATE_FORMAT(DATE('".$date."'),'%b'),'/', DATE_FORMAT(DATE('".$date."'),'%Y')) AS monthyear,
                    emp.employee_id,
                    CASE
                    WHEN emp.emp_middle_name = '' OR emp.emp_middle_name IS NULL
                    THEN CONCAT(emp.emp_firstname,' ',emp.emp_lastname)
                    WHEN emp.emp_lastname = '' OR emp.emp_lastname IS NULL
                    THEN CONCAT(emp.emp_firstname,' ',emp.emp_middle_name)
                    ELSE
                    CONCAT(emp.emp_firstname,' ',emp.emp_middle_name,' ',emp.emp_lastname)
                    END as employee_name,
                    emp.work_station as employee_department
                    FROM
                    sunhrm.hs_hr_employee AS emp
                    WHERE termination_id IS NULL
                    AND emp.employee_id NOT LIKE '____CO'
                    AND emp.employee_id NOT LIKE '____US'";
            $query = $this->legacy_db->query($sql);
            $affected_rows = $this->legacy_db->affected_rows();
            return $affected_rows;       
         }

            
    }