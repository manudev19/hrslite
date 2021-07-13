<?php
require_once 'PHPMailerAutoload.php';
require_once('util/timesheet_util.php');

define("SMTP_HOST", "email-smtp.us-east-1.amazonaws.com");
define("SMTP_AUTH", "True");
define("SMTP_USERNAME", "AKIA34I7YWRULMMJ25KM");
define("SMTP_PASSWORD", 'BLmFl9xdx+nRN65+pv9W+/uCnmPIwVL4maBWm4CHDBVN');
define("SMTP_SECURE", "ssl");
define("SMTP_PORT", "465");
define("FROM_EMAIL", "hrmadmin@suntechnologies.com");
define("IS_HTML_EMAIL", "True");

function printTimesheetDefaulters($timesheet_defaulter_list)
{
  echo "<table border=1><tr><th>Employee ID:</th><th>Employee Number</th><th>Start Date:</th><th>Work Station:</th><th>Work Email:</th><th>Employee Name</th><th>Supervisor Number</th>";
  foreach ($timesheet_defaulter_list as $emp_timesheet_info) {

    echo
      "<tr><td>" . $emp_timesheet_info->employee_id . "</td>" .
        "<td>" . $emp_timesheet_info->emp_number . "</td>";

    $default_week_list = $emp_timesheet_info->weekinfolist;

    echo   "<td>";

    foreach ($default_week_list as $default_week_info) {
      echo "$default_week_info->week($default_week_info->status)<br/>";
    }
    echo   "</td>" .
      "<td>" . $emp_timesheet_info->dept_id . "</td>" .
      "<td>" . $emp_timesheet_info->emp_work_email . "</td>" .
      "<td>" . $emp_timesheet_info->emp_firstname . " " . $emp_timesheet_info->emp_lastname . "</td>" .
      "<td>" . $emp_timesheet_info->erep_sup_emp_number . "</td></tr>";
  }
  echo "</table>";
}

function getWorkWeeksFromStartDtTillToday($start_alert_week)
{

  $work_weeks  = array();

  $current_date = date('Y-m-d');
  $end_date = date('Y-m-d', strtotime($current_date . ' - 7 day'));
  $new_week = date('Y-m-d', strtotime($start_alert_week));

  while ($new_week < $end_date) {
    $work_weeks[] = $new_week;
    $new_week = date('Y-m-d', strtotime($new_week . ' + 7 day'));
  }
  return $work_weeks;
}

function sortDefaultersByManager($timesheet_defaulter_list)
{

  $defaultersByManagerList = array();

  foreach ($timesheet_defaulter_list as $emp_timesheet_info) {
    $managerId = $emp_timesheet_info->erep_sup_emp_number;
    $defaultersByManagerList[$managerId][] = $emp_timesheet_info;
  }

  return $defaultersByManagerList;
}

function sendEmailToDefaulter($timesheet_defaulter_list)
{

  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = SMTP_AUTH;
  $mail->Username   = SMTP_USERNAME;
  $mail->Password   = SMTP_PASSWORD;
  $mail->SMTPSecure = SMTP_SECURE;
  $mail->Port       = SMTP_PORT;
  $mail->From       = FROM_EMAIL;
  $mail->IsHTML(IS_HTML_EMAIL);

  foreach ($timesheet_defaulter_list as $emp_timesheet_info) {

    $mail2 = clone $mail;
    $mail2->addAddress($emp_timesheet_info->emp_work_email);
    $mail2->Subject = 'Timesheets Alert Email';

    $timsheetInfo = mailBodyContentForDefaulters($emp_timesheet_info);

    $tableBody = '<table border><tr><th>Period</th> <th>Status</th> <th>Link</th> </tr>';
    $tableBody = $tableBody .  $timsheetInfo . '</table>';

    if ($timsheetInfo) {
      $message = " <html>
                    <body>
                      <p>Hi " . $emp_timesheet_info->emp_firstname . ",</br></p>
                      <p>You were sent this email as you have not entered the timesheet details for the below mentioned previous weeks.
                        <tr> <td>" . $tableBody . " </td>     
                        </tr>
                      <p>For any help, email us at support.hrm@suntechnologies.com </br>
                      <p>Thank you.
                      <p>This is an automated notification. 
                    </body>
                  </html>
                ";

      $mail2->Body = $message;

      if (!$mail2->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail2->ErrorInfo;
      } else {
        echo "Message has been sent to defaulters ";
      }
    }
  }
}

