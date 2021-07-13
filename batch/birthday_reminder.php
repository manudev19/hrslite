<!--Birthday List For Current Date-->
<?php
require_once('db/database.php');
require_once('db/users.php');
require_once('util/template_texts.php');
require_once('util/constants.php');
require_once('util/send-mail.php');
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

$birthdayListForCardGeneration = getBirthdayList($conn, $currentDateMonthDateFormat, $departmentId);

$lengthOfTemplate = ($birthdayListForCardGeneration != null) ? sizeof($birthdayListForCardGeneration) : 1;

$numbers = range(0, 9);
shuffle($numbers);
$result =  array_slice($numbers, 0, $lengthOfTemplate);

if ($birthdayListForCardGeneration) {
    foreach($birthdayListForCardGeneration as $key => $temp) {
        
        $templateToBeSent = $result[$key];
        $templateText = $birthdayTexts[$templateToBeSent];
        $employeeFullName = $temp['emp_firstname']." ".$temp['emp_lastname'];
        $imagePath = IMAGE_DIRECTORY."bd_".$templateToBeSent.".jpg";
        $subject = "Wish you Happy Birthday ".$employeeFullName;
        $message = "
           <html>
                <head>
                <meta charset='utf-8'/>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width ,intial-scale=1' >
                <style type='text/css'>
                    body{
                        background-color: #eaefc6;
                        height: 100%;
                        margin-top: -25px;
                        margin-left: 50px;
                        margin-bottom: 0px;
                        margin-right: 200px;
                    }
                    .wrapper{
                       font-family:Monotype Corsiva;
                       color: #3a211f;
                       font-size:24px;
                       line-height:24px;
                       margin:10px;
                    }
                    p.h1{
                        font-family:Monotype Corsiva;
                        color: #3a211f;
                        font-size:24px;
                        text-align: center;
                        line-height:24px;
                    }
                    img{
                       margin: 27px 20px 102px 125px;
                       height: auto;
                        max-width: 500px;
                        width:100%;
                    }
                    p.h2{
                        font-family:Monotype Corsiva;
                         color: #3a211f;
                        font-size:24px;
                        text-align: center;
                        line-height:24px;
                    }
                    p.h3{
                        font-family:Monotype Corsiva;
                        color: #3a211f;
                        font-size:24px;

                    }


                    @media screen and (max-width: 400px) {
                        .wrapper{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            line-height:24px;
                            margin:10px;
                            width:506px;
                            height:45px;
                            margin-bottom: 0px;
                            margin-top: 45px;
                        }
                        p.h1{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            text-align: center;
                            line-height:24px;
                            width:512px;
                            height:40px;
                            margin-top:0px;
                            margin-bottom:0px;
                        }
                        img{
                           
                            width: 500px;
                            height: 300px;
                            margin-left: 0px;
                            margin-right: 0px;
                            margin-top: 0px;
                            margin-bottom: 0px;
                            padding-top: 20px;
                            padding-bottom: 10px;
                            max-width: 500px;
                            
                        }
                        p.h2{
                            font-family:Monotype Corsiva;
                             color: #3a211f;
                            font-size:18px;
                            text-align: center;
                            line-height:24px;
                            width:512px;
                            height:48px;
                            margin-top:0px;
                            margin-bottom:0px;

                        }
                        p.h3{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            width: 512px;
                            height:46px
                            margin-top: 0px;
                            margin-bottom: 0px;
                        }
                    }

                    @media screen and (min-width: 401px) and (max-width: 400px) {
                        .wrapper{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            line-height:24px;
                            margin:10px;
                            margin:10px;
                            width:506px;
                            height:45px;
                            margin-bottom: 0px;
                            margin-top: 45px;
                        }
                        p.h1{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            text-align: center;
                            line-height:24px;
                            width:512px;
                            height:40px;
                            margin-top:0px;
                            margin-bottom:0px;
                        }
                        img{
                           width: 500px;
                            height: 300px;
                            margin-left: 0px;
                            margin-right: 0px;
                            margin-top: 0px;
                            margin-bottom: 0px;
                            padding-top: 20px;
                            padding-bottom: 10px;
                            max-width: 500px;
                        }
                        p.h2{
                            font-family:Monotype Corsiva;
                             color: #3a211f;
                            font-size:18px;
                            text-align: center;
                            line-height:24px;
                            width:512px;
                            height:48px;
                            margin-top:0px;
                            margin-bottom:0px;
                        }
                         p.h3{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            width: 512px;
                            height:46px
                            margin-top: 0px;
                            margin-bottom: 0px;
                        }
                    }

                    @media screen and (max-width:768px) {
                        .wrapper{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            line-height:24px;
                            margin:10px;
                            margin:10px;
                            width:506px;
                            height:45px;
                            margin-bottom: 0px;
                            margin-top: 45px;
                        }
                        p.h1{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            text-align: center;
                            line-height:24px;
                            width:512px;
                            height:40px;
                            margin-top:0px;
                            margin-bottom:0px;
                        }
                        img{
                          width: 500px;
                            height: 300px;
                            margin-left: 0px;
                            margin-right: 0px;
                            margin-top: 0px;
                            margin-bottom: 0px;
                            padding-top: 20px;
                            padding-bottom: 10px;
                            max-width: 500px;
                        }
                        p.h2{
                            font-family:Monotype Corsiva;
                             color: #3a211f;
                            font-size:18px;
                            text-align: center;
                            line-height:24px;
                            width:512px;
                            height:48px;
                            margin-top:0px;
                            margin-bottom:0px;
                        }
                         p.h3{
                            font-family:Monotype Corsiva;
                            color: #3a211f;
                            font-size:18px;
                            width: 512px;
                            height:46px
                            margin-top: 0px;
                            margin-bottom: 0px;
                        }
                    }
                </style>
                </head>
                <body >
                <div class ='wrapper' >Dear $temp[emp_firstname] $temp[emp_lastname],</div>
                <p class='h1'>
                    Sun Technologies wishes you a very HAPPY BIRTHDAY !!!!!!!!<br>
                </p>
                <p class='h2'> 
                    $templateText
                </p>
                 <img src='cid:logo_2u'/>
                <p class='h3'>
                    Thanks and Regards,<br>
                    HR Team.
                </p>
                </body>
            </html>

";      
        $selectedDLs = $departmentMailList[$temp['work_station']];
        sendBirthdayMail($message, $subject,$temp['emp_work_email'], $imagePath, $selectedDLs, $employeeFullName);
    }
}



