<?php

/****
 * dept ids:
 Sales & Marketing - Offshore = 2
 Application Development Services = 3
 HR Team = 4
 IT Support = 5
 Off-shore Team = 6
 Accounts = 8
 Gaming = 9
 Facility = 10
 Administration= 11
 Domestic Staffing = 12
 US Staffing =13
 QA = 14

  
 */
$dept_id = $_GET["dept_id"];//5 , 3, 6
/*
* Refactor : 26112019
*/
//$start_alert_week   = "2015-10-18";

$current_day_of_week = date('N');
$subtract = '-'.$current_day_of_week.' days';
$date_setter = date('Y-m-d',strtotime(date('Y-m-d').$subtract));
$start_alert_week = date('Y-m-d',strtotime($date_setter.' - 6 weeks'));

$active_emp_status  = 1;
$dept_head_list = array(2=> array(5, 1700),
                  3=>array(5, 1700),
                  4=>array(1700),
                  5=>array(1701, 1700),
                  6=>array(1701, 1700),
                  8=>array(1700),
                  9=>array(5, 1700),
                  10=>array(1700),
                  11=>array(1700),
                  12=>array(1703, 1700),
                  13=>array(1705, 1700),
                  14=>array(5)
                  );
$exclude_email_for_id = array(1751,1703);
/***
 *  Do not edit below
 */

require_once('db/database.php');
require_once('db/timesheet_functions.php');
require_once('util/timesheet_util.php');

//Anitha , Ravi , dept head. 
$work_weeks  = getWorkWeeksFromStartDtTillToday($start_alert_week);

$conn    = connect($config);

//get all dept users
$results = getAllDeptUsers($conn,$dept_id,$active_emp_status);
$resultsForAdmin = getAllDeptUsersForAdmin($conn,$dept_id,$active_emp_status);
if(empty($results)) {
  echo "No users in the department";
  return; 
}

$timesheet_defaulter_list = array();
$timesheet_defaulter_not_approved_list = array();
$timesheet_defaulter_list_for_admin = array();
$timesheet_defaulter_not_approved_list_for_admin = array();


