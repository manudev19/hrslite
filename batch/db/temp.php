<?php


	function getEmployeeInfoDemo($conn){

		$query="SELECT hs_hr_employee.employee_id,emp_number,emp_firstname,joined_date,
					emp_middle_name,emp_lastname
				FROM hs_hr_employee,ohrm_subunit
	       		WHERE  ohrm_subunit.id=13 AND ohrm_subunit.id= hs_hr_employee.work_station AND hs_hr_employee.emp_status=4";
			return query( $query, $conn );
	}

	function getEmployeemanager($conn,$emp_number){

		$query="SELECT  emp_firstname,emp_middle_name,emp_lastname,erep_sup_emp_number 
				FROM hs_hr_employee,hs_hr_emp_reportto,ohrm_emp_reporting_method
				WHERE erep_sup_emp_number = emp_number and erep_reporting_mode=reporting_method_id and erep_sub_emp_number =$emp_number";
		return query( $query,$conn);
	}
	
	function getEmloyeNotSubmitedData($conn,$emp_number){
		$query="SELECT ohrm_subunit.name as department,state,start_date,end_date
				FROM hs_hr_employee,ohrm_subunit,ohrm_timesheet
				WHERE ohrm_subunit.id=13 AND ohrm_subunit.id= hs_hr_employee.work_station AND hs_hr_employee.emp_status=4 and hs_hr_employee.employee_id=$emp_number and  ohrm_timesheet.employee_id=hs_hr_employee.emp_number and  state='NOT SUBMITTED'  and  start_date >= '2017-01-01' and end_date <= '2017-09-30' ORDER BY end_date ";
	
			return query( $query,$conn);
	}

	function getTimeSheetDetailsByday($conn,$emp_number,$start_date,$end_date){
		
		$query="SELECT distinct punch_in_user_time,state,actual_working_hours FROM `ohrm_attendance_record` where employee_id=$emp_number and  punch_in_user_time BETWEEN '".$start_date."' and '".$end_date."'  ORDER BY punch_in_user_time ASC";
		return query($query,$conn);
	}


	
	function AutomationFormate($conn){
		$query="SELECT hs_hr_employee.employee_id,emp_number,emp_firstname,joined_date,
					emp_middle_name,emp_lastname,state,start_date,end_date
				FROM hs_hr_employee,ohrm_subunit,ohrm_timesheet
	       		WHERE  ohrm_subunit.id=6 AND ohrm_subunit.id= hs_hr_employee.work_station AND hs_hr_employee.emp_status=4 and  ohrm_timesheet.employee_id=hs_hr_employee.emp_number and  state='SUBMITTED' 
	       			and  start_date >= '2017-01-01' and end_date <= '2017-09-30' 
	       			";

	    return query($query,$conn);		
	}
	
	
	function checkMaxStartDateAndMaxEnddate($conn,$employee_id){
		$query="SELECT max(start_date) as maxStartdate, max(end_date) as maxenddate FROM ohrm_timesheet where employee_id=$employee_id";
		return query($query,$conn);
	} 
	
	function getSubmitedData($conn,$employee_id){
		$query="SELECT * FROM `ohrm_timesheet` where employee_id=$employee_id and state='SUBMITTED' and start_date >= '2017-01-01' and end_date <= '2017-08-31'";
		return query($query,$conn);
	}

?>