$birthdaylistForTomorrow = getBirthdayList($conn, $tomorrowDateMonthDateFormat, $departmentId);


if ($birthdaylistForTomorrow) {
    
    $message = '<table style="border: 1px solid black;" cellspacing = "0" width = "100%"> 
        <thead> 
            <tr> 
                <th style="border: 1px solid black">Employee ID</th> 
                <th style="border: 1px solid black">First Name</th> 
                <th style="border: 1px solid black">Last Name</th> 
                <th style="border: 1px solid black">Birth Date</th> 
                <th style="border: 1px solid black">E-mail</th> 
                <th style="border: 1px solid black">Department name</th>  
                <th style="border: 1px solid black">Opted-out of Birthday</th> 
            </tr> 
    </thead>';

    foreach ($birthdaylistForTomorrow as $key => $temp) {

        $optOut = ($temp['opt_out_of_birthday_mails'] != null) ? 'Yes' : 'No';
        $dateAndMonth = date('d M', strtotime($temp['emp_birthday']));
        $message .= "<tr style='border: 1px solid black'>
                        <td style='border: 1px solid black' align='center'>". $temp['employee_id'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_firstname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_lastname'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $dateAndMonth ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['emp_work_email'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $temp['name'] ."</td>
                        <td style='border: 1px solid black' align='center'>". $optOut ."</td>
                    </tr>";
    }

    $message .= "</table>";
	
    sendMailToHr(
        'hr@suntechnologies.com', 
        $message,
        "Birthday List for $tomorrowDate $departmentName",
        "HRM Admin"
    ); 
}  else {
	$message = "<p>No Birthday celebration for $tomorrowDate $departmentName</p>";
	sendMailToHr(
        'hr@suntechnologies.com', 
        $message, 
        'Birthday List',
        'HRM Admin'
    ); 
}
?>