foreach ($resultsForAdmin as $empInfo) {
  extract($empInfo);
  
  if(in_array($employee_id, $exclude_email_for_id)){
	continue;
  }
  $emp_id         = $employee_id;
  $emp_number     = $emp_number;
  $emp_email      = $emp_work_email;
  $emp_fname      = $emp_firstname;
  $emp_lname      = $emp_lastname;
  $emp_joinedDate = $joined_date;
  $default_weeks  = array();
  $default_weeks_not_approved = array();

  $joinedDate = date('Y-m-d',strtotime($emp_joinedDate.' - 6 days'));

  foreach ($work_weeks as $work_week) {
    
    if ( $work_week < $joinedDate ){
      continue;
    }
    $timesheet_result = getTimesheetInfoByStartDate($conn, $emp_number, $work_week);

    if(!empty($timesheet_result)){
      foreach ($timesheet_result as $timesheetInfo) {
        extract($timesheetInfo);
        
        if($state != "APPROVED"  ) {
           if($state == "SUBMITTED") {
          $default_week_info_not_approved = new stdClass;
          $default_week_info_not_approved->week = $work_week;
          $default_week_info_not_approved->status = "NOT APPROVED";
          $default_weeks_not_approved[] = $default_week_info_not_approved;
          }else{
            $default_week_info = new stdClass;
            $default_week_info->week = $work_week;
            $default_week_info->status = "NOT SUBMITTED";
            $default_weeks[] = $default_week_info; 
          }
        }
      }
    }else{
      $default_week_info = new stdClass;
      $default_week_info->week = $work_week;
      $default_week_info->status = "NOT SUBMITTED";
      $default_weeks[] = $default_week_info;
    }
  }

  if(!empty($default_weeks)){
    $timesheet_defaulter_info                       = new stdClass;
    $timesheet_defaulter_info->employee_id          =  $emp_id;
    $timesheet_defaulter_info->weekinfolist         =  $default_weeks;
    $timesheet_defaulter_info->dept_id              =  $work_station;
    $timesheet_defaulter_info->emp_work_email       =  $emp_email;
    $timesheet_defaulter_info->emp_firstname        =  $emp_fname;
    $timesheet_defaulter_info->emp_lastname         =  $emp_lname;
    $timesheet_defaulter_info->emp_number           =  $emp_number;

    $timesheet_defaulter_list_for_admin[] = $timesheet_defaulter_info;
  }

  if(!empty($default_weeks_not_approved)){
    $timesheet_defaulter_info                       = new stdClass;
    $timesheet_defaulter_info->employee_id          =  $emp_id;
    $timesheet_defaulter_info->weekinfolist         =  $default_weeks_not_approved;
    $timesheet_defaulter_info->dept_id              =  $work_station;
    $timesheet_defaulter_info->emp_work_email       =  $emp_email;
    $timesheet_defaulter_info->emp_firstname        =  $emp_fname;
    $timesheet_defaulter_info->emp_lastname         =  $emp_lname;
    $timesheet_defaulter_info->emp_number           =  $emp_number;

    $timesheet_defaulter_not_approved_list_for_admin[] = $timesheet_defaulter_info;
  }

}
//check is valid timesheet exist for the users
foreach ($results as $empInfo) {
  extract($empInfo);
  
  if(in_array($employee_id, $exclude_email_for_id)){
	continue;
  }
  $emp_id         = $employee_id;
  $emp_number     = $emp_number;
  $emp_email      = $emp_work_email;
  $emp_fname      = $emp_firstname;
  $emp_lname      = $emp_lastname;
  $emp_joinedDate = $joined_date;
  $emp_sup_number = $erep_sup_emp_number;
  $default_weeks  = array();
  $default_weeks_not_approved = array();

  $joinedDate = date('Y-m-d',strtotime($emp_joinedDate.' - 6 days'));

  foreach ($work_weeks as $work_week) {
    
    if ( $work_week < $joinedDate ){
      continue;
    }
    $timesheet_result = getTimesheetInfoByStartDate($conn, $emp_number, $work_week);

    if(!empty($timesheet_result)){
      foreach ($timesheet_result as $timesheetInfo) {
        extract($timesheetInfo);
        
        if(  $state != "APPROVED"  ) {
          if($state == "SUBMITTED") {
            $default_week_info_not_approved = new stdClass;
            $default_week_info_not_approved->week = $work_week;
            $default_week_info_not_approved->status = "NOT APPROVED";
            $default_weeks_not_approved[] = $default_week_info_not_approved;
            }else{
              $default_week_info = new stdClass;
              $default_week_info->week = $work_week;
              $default_week_info->status = "NOT SUBMITTED";
              $default_weeks[] = $default_week_info; 
            }
        }
      }
    }else{
      $default_week_info = new stdClass;
      $default_week_info->week = $work_week;
      $default_week_info->status = "NOT SUBMITTED";
      $default_weeks[] = $default_week_info;
    }
  }

  if(!empty($default_weeks)){
    $timesheet_defaulter_info                       = new stdClass;
    $timesheet_defaulter_info->employee_id          =  $emp_id;
    $timesheet_defaulter_info->weekinfolist         =  $default_weeks;
    $timesheet_defaulter_info->dept_id              =  $work_station;
    $timesheet_defaulter_info->emp_work_email       =  $emp_email;
    $timesheet_defaulter_info->emp_firstname        =  $emp_fname;
    $timesheet_defaulter_info->emp_lastname         =  $emp_lname;
    $timesheet_defaulter_info->emp_number           =  $emp_number;
    $timesheet_defaulter_info->erep_sup_emp_number  =  $emp_sup_number;

    $timesheet_defaulter_list[] = $timesheet_defaulter_info;
  }

  if(!empty($default_weeks_not_approved)){
    $timesheet_defaulter_info                       = new stdClass;
    $timesheet_defaulter_info->employee_id          =  $emp_id;
    $timesheet_defaulter_info->weekinfolist         =  $default_weeks_not_approved;
    $timesheet_defaulter_info->dept_id              =  $work_station;
    $timesheet_defaulter_info->emp_work_email       =  $emp_email;
    $timesheet_defaulter_info->emp_firstname        =  $emp_fname;
    $timesheet_defaulter_info->emp_lastname         =  $emp_lname;
    $timesheet_defaulter_info->emp_number           =  $emp_number;
    $timesheet_defaulter_info->erep_sup_emp_number  =  $emp_sup_number;

    $timesheet_defaulter_not_approved_list[] = $timesheet_defaulter_info;
  }

}
//TODO print array 
//printTimesheetDefaulters
printTimesheetDefaulters($timesheet_defaulter_list);
sendEmailToDefaulter($timesheet_defaulter_list_for_admin);

$defaultersByManagerList = sortDefaultersByManager($timesheet_defaulter_list);
$defaultersByApprovalList = sortDefaultersByManager($timesheet_defaulter_not_approved_list);

if(!empty($defaultersByApprovalList)){
$summary_mail_not_approved = sendMailToManager($defaultersByApprovalList,$conn, $dept_head_list[$dept_id],$default_week_info_not_approved->status);
}
if(!empty($defaultersByManagerList)){
$summary_mail = sendMailToManager($defaultersByManagerList, $conn, $dept_head_list[$dept_id],$default_week_info->status);
}

if(!empty($timesheet_defaulter_list_for_admin)){
sendMailToHeadOrAdmin($timesheet_defaulter_list_for_admin, $conn , $dept_head_list[$dept_id], $dept_id,$default_week_info->status);
}

if(!empty($timesheet_defaulter_not_approved_list_for_admin)){
sendMailToHeadOrAdmin($timesheet_defaulter_not_approved_list_for_admin, $conn , $dept_head_list[$dept_id], $dept_id,$default_week_info_not_approved->status);
}
// sendSummrayMailToHead($summary_mail,  $conn , $dept_head_list[$dept_id], $dept_id);
// sendSummrayMailToHead($summary_mail_not_approved,  $conn , $dept_head_list[$dept_id], $dept_id);

?>