function mailBodyContentForDefaulters($emp_timesheet_info)
{

  $tableBody = "";
  $is_defaulter = false;
  $default_week_list = $emp_timesheet_info->weekinfolist;

  foreach ($default_week_list as $default_week_info) {
    $endDate = date('Y-m-d', strtotime($default_week_info->week . ' + 6 days'));

    if ($default_week_info->status != 'SUBMITTED') {
      $is_defaulter = true;
      $tableBody .= " <tr> <td>" . $default_week_info->week . " - " . $endDate . "</td>
              <td>" . $default_week_info->status . "</td>
              <td> <a href= 'http://hrm.sti.com/symfony/web/index.php/time/viewMyTimesheet/timesheetStartDateFromDropDown/".$default_week_info->week."/employeeId/". $emp_timesheet_info->emp_number." '>View Timesheet</td>
            </tr>";
    }
 
  }
  return ($is_defaulter) ?  $tableBody : false;
}

function sendMailToManager($defaultersByManagerList, $conn, $dept_head_list,$status)
{

  $summary_mail = "";
  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = SMTP_AUTH;
  $mail->Username   = SMTP_USERNAME;
  $mail->Password   = SMTP_PASSWORD;
  $mail->SMTPSecure = SMTP_SECURE;
  $mail->Port       = SMTP_PORT;
  $mail->From       = FROM_EMAIL;
  $mail->IsHTML(IS_HTML_EMAIL);

  foreach ($defaultersByManagerList as $managerNumber => $timesheet_defaulter_list) {
    $managersDetails = getManagersInfo($conn, $managerNumber);

    foreach ($managersDetails as $managerInfo) {
      extract($managerInfo);

      $mail2 = clone $mail;
      $mail2->addAddress($emp_work_email);
      $mail2->Subject = ucwords(strtolower($status)).' Timesheets Notification Email';

      $timsheetInfo = mailBodyContentForManagers($timesheet_defaulter_list, $managersDetails);

      if ($timsheetInfo) {
        $summary_mail = $summary_mail . $timsheetInfo;

        if (in_array($employee_id, $dept_head_list)) {
          continue;
        }

        foreach ($timesheet_defaulter_list as $emp_timesheet_info) {
          $default_week_list = $emp_timesheet_info->weekinfolist;
    
          foreach ($default_week_list as $default_week_info) { 
            if($default_week_info->status=="NOT APPROVED"){
              $header='Not Approved timesheets needs to be approved';

            }else{
              $header='Not Submitted timesheets needs to be Submit';
            }
          }
        }

        $tableBody = '<table border><tr><th>Employee Id</th> <th>Employee Name</th> <th>Week</th> <th>Status</th> <th>Action Required By</th> <th>Link</th> </tr>';
        $tableBody = $tableBody .  $timsheetInfo . '</table>';

        $message = " <html>
                        <body>
                          <p>Hi " . $emp_firstname . ",</br></p>
                          <p>All  ". $header.". Kindly visit the link to perform the action for the below mentioned timesheet.</br></p>
                            <tr> <td>" . $tableBody . " </td>     
                            </tr>
                          <p>For any help, email us at support.hrm@suntechnologies.com </br>
                          <p>Thank you.
                          <p>This is an automated notification. 
                       </body>
                      </html>
                      ";

        $mail2->Body = $message;

        if (!$mail2->send()) {
          echo 'Message could not be sent.';
          echo 'Mailer Error: ' . $mail2->ErrorInfo;
        } else {
          echo "Message has been sent to manager";
        }
      }
    }
  }
  return $summary_mail;
}

