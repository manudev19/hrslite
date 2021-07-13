<?php
function getAllDeptUsers($conn, $workstation, $active_emp_status) {
	
	//SELECT * FROM hs_hr_employee  , hs_hr_emp_reportto WHERE work_station  = 5 and erep_sub_emp_number = emp_number and erep_reporting_mode = 2 
	//$query = "SELECT DISTINCT employee_id, emp_firstname, emp_lastname, emp_number, emp_work_email, work_station FROM hs_hr_employee  WHERE work_station  = :workstation";
	$query = "SELECT employee_id, emp_firstname, emp_lastname, emp_number, emp_work_email, joined_date, work_station, erep_sup_emp_number 
				FROM hs_hr_employee , hs_hr_emp_reportto WHERE work_station  = :workstation and erep_sub_emp_number = emp_number 
				and (erep_reporting_mode = :reporting_mode or erep_reporting_mode = 2) and  termination_id IS NULL";
	$binding = array( 
		"workstation" 		=> $workstation,
		"reporting_mode"	=> 1
		);
	
  	return query( $query, $conn , $binding  );
}

//TODO - Convert binding, user appropriate name 
function getTimesheetInfoByStartDate($conn, $emp_number, $start_alert_dt) {
	$query = "SELECT employee_id, state, start_date FROM ohrm_timesheet WHERE employee_id=:emp_number AND start_date=:start_date";

	$binding = array(
		"emp_number" => $emp_number,
		"start_date" => $start_alert_dt
		);
	return query( $query, $conn , $binding );
}

function getManagersInfoByEmpId($conn, $managerId) {
	
	$query = "SELECT employee_id, emp_firstname, emp_lastname, emp_work_email FROM hs_hr_employee WHERE employee_id = :managerId ";
	
	$binding = array(
		"managerId" => $managerId
		);
  	return query( $query, $conn, $binding);
}

function getManagersInfo($conn, $managerNumber) {
	
	$query = "SELECT employee_id, emp_firstname, emp_lastname, emp_work_email FROM hs_hr_employee WHERE emp_number = :managerNumber ";
	
	$binding = array(
		"managerNumber" => $managerNumber
		);
  	return query( $query, $conn, $binding);
}

function getDepartmenttName($conn, $deptId){

	$query = "SELECT name FROM ohrm_subunit WHERE id = :deptId ";
	
	$binding = array(
		"deptId" => $deptId
		);
  	return query( $query, $conn, $binding);
}

/**
* Name : getManagerAndSubOrdinatesList
* Purpose : To load the managers and their sub ordinates in a array list.
*/
function getManagerAndSubOridatesList($conn)
{
	$query = "SELECT erep_sup_emp_number, GROUP_CONCAT(erep_sub_emp_number) as subordinates FROM hs_hr_emp_reportto GROUP BY erep_sup_emp_number";
	return query($query, $conn);
}


/**
* Name : getSubOrdinatesLowerThan40Hours
* Purpose : To get the subordinates of a manager whose timesheets are lesser than 40 hours.
*/
function getSubOrdinatesLowerThan40Hours($conn,$subOrdinatesId, $weekStartDate, $weekEndDate)
{
	
	$query = "SELECT ohrm_attendance_record.employee_id, SEC_TO_TIME(SUM(TIME_TO_SEC(actual_working_hours))) as total_dur, SEC_TO_TIME(SUM(TIME_TO_SEC(working_hours))) as working_hours, hs_hr_employee.employee_id as employeeID, emp_firstname, emp_lastname FROM `ohrm_attendance_record` JOIN hs_hr_employee on hs_hr_employee.emp_number = ohrm_attendance_record.employee_id WHERE punch_in_user_time BETWEEN '$weekStartDate' AND '$weekEndDate' AND FIND_IN_SET(ohrm_attendance_record.employee_id, '$subOrdinatesId') AND hs_hr_employee.termination_id IS NULL GROUP BY ohrm_attendance_record.employee_id HAVING TIME_TO_SEC(total_dur) < 144000";
	
	return query( $query, $conn);
}

function getManagersIdBySubordinateId($conn, $subordinateId) {
	
	$query = "SELECT erep_sup_emp_number FROM hs_hr_emp_reportto WHERE erep_sub_emp_number = :empId ORDER BY erep_reporting_mode ASC";
	
	$binding = array(
		"empId" => $subordinateId
		);
  	return query( $query, $conn, $binding);
}

function getAllDeptUsersForAdmin($conn, $workstation, $active_emp_status) {
	
	$query = "SELECT DISTINCT employee_id, emp_firstname, emp_lastname, emp_number, emp_work_email, joined_date, work_station
				FROM hs_hr_employee , hs_hr_emp_reportto WHERE work_station  = :workstation  and erep_sub_emp_number = emp_number 
				and (erep_reporting_mode = :reporting_mode or erep_reporting_mode = 2) and  termination_id IS NULL";
	$binding = array( 
		"workstation" 		=> $workstation,
		"reporting_mode"	=> 1
		);
	
  	return query( $query, $conn , $binding  );
}