<!--Birthday List For Current Date-->
<?php
require_once('db/database.php');
require_once('db/timesheet_functions.php');
require_once('util/send-mail.php');
$conn    = connect($config);
$messageBody = '';

$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : '';

$date = new DateTime(); 
$date->modify("-9 day");
$weekStartDate = $date->format("Y-m-d");
$date->modify("+6 day");
$weekEndDate = $date->format("Y-m-d");


$managerAndTheirSubOrdinatesList = getManagerAndSubOridatesList(
    $conn
);



// For all the managers a list will be generated.
if ($managerAndTheirSubOrdinatesList) {

    // For Every manager this loop will be executed.
    
	$i = 0;
    foreach($managerAndTheirSubOrdinatesList as $key =>$temp) {
		
        $managerInfo = getManagersInfo($conn, $temp['erep_sup_emp_number']);

        $managerEmail = $managerInfo[0]['emp_work_email'];

        $result = getSubOrdinatesLowerThan40Hours($conn, $temp['subordinates'], $weekStartDate, $weekEndDate);
        
        
        // Creating report for all the managers.
        if ($result) {
            
             $message = '
			 <table style="border: 1px solid black;" cellspacing = "0" width = "100%"> 
                <thead> 
                    <tr>
						<th style="border: 1px solid black">S.No</th> 
                        <th style="border: 1px solid black">Employee ID</th> 
                        <th style="border: 1px solid black">First Name</th> 
                        <th style="border: 1px solid black">Last Name</th>
						<th style="border: 1px solid black">Working Hours</th>
                        <th style="border: 1px solid black">Actual Working Hours</th> 
                    </tr> 
                </thead>';

            foreach ($result as $keySubordinates => $temp) {
                $message .= "<tr style='border: 1px solid black'>
				<td style='border: 1px solid black' align='center'>".$keySubordinates."</td>
                <td style='border: 1px solid black' align='center'>".$temp['employeeID']."</td>
                <td style='border: 1px solid black' align='center'>".$temp['emp_firstname']."</td>
                <td style='border: 1px solid black' align='center'>".$temp['emp_lastname']."</td>
				<td style='border: 1px solid black' align='center'>".$temp['working_hours']."</td>
                <td style='border: 1px solid black' align='center'>".$temp['total_dur']."</td>
                </tr>";
            }
			$message .= "</table>";
			sendTimesheetToManagers($managerEmail, $message, 'SubOrdinate Timesheet '.$weekStartDate.' '.$weekEndDate);
        }
        
    }
}


?>