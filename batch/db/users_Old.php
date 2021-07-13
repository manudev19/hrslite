<?php

//Employee Confirmation List For Current Date
function getEmployeeConfirmationlistForToday($conn) {

	$query="SELECT employee_id,emp_firstname,emp_middle_name,emp_lastname,joined_date,emp_work_email,ohrm_subunit.name as dname ,ohrm_employment_status.name as sname FROM hs_hr_employee,ohrm_subunit, ohrm_employment_status  WHERE joined_date=CURRENT_DATE() - INTERVAL 6 MONTH and ohrm_subunit.id= hs_hr_employee.work_station and ohrm_employment_status.id=hs_hr_employee.emp_status and hs_hr_employee.emp_status=4 ORDER BY work_station";

 return query( $query, $conn );
}  
  

// newly added for sending birthday card
function getAnniversaryList($conn, $date, $departmentId) {

    $query="SELECT tb1.employee_id ,tb1.emp_number, tb1.emp_firstname , tb1.emp_lastname , tb1.joined_date,tb1.
            emp_work_email,tb2.name,tb1.work_station,timestampdiff(year,tb1.joined_date,(CURRENT_DATE)) as anniversary
            FROM  hs_hr_employee tb1 JOIN ohrm_subunit tb2 on tb1.work_station= tb2.id 
			JOIN hs_hr_emp_locations on tb1.emp_number = hs_hr_emp_locations.emp_number
			WHERE 
			hs_hr_emp_locations.location_id = 1 AND
            DATE_FORMAT(tb1.joined_date, '%m-%d') = '$date' and tb1.termination_id is NULL";
    if ($departmentId != null) {
        $query .= " and tb1.work_station = $departmentId";
    }
	
    return query( $query, $conn );

}

//Birthday List For Current Date
function getBirthdayList($conn, $date, $departmentId) {

    $query="SELECT tb1.employee_id,tb1.emp_firstname,tb1.emp_lastname,tb1.emp_birthday,tb1.emp_work_email,
            tb1.opt_out_of_birthday_mails,tb1.work_station,tb2.name from hs_hr_employee tb1 
			JOIN ohrm_subunit tb2 on tb1.work_station= tb2.id 
			JOIN hs_hr_emp_locations on tb1.emp_number = hs_hr_emp_locations.emp_number
			where 
			hs_hr_emp_locations.location_id = 1 AND
			date_format(tb1.emp_birthday,'%m-%d')
            ='$date' and tb1.termination_id is NULL and tb1.opt_out_of_birthday_mails IS NULL";
    if ($departmentId != null) {
        $query .= " and tb1.work_station = $departmentId";
    }
    return query($query, $conn);
}  

function getConfirmationEmployeesNonUSStaffing($dbConnection, $joinedDate)
{
    $query = "SELECT employee_id, emp_firstname, emp_lastname, joined_date, emp_work_email,name   FROM `hs_hr_employee` 
        JOIN ohrm_subunit ON work_station = ohrm_subunit.id 
        WHERE joined_date = '$joinedDate' AND termination_id IS NULL AND 
        check_for_confirmation IS NULL AND work_station <> 13";
        
    return query($query, $dbConnection);
}


function getConfirmationEmployeesUSStaffing($dbConnection, $joinedDate)
{
    
    $query = "SELECT employee_id, emp_firstname, emp_lastname, joined_date, emp_work_email,name   FROM `hs_hr_employee` 
        JOIN ohrm_subunit ON work_station = ohrm_subunit.id 
        WHERE joined_date = '$joinedDate' AND termination_id IS NULL AND 
        check_for_confirmation IS NULL AND work_station=13";
    
    return query($query, $dbConnection);
}

	
?>
<!-- MONTH(emp_birthday).Month == 2 && DAY(emp_birthday).Day == 29 --> 