function mailBodyContentForManagers($timesheet_defaulter_list, $managersDetails)
{

  $is_defaulter = false;
  $tableBody    = "";

  foreach ($managersDetails as $managerInfo) {
    extract($managerInfo);

    foreach ($timesheet_defaulter_list as $emp_timesheet_info) {
      $default_week_list = $emp_timesheet_info->weekinfolist;

      foreach ($default_week_list as $default_week_info) {
        $endDate = date('Y-m-d', strtotime($default_week_info->week . ' + 6 days'));

        if ($default_week_info->status != "APPROVED") {
          $is_defaulter = true;
          $action_by = ($default_week_info->status == "SUBMITTED") ? $emp_firstname :  $emp_timesheet_info->emp_firstname;
          if($default_week_info->status== "NOT APPROVED")
          {
            $action_by=$managerInfo['emp_firstname'];
          }
          $tableBody .= " <tr> <td>" . $emp_timesheet_info->employee_id  . "</td>
                            <td>" . $emp_timesheet_info->emp_firstname . " " . $emp_timesheet_info->emp_lastname . "</td>
                            <td>" . $default_week_info->week . "-" . $endDate . "</td>
                            <td>" . $default_week_info->status . "</td>
                            <td>" . $action_by . "</td>
                            <td> <a href= 'http://hrm.sti.com/symfony/web/index.php/time/viewTimesheet/timesheetStartDateFromDropDown/".$default_week_info->week."/employeeId/". $emp_timesheet_info->emp_number."' >View Timesheet</td>
                        </tr>";
        }
      }
    }
  }
  return ($is_defaulter) ?  $tableBody : false;
}

function sendSummrayMailToHead($summary_mail, $conn, $dept_head_list, $dept_id,$status)
{

  $mail = new PHPMailer;
  $toAddress = "hrmtsalerts@suntechnologies.com";
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = SMTP_AUTH;
  $mail->Username   = SMTP_USERNAME;
  $mail->Password   = SMTP_PASSWORD;
  $mail->SMTPSecure = SMTP_SECURE;
  $mail->Port       = SMTP_PORT;
  $mail->From       = FROM_EMAIL;
  $mail->IsHTML(IS_HTML_EMAIL);

  $departmenttName = getDepartmenttName($conn, $dept_id);
  $deptName = (string)$departmenttName[0]['name'];

  foreach ($dept_head_list as $managerId) {

    $departmentHeadDetails = getManagersInfoByEmpId($conn, $managerId);
    if ($departmentHeadDetails) {

      foreach ($departmentHeadDetails as $deptHeadDetails) {
        extract($deptHeadDetails);

        $mail2 = clone $mail;
        $mail2->addAddress($emp_work_email);
        $mail2->addAddress($toAddress, 'TS Alert');
        $mail2->Subject = ucwords(strtolower($status)).' Timesheets Notification Email';

        $tableBody = '<table border><tr><th>Employee Id</th> <th>Employee Name</th> <th>Week</th> <th>Status</th> <th>Action Required By</th> <th>Link</th> </tr>';
        $tableBody = $tableBody .  $summary_mail . '</table>';

        $message = " <html>
                    <body>
                      <p>Hello Admin</br></p>
                      <p>Below is the timesheet summary of " . $deptName . " department employees for the previous week for your kind perusal and needful action </br></p>
                      <tr> <td>" . $tableBody . " </td>     
                        </tr>
                      <p>For any help, email us at support.hrm@suntechnologies.com </br>
                      <p>Thank you.
                      <p>This is an automated notification. 
                    </body>
                    </html>
                  ";

        $mail2->Body = $message;

        if (!$mail2->send()) {
          echo 'Message could not be sent.';
          echo 'Mailer Error: ' . $mail2->ErrorInfo;
        } else {
          echo 'Message has been sent to head';
        }
      }
    }
  }
}

