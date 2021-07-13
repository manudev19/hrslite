<!--Birthday List For Current Date-->
<?php
require_once('db/database.php');
require_once('db/users.php');
require_once('util/template_texts.php');
require_once('util/constants.php');
require_once('util/send-mail.php');
require_once('db/timesheet_functions.php');
$conn    = connect($config);
$messageBody = '';

$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : '';
$departmentNameResult = getDepartmenttName($conn, $departmentId);
$departmentName = '';
if ($departmentNameResult != null) {
    $departmentName = $departmentNameResult[0]['name'];
}

//to get next days date
$tomorrowDate = date('Y-m-d',strtotime("+1 days")); 
$tomorrowDateMonthDateFormat = substr("$tomorrowDate",5);

// Getting current date
$currentDate = date('Y-m-d'); //to get next days date
$currentDateMonthDateFormat = substr("$currentDate",5); //to get only month and day from the date

$anniversaryListForCardGeneration = getAnniversaryList(
    $conn, 
    $currentDateMonthDateFormat,
    $departmentId
);

$lengthOfTemplate = ($anniversaryListForCardGeneration != null) ? sizeof($anniversaryListForCardGeneration) : 1;

$numbers = range(0, 9);
shuffle($numbers);
$result =  array_slice($numbers, 0, $lengthOfTemplate);

//newly added for sending anniversary cards
if ($anniversaryListForCardGeneration) {
    foreach($anniversaryListForCardGeneration as $key =>$temp) {
        
        $templateToBeSent = $result[$key];
        $templateText = $anniversaryTexts[$templateToBeSent];
        $employeeFullName = $temp['emp_firstname']." ".$temp['emp_lastname'];
        $subject = "Happy ".$temp['anniversary']. " ". "year Anniversary ".$employeeFullName ;
        $imagePath = IMAGE_DIRECTORY."ann_".$templateToBeSent.".jpg";
        $message = createEmailTemplateForEmployee(
            $temp['emp_firstname'], 
            $temp['emp_lastname'],
            $templateText,
            $imagePath,
            $temp['anniversary']
        );

        $selectedDLs = $departmentMailList[$temp['work_station']];
        sendMail($message, $subject,$temp['emp_work_email'], $imagePath, $selectedDLs, $employeeFullName);
        
    }
}


$anniversarylistForHr = getAnniversaryList(
    $conn, 
    $tomorrowDateMonthDateFormat,
    $departmentId
);


if ($anniversarylistForHr) {
    
    $message = '<table style="border: 1px solid black;" cellspacing = "0" width = "100%"> 
        <thead> 
            <tr> 
                <th style="border: 1px solid black">Employee ID</th>
                <th style="border: 1px solid black">Employee Number</th>
                <th style="border: 1px solid black">First Name</th> 
                <th style="border: 1px solid black">Last Name</th> 
                <th style="border: 1px solid black">Joined Date</th> 
                <th style="border: 1px solid black">E-mail</th> 
                <th style="border: 1px solid black">Department name</th>  
                <th style="border: 1px solid black">Anniversary</th> 
            </tr> 
        </thead>';

    foreach ($anniversarylistForHr as $key => $temp) {
        
        $message .= "<tr style='border: 1px solid black'>
                        <td style='border: 1px solid black' align='center'>". $temp['employee_id'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_number'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_firstname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_lastname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['joined_date'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_work_email'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['name'] ."</td>
                        <td style='border: 1px solid black' align='center'>". ($temp['anniversary']+1)."</td>
                    </tr>";
    }

    $message .= "</table>";
    
     sendMailToHr(
        'hr@suntechnologies.com', 
        $message, 
        "Anniversary List $departmentName",
        'HRM Admin'
    );  
    
} else {
	$message = "<p>No anniversary celebration for $tomorrowDate $departmentName</p>";
	 sendMailToHr(
        'hr@suntechnologies.com', 
        $message, 
        'Anniversary List',
        'HRM Admin'
    );  
}

?>