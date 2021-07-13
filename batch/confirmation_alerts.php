<!--Birthday List For Current Date-->
<?php
require_once('db/database.php');
require_once('db/users.php');
require_once('util/send-mail.php');
require_once('db/timesheet_functions.php');
$conn    = connect($config);
$message = '';
$messageUsStaffing = '';
$combinedMessage = '';

$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : '';
$departmentNameResult = getDepartmenttName($conn, $departmentId);
$departmentName = '';
if ($departmentNameResult != null) {
    $departmentName = $departmentNameResult[0]['name'];
}


$date = new DateTime();
$date->modify("-6 month");
$actualJoiningDate = $date->modify("+1 day");
$actualJoiningDateFormat = $actualJoiningDate->format('Y-m-d');

$dateUsStaffing = new DateTime();
$dateUsStaffing->modify("-9 month");
$joiningDateUStaffing = $dateUsStaffing->modify("+1 day");
$joiningDateUStaffingFormat = $joiningDateUStaffing->format('Y-m-d');



$confirmationListForHR = getConfirmationEmployeesNonUSStaffing(
    $conn,
    $actualJoiningDateFormat
);

$confirmationListForHRUsStaffing = getConfirmationEmployeesUSStaffing($conn, $joiningDateUStaffingFormat);

var_dump($confirmationListForHR);
var_dump($confirmationListForHRUsStaffing);
exit;
if ($confirmationListForHR) {
    
    $message = '<table style="border: 1px solid black;" cellspacing = "0" width = "100%"> 
        <thead> 
            <tr> 
                <th style="border: 1px solid black">Employee Id</th>
                <th style="border: 1px solid black">First Name</th> 
                <th style="border: 1px solid black">Last Name</th> 
                <th style="border: 1px solid black">Joined Date</th> 
                <th style="border: 1px solid black">E-mail</th> 
                <th style="border: 1px solid black">Department name</th>  
                
            </tr> 
        </thead>';

    foreach ($confirmationListForHR as $key => $temp) {
        
        $message .= "<tr style='border: 1px solid black'>
                        <td style='border: 1px solid black' align='center'>". $temp['employee_id'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_firstname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_lastname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['joined_date'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_work_email'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['name'] ."</td>
                        
                    </tr>";
    }

    $message .= "</table>";
} else {
    $message = "<p>No Confirmation List for Non-US Staffing</p>";
}

if ($confirmationListForHRUsStaffing) {
    
    $messageUsStaffing = '<table style="border: 1px solid black;" cellspacing = "0" width = "100%"> 
        <thead> 
            <tr> 
                <th style="border: 1px solid black">Employee Id</th>
                <th style="border: 1px solid black">First Name</th> 
                <th style="border: 1px solid black">Last Name</th> 
                <th style="border: 1px solid black">Joined Date</th> 
                <th style="border: 1px solid black">E-mail</th> 
                <th style="border: 1px solid black">Department name</th>  
                
            </tr> 
        </thead>';

    foreach ($confirmationListForHRUsStaffing as $key => $temp) {
        
        $messageUsStaffing .= "<tr style='border: 1px solid black'>
                        <td style='border: 1px solid black' align='center'>". $temp['employee_id'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_firstname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_lastname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['joined_date'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_work_email'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['name'] ."</td>
                        
                    </tr>";
    }

    $messageUsStaffing .= "</table>";

} else {

    $messageUsStaffing = "<p>No Confirmation List for US Staffing</p>";
}

$combinedMessage = $message.'<br>'.$messageUsStaffing;
print_r($combinedMessage); exit;
if ($combinedMessage != null) {
     sendMailToHr(
        'hr@suntechnologies.com', 
        $combinedMessage, 
        'Confirmation List for tomorrow'
    );
} else {
    $message = "<p>No anniversary celebration for $tomorrowDate $departmentName</p>";
    /*sendMailToHr(
        'hr@suntechnologies.com', 
        $message, 
        'Anniversary List',
        'HRM Admin'
    );  */
}



?>