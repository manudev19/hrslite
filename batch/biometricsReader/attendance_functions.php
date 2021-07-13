<?php

function add_attendance_db($conn, $attendance_biometrics_info) {

    $query = "INSERT INTO ohrm_attendance_record(employee_id, login_date, shift, punch_in_user_time, punch_out_user_time,"
  			. " working_hours, over_time, break_time, actual_working_hours, state )"
			. "VALUES(:emp_number, :date, :shift, :in_time, :out_time, :working_hours,"
			. " :over_time, :break_time, :actual_working_hours, :status)";
	$binding = array(
	      'emp_number'        		 => $attendance_biometrics_info->emp_number,
	      'date'         			 => $attendance_biometrics_info->date,
	      'shift'          			 => $attendance_biometrics_info->shift,
	      'in_time'       			 => $attendance_biometrics_info->in_time,
	      'out_time'      			 => $attendance_biometrics_info->out_time,
	      'working_hours'    		 => $attendance_biometrics_info->working_hours,
	      'over_time'           	 => $attendance_biometrics_info->over_time,
	      'break_time'      		 => $attendance_biometrics_info->break_time,
	      'actual_working_hours'     => $attendance_biometrics_info->actual_working_hours,
	      'status'     				 => $attendance_biometrics_info->status  
	    );

  $results = insert_query_execute( $query, $conn , $binding );

}

function getEmployeeNumber($conn , $emp_Id) {
	$query = "SELECT emp_number FROM hs_hr_employee WHERE employee_id=:emp_id";
	
	$binding = array( 
	    'emp_id' => $emp_Id
	  );

    $results = query( $query, $conn , $binding );

    if($results){
	   	foreach ($results as $row) {
	  	 	extract($row);
	  	 	return $emp_number;
	  	}
	}
	return 0;
 }