//created new function to fix Admin or Head Mail triggering Issue
function sendMailToHeadOrAdmin($timesheet_defaulter_list, $conn, $dept_head_list, $dept_id,$status)
{

  $mail = new PHPMailer;
  $toAddress = "hrmtsalerts@suntechnologies.com";
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = SMTP_AUTH;
  $mail->Username   = SMTP_USERNAME;
  $mail->Password   = SMTP_PASSWORD;
  $mail->SMTPSecure = SMTP_SECURE;
  $mail->Port       = SMTP_PORT;
  $mail->From       = FROM_EMAIL;
  $mail->IsHTML(IS_HTML_EMAIL);

  $timsheetInfo = mailBodyContentForAdmin($timesheet_defaulter_list, $conn);
  $departmenttName = getDepartmenttName($conn, $dept_id);
  $deptName = (string)$departmenttName[0]['name'];

  foreach ($dept_head_list as $managerId) {

    $departmentHeadDetails = getManagersInfoByEmpId($conn, $managerId);
    if ($departmentHeadDetails) {

      foreach ($departmentHeadDetails as $deptHeadDetails) {
        extract($deptHeadDetails);

        $mail2 = clone $mail;
        $mail2->addAddress($emp_work_email);
        $mail2->addAddress($toAddress, 'TS Alert');
        $mail2->Subject = ucwords(strtolower($status)).' Timesheets Notification Email';
      }
    }
    $tableBody = '<table border><tr><th>Employee Id</th> <th>Employee Name</th> <th>Week</th> <th>Status</th> <th>Action Required By</th> <th>Link</th> </tr>';
    $tableBody = $tableBody .  $timsheetInfo . '</table>';

    $message = " <html>
                <body>
                  <p>Hello Admin</br></p>
                  <p>Below is the timesheet summary of " . $deptName . " department employees for the previous week for your kind perusal and needful action </br></p>
                  <tr> <td>" . $tableBody . " </td>     
                    </tr>
                  <p>For any help, email us at support.hrm@suntechnologies.com </br>
                  <p>Thank you.
                  <p>This is an automated notification. 
                </body>
                </html>
              ";

    $mail2->Body = $message;
    if (!$mail2->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $mail2->ErrorInfo;
    } else {
      echo 'Message has been sent to head';
    }
  }
}

function mailBodyContentForAdmin($timesheet_defaulter_list, $conn)
{

  $tableBody = '';
  foreach ($timesheet_defaulter_list as $emp_timesheet_info) {
    $default_week_list = $emp_timesheet_info->weekinfolist;
    foreach ($default_week_list as $default_week_info) {

      if ($default_week_info->status == "NOT APPROVED") {

        $action_by = '';
        $managersDetails = getManagersIdBySubordinateId($conn,  $emp_timesheet_info->emp_number);
        foreach ($managersDetails as $manger) {
          $managersDetails = getManagersInfo($conn, $manger['erep_sup_emp_number']);
          foreach ($managersDetails as $man) {
            $action_by = $action_by . $man['emp_firstname'] . ', ';
          }
        }
        $action_by =  rtrim($action_by, ", ");
      } else {
        $action_by = $emp_timesheet_info->emp_firstname;
      }

      $endDate = date('Y-m-d', strtotime($default_week_info->week . ' + 6 days'));
      $tableBody .= " <tr> <td>" . $emp_timesheet_info->employee_id  . "</td>
        <td>" . $emp_timesheet_info->emp_firstname . " " . $emp_timesheet_info->emp_lastname . "</td>
        <td>" . $default_week_info->week . '-' . $endDate . "</td>
        <td>" . $default_week_info->status . "</td>
        <td>" .  $action_by . "</td>
        <td> <a href= 'http://hrm.sti.com/symfony/web/index.php/time/viewTimesheet/timesheetStartDateFromDropDown/" . $default_week_info->week . "/employeeId/" . $emp_timesheet_info->emp_number . "' >View Timesheet</td>
        </tr>";
    }
  }
  return $tableBody;
}
