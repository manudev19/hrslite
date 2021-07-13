<?php /**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */ ?>
<?php
$noOfColumns = sizeof($sf_data->getRaw('rowDates'));
$width = 350 + $noOfColumns * 75;

$actionName = sfContext::getInstance()->getActionName();
?>

<?php if ($actionName == 'viewMyTimesheet') {
            $breadcrumb['name'] = 'View Timesheet' ;
             $breadcrumb['link'] = null; ?>
    <?php  } else { ?>
        <?php if ($timesheetActon == 'viewDepartmentTimesheet') {
                $breadcrumb['name'] = "Department Timesheets";
                $breadcrumb['link'] = "/symfony/web/index.php/time/viewDepartmentTimesheet";  ?>
             <?php } else if($timesheetActon == 'timesheetSummary'){
               $breadcrumb['name'] = "Timesheet Summary";
                $breadcrumb['link'] = "/symfony/web/index.php/time/displayDepartmentTimesheetSummaryReportCriteria"; ?>
            <?php }  else if($timesheetActon == 'deptTimesheetSummary'){
               $breadcrumb['name'] = "MGMT Timesheet Summary";
                $breadcrumb['link'] = "/symfony/web/index.php/time/displayDepartmentLeadTimesheetSummaryReportCriteria"; ?>
            <?php } else{
                $breadcrumb['name'] = "Employee Timesheets";
                $breadcrumb['link'] = "/symfony/web/index.php/time/viewEmployeeTimesheet"; ?>
            <?php }
             $breadcrumbnew['name'] = 'View Timesheet' ;
             $breadcrumbnew['link'] = null; ?>
            
        <?php  }   $breadcrumb=array($breadcrumb,$breadcrumbnew);
       ?>
        <?php include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>

<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/viewTimesheet')); ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/viewTimesheet')); ?>



<style type="text/css">
    #timeComment {
        width: 365px;
        margin-bottom: 5px;
    }
</style>
<?php if ($timesheetPermissions->canRead()) { ?>
    <?php if (isset($messageData[0])): ?>
        <div class="box timesheet">
            <div class="inner">
                <div class="message <?php echo $messageData[0]; ?>">
                    <?php echo $messageData[1]; ?>
                    <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
                </div>
            </div>
        </div>
    <?php else: ?>

        <div class="box timesheet noHeader" id="timesheet">

            <div class="inner">

                <?php echo (isset($successMessage[0])) ? displayMainMessage($successMessage[0], $successMessage[1]) : '' ?>
                
                <?php 
                    /*
                    * DATE : 31102019 
                    * REFACTOR : 12112019
                    */
                        $final_flag = null;
                        $flag = null;
                        $enable_blocking_timesheet = false;                             
                        $alert = array();
                        $is_manager = false;
                        
                        $current_date = date('Y-m-d');
                        $start_date = $timesheet->startDate;
                        $end_date = $timesheet->endDate;

                        $timesheet_start_date = new DateTime($start_date);
                        $timesheet_end_date =  new DateTime($end_date);
                        $current_date_object = new DateTime($current_date);

                        $date_diff_to_start_date = $current_date_object->diff($timesheet_start_date);
                        $date_diff_to_end_date =  $timesheet_end_date->diff($current_date_object);
                        $week_diff = $timesheet_end_date->diff($timesheet_start_date);

                        $increment_to_tuesday = new DateTime($end_date);
                        $increment_to_tuesday->modify('+3 day');

                       if(($date_diff_to_start_date->days <= $week_diff->days) &&  ($week_diff->days >= $date_diff_to_end_date->days))
                       {


                           $flag = "";
                           
                           // $increment_to_tuesday->modify('+3 day');

                           $alert['class'] = "warning";
                           $alert['message'] = "Please submit your previous week timesheet by Tuesday. ";
                       }
                       else
                       {

                            $get_end_date = $timesheet->endDate;
                            $timesheet_get_end_date =  new DateTime($get_end_date);
                            
                            $increment_by_week =  new DateTime($get_end_date);
                            $increment_by_week->modify('+7 day');


                            $date_diff_to_previous_end_date = $timesheet_get_end_date->diff($current_date_object);

                            $days_array = ['Sun','Mon','Tue'];

                            if(in_array($current_date_object->format('D'),$days_array) && ($date_diff_to_previous_end_date->days < 7))
                            {
                                $flag = "";

                                // $increment_to_tuesday->modify('+10 day');

                                $alert['class'] = "warning";
                                $alert['message'] = "Please submit your previous week timesheet by Tuesday."; 
                            }
                            else
                            {
                                $flag = 'disabled="disabled" style="display:none;"';

                                $alert['class'] = "error";
                                $alert['message'] = "This timesheet cannot be submitted. Contact your team lead or managers.";                               
                            }
                       }

                        $result = substr($_SERVER['REQUEST_URI'], -15);

                       if($result == "viewMyTimesheet")
                        {
                            $flagger = true;
                        }
                        else
                        {
                            if(isset($_GET['employeeId']))
                            {
                                $get_employee_id = $_GET['employeeId'];
                            }
                            else
                            {
                                $get_employee_id = explode('employeeId/',$_SERVER['REQUEST_URI']);
                                $get_employee_id = $get_employee_id[1];     
                            }

                        }
                        // Due to work from home for all employee $enable_blocking_timesheet is true to submit the timesheet at any day
                        if(($_SESSION['empNumber'] == $get_employee_id || $flagger == "true") && $enable_blocking_timesheet == true)
                        { 
                          
                            $date_diff_from_current_to_start_date = $current_date_object->diff($timesheet_start_date);
                            
                            $final_flag = $flag;
                           //This loop used to hide submit button from Wed to Thu
                            if($actionName =='viewMyTimesheet'&& $final_flag==''){
                              
                            $days_array = ['Fri','Sat','Sun','Mon','Tue'];
                            if(in_array($current_date_object->format('D'), $days_array)
                            &&($date_diff_from_current_to_start_date->days==6||
                            $date_diff_from_current_to_start_date->days==5||
                            $date_diff_from_current_to_start_date->days==9||
                            $date_diff_from_current_to_start_date->days==8||
                            $date_diff_from_current_to_start_date->days==7)){
                                /*Temporary change for timesheet enabling*/
                                $submit_flag = $flag;
                               
                               }
                            else{
                                $submit_flag = 'disabled="disabled" style="display:none;"';
                            }
                        }

                        }
                        else
                        {
                             $is_manager = true;
                             $final_flag = "";
                        }

                        if((strtolower($timesheet->getState()) == "not submitted" && !$is_manager))
                        {
                    ?>
                    <div class="row"> 
                        <div class="message <?php echo $alert['class'];  ?>">
                            <?php echo $alert['message']; ?>
                            <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <?php
                     // This is used to hide edit button for employee after submitting of the timesheet
                     if($actionName =='viewMyTimesheet' && strtolower($timesheet->getState()) == "submitted" ){ ?>
                             <script type="text/javascript">
                                 $(document).ready(function() {
                                     $('#btnEdit').hide();
                                     });
                             </script>
	                     <?php } ?>


                

                
<div class="topnav">
  <a  class="timesheet_name"> <?php
                      $timesheetservice = new TimesheetService();
                      $idemp=$timesheetservice->getemployeeid($empId); 
                        echo ($employeeName."- ".$idemp) ;?></a>

  <div class="topnav-right">
    <a class="edit_timesheet_text">  <?php
                      
                        echo (isset($employeeName)) ? __('Timesheet for') . " " . __($headingText) . " " : __('Timesheet for') . " " . __($headingText) . "  ";
                        ?></a>
    <a >  <?php  echo $dateForm['startDates']->render(array('onchange' =>'clicked(event)')); ?></a>
    <!-- <a title=" AP - Approved<br>NA - Not Approved<br>NS - Not Submitted" class="tiptip "   ><i class="fa fa-info-circle" style="font-size:22px;padding-left:7px;margin-top: -2px;"></i></a> -->
    <?php if ($allowedToCreateTimesheets) : ?>
                   
                                          
                   <?php /*
                      * Disabeled Add Timeseet only for ESS, Supervisor and for Admins, for their timesheet
                      * Visible for Employee Timesheet and Department Timesheet
                      */ 
                     ?>
           <?php if((strpos($_SERVER['REQUEST_URI'], 'viewMyTimesheet') != true )): ?>          
       <a   data-toggle="modal" href="#createTimesheet" class="fieldHelpRight"  style="font-size:14px;margin-top:3px;text-decoration:underline;" ><?php echo __("Add Timesheet"); ?>   </a>    
       <?php endif; 
        endif;?> 
  </div>
</div>



<div class="department_name">
  <?php $timesheetservice = new TimesheetService();
                       $dept=$timesheetservice->getdepartmentonemployeeid($empId);   
                       echo ('Department - '. $dept) ;?>

</div>
<br>

                <!-- table ends -->

                <div id="validationMsg"></div>
                <div class="tableWrapper" style="overflow:auto">
                    <table style="width:100%" class="table">
                        <thead>
                            <tr>
                                <th id="projectColumn" style="width:20%"><?php echo __("Project") ?></th>
                                <th id ="activityColumn" style="width:15%"><?php echo __("Activity") ?></th>
                                <?php foreach ($rowDates as $data): ?>
                                    <th style="width:5%" class="center">
                                        <?php echo __(date('D', strtotime($data))); ?> 
                                        <?php echo date('j', strtotime($data)); ?>
                                    </th>
            <!--                        <th class="commentIcon"></th>-->
                                <?php endforeach; ?>
                                <th style="width:6%" class="center"><?php echo __("Total") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($toggleDate)):
                                $selectedTimesheetStartDate = $toggleDate;
                            else :
                                $selectedTimesheetStartDate = $timesheet->getStartDate();
                            endif;

                            if ($timesheetRows == null) :
                                ?>
                                <!-- colspan should be based on  the fields in a timesheet-->
                                <tr>
                                    <td id="noRecordsColumn" colspan="100"><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                </tr>
                            <?php
                            else:
                                // timesheet available 
                                $class = 'odd';
                                foreach ($timesheetRows as $timesheetItemRow):
                                    if ($format == '1')
                                        $total = '0:00';
                                    if ($format == '2')
                                        $total = 0;
                                    ?>
                                    <tr class="<?php echo $class; ?>">
                                        <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>
                                        <td id="columnName">
                                            <?php echo str_replace("##", "", html_entity_decode($timesheetItemRow['projectName'])); ?>
                                        </td>
                                        <td id="columnName">
                                            <?php echo html_entity_decode($timesheetItemRow['activityName']); ?>
                                        </td>
                                        <?php
                                        foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects):

                                            if ($format == '1') {
                                                ?>
                                                <td class="right comments">
                                                    <?php echo ($timesheetItemObjects->getDuration() == null ) ? "0:00" :
                                                            $timesheetItemObjects->getConvertTime();
                                                    ?><span class="commentIcon" data-toggle="modal" href="#commentDialog">
                                                    <?php
                                                    if ($timesheetItemObjects->getComment() != null)
                                                        echo image_tag(theme_path('images/comment.png'), array('id' => 'callout_' .
                                                            $timesheetItemObjects->getTimesheetItemId(), 'class' => 'icon'));
                                                    // comment -- callout
                                                    ?>
                                                    </span>                                
                                                </td>
                                            <?php } ?>

                                                <?php if ($format == '2') { ?>
                                                <td class="right"><?php echo ($timesheetItemObjects->getDuration() == null ) ? "0.00" :
                                        $timesheetItemObjects->getConvertTime();
                                                    ?><span class="commentIcon" data-toggle="modal" href="#commentDialog">
                                                    <?php
                                                    if ($timesheetItemObjects->getComment() != null)
                                                        echo image_tag(theme_path('images/comment.png'), array('id' => 'callout_' .
                                                            $timesheetItemObjects->getTimesheetItemId(), 'class' => 'icon'));
                                                    ?>
                                                    </span>                                    
                                                </td>
                                            <?php } ?>

                                            <?php
                                            if ($format == '1')
                                                $total+=$timesheetItemObjects->getDuration();

                                            if ($format == '2')
                                                $total+=$timesheetItemObjects->getConvertTime();
                                        endforeach;
                                        ?>

                                        <?php if ($format == '1') { ?>
                                            <td class="right total">
                                                <strong><?php echo $timeService->convertDurationToHours($total) ?></strong>
                                            </td>
                                        <?php } ?>
                                        <?php if ($format == '2') { ?>
                                            <td class="right total">
                                                <strong><?php echo number_format($total, 2, '.', ''); ?></strong>

                                            </td>
                                    <?php } ?>
                                    </tr>
                <?php
            endforeach;
            ?>

                                <tr class="total">
                                    <td id="totalVertical"><?php echo __('Total'); ?></td>
                                    <td></td>
                                    <?php
                                    if ($format == '1') {
                                        $weeksTotal = '0:00';
                                    }
                                    if ($format == '2') {
                                        $weeksTotal = 0.00;
                                    }
                                    foreach ($rowDates as $data):
                                        if ($format == '1') {
                                            $verticalTotal = '0:00';
                                        }
                                        if ($format == '2') {
                                            $verticalTotal = 0.00;
                                        }
                                        foreach ($timesheetRows as $timesheetItemRow):
                                            foreach ($timesheetItemRow['timesheetItems'] as $timesheetItemObjects):
                                                if ($data == $timesheetItemObjects->getDate()):
                                                    if ($format == '1')
                                                        $verticalTotal+=$timesheetItemObjects->getDuration();
                                                    if ($format == '2')
                                                        $verticalTotal+=$timesheetItemObjects->getConvertTime();
                                                    continue;
                                                endif;
                                            endforeach;
                                        endforeach;
                                        ?>
                                        <?php if ($format == '1') { ?>
                                            <td class="right"><?php echo $timeService->convertDurationToHours($verticalTotal); ?></td>
                                        <?php } ?>
                                        <?php if ($format == '2') { ?>
                                            <td class="right"><?php echo number_format($verticalTotal, 2, '.', ''); ?></td>
                                        <?php } ?>
                <?php
                $weeksTotal+=$verticalTotal;

            endforeach;
            ?>
                                    <?php if ($format == '1') { ?>
                                        <td class="right total">
                                            <strong><?php echo $timeService->convertDurationToHours($weeksTotal); ?></strong>
                                        </td>
                                    <?php } ?>
                                <?php if ($format == '2') { ?>
                                        <td class="right total">
                                            <strong><?php echo number_format($weeksTotal, 2, '.', ''); ?></strong>
                                        </td>
            <?php } ?>
                                </tr>
        <?php endif; ?>
                        </tbody>
                    </table>
                </div> <!-- tableWrapper -->
                <div class="bottom">
                    <em><h2><?php echo __('Status') . ': ' ?><?php echo __(ucwords(strtolower($timesheet->getState()))); ?></h2></em>                    
                    <form id="timesheetFrm" name="timesheetFrm"  method="post">
                            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
                        <p>
                                <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_MODIFY])) : ?>
                                    <input type="button" class="edit" name="button" id="btnEdit" value="<?php echo __('Edit');  ?>" <?php echo $final_flag; ?> />
                                <?php endif; ?>

                                <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT])) : ?>
                                    <?php if ( $weeksTotal!==null):?>
                                        <input type="button" class="" name="button" id="btnSubmit" value="<?php echo __('Submit'); ?>" <?php if($actionName=='viewMyTimesheet'&& $final_flag=='') echo $submit_flag; else echo $final_flag; ?> />
                                <?php endif; ?>
                                <?php endif; ?>

                                <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_RESET])) : ?>
                                    <input type="button" class="reset"  name="button" id="btnReset" value="<?php echo __('Reset') ?>" <?php echo $final_flag; ?> />
                                <?php endif; ?>
                        </p>         
                    </form>
                </div>
            </div> <!-- inner -->
        </div> <!-- Box -->

        <div class="box miniList">
                <div class="head">
                    <h1 id="actionLogHeading"><?php echo __("Biometric Details"); ?></h1>
                </div>

                <div class="inner">
                    <table border="0" cellpadding="5" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <th id="actionlogPerform" width="12%"><?php echo __('Date'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('In Time'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('Out Time'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('Break Time'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('Working Hours'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('Actual Working Hours'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('Overtime'); ?></th>
                                <th id="actionlogPerform" width="12%"><?php echo __('Attendance Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach ($attendanceDetails as $attendanceRecord):
                        ?> 
                            <tr>
                                <td id="actionlogStatus"><?php echo $attendanceRecord['date']; ?></td>
                                <td id="actionlogStatus"><?php echo $attendanceRecord['inTime']; ?></td>
                                <td id="actionlogStatus"><?php echo $attendanceRecord['outTime']; ?></td>
                                <td id="actionlogStatus"><?php echo $attendanceRecord['breakTime']; ?></td>

                                <td id="actionlogStatus"><?php echo $attendanceRecord['WorkingHrs']; ?></td>
                                <td id="actionlogStatus"><?php echo $attendanceRecord['actualWorkingHrs']; ?></td> 
                                <td id="actionlogStatus"><?php echo $attendanceRecord['overTime']; ?></td>
                                <td id="actionlogStatus"><?php echo $attendanceRecord['status']; ?></td>
                            </tr>

                        <?php
                        endforeach;
                        ?>
                            <tr class="total">
                                <td id="totalVertical"><?php echo __('Total'); ?></td>
                                <td ></td>
                                <td ></td>
                                <td ><?php echo $timeService->convertDurationToHours($breakTimeTotal); ?></td>
                                <td ><?php echo $timeService->convertDurationToHours($wrkHrsTotal); ?></td> 
                                <td ><?php echo $timeService->convertDurationToHours($actualWrkHrsTotal); ?></td>
                                <td ><?php echo $timeService->convertDurationToHours($overTimeTotal); ?></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div> <!-- inner -->
            </div> <!-- Box-miniList -->
    
       <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_APPROVE]) ||
                isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_REJECT])) :
            ?>
            <div class="box">
                <div class="head">
                    <h1 id=""><?php echo __("Timesheet Action"); ?></h1>
                </div>
                <div class="inner">
                    <form id="timesheetActionFrm" name="timesheetActionFrm"  method="post">
            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
                        <fieldset>
                            <ol>
                                <li class="largeTextBox">
                                    <label><?php echo __("Comment") ?></label>
                                    <textarea name="Comment" id="txtComment"></textarea>
                                </li>
                            </ol>
                            <p>
            <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_APPROVE])): ?>
                <?php if ( $weeksTotal!==null):?>
                                    <input type="button" class="" name="button" id="btnApprove" value="<?php echo __('Approve') ?>" />
            <?php endif; ?>
            <?php endif; ?>
            <?php if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_REJECT])) : ?>
                                    <input type="button" class="delete"  name="button" id="btnReject" value="<?php echo __('Reject') ?>" />
            <?php endif; ?>
                            </p>
                        </fieldset>
                    </form>
                </div>
            </div>
        <?php endif; ?>
          
       <div class="box miniList">
            <div class="head">
                <h1 id="actionLogHeading"><?php echo __("Leave Details"); ?></h1>
            </div>

            <div class="inner">
                <table border="0" cellpadding="5" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th id="actionlogPerform" width="20%"><?php echo __('Date'); ?></th>
                            <th id="actionlogPerform" width="20%"><?php echo __('Status'); ?></th>
                            <th id="actionlogPerform" width="20%"><?php echo __('Hours'); ?></th>
                            <th id="actionlogPerform" width="20%"><?php echo __('Leave Type'); ?></th>
                        </tr>
                    </thead>
                <tbody>

                <?php 
                foreach ($leaveDetails as $leaveData):
                ?> 
                    <tr>
                        <td id="actionlogStatus"><?php echo $leaveData['dateApplied']; ?></td>
                        <td id="actionlogPerform"><?php echo trim(preg_replace('/\s*\([^)]*\)/', '', $leaveData['status']));?>
                        <td id="actionlogPerform"><?php echo $leaveData['leavesDuration']; ?></td>
                        <td id="actionlogPerform"><?php echo $leaveData['leaveType']; ?></td>
                    </tr>

                <?php
                endforeach;
                ?>
                  
                </tbody>
            </table>
        </div> <!-- inner -->
    </div> <!-- Box-miniList -->
                

        <?php if ($actionLogRecords != null): ?>
            <div class="box miniList">

                <div class="head">
                    <h1 id="actionLogHeading"><?php echo __("Actions Performed on the Timesheet"); ?></h1>
                </div>

                <div class="inner">
                    <table border="0" cellpadding="5" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <th id="actionlogStatus" width="15%"><?php echo __('Action'); ?></th>
                                <th id="actionlogPerform" width="25%"><?php echo __('Performed By'); ?></th>
                                <th id="actionLogDate" width="15%"><?php echo __('Date'); ?></th>
                                <th id="actionLogComment" width="45%"><?php echo __('Comment'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($actionLogRecords as $row):
                                $performedBy = $row->getUsers()->getEmployee()->getFullName();
                                if (empty($performedBy) && $row->getUsers()->getIsAdmin() == 'Yes') {
                                    $performedBy = __("Admin");
                                }
                                ?>
                                <tr class="<?php echo ($i & 1) ? 'even' : 'odd'; ?>">
                                    <td id="actionlogStatus"><?php echo __(ucfirst(strtolower($row->getAction()))); ?></td>
                                    <td id="actionlogPerform"><?php echo $performedBy; ?></td>
                                    <td id="actionLogDate"><?php echo set_datepicker_date_format($row->getDateTime()); ?></td>
                                    <td id="actionLogComment"><?php echo $row->getComment(); ?></td>
                                </tr>
                <?php
                $i++;
            endforeach;
            ?>
                        </tbody>
                    </table>
                </div> <!-- inner -->

            </div> <!-- Box-miniList -->
        <?php endif; ?>

        <!-- Comment-Dialog -->
        <div class="modal hide" id="commentDialog">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3><?php echo __('Comment'); ?></h3>
            </div>
            <div class="modal-body">

                <form action="updateComment" method="post" id="frmCommentSave">
                    <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
                    <fieldset>
                        <ol>
                            <li class="line">
                                <label><?php echo __("Project Name ") ?></label>
                                <label id="commentProjectName" class="line"></label>
                            </li>
                            <li class="line">
                                <label><?php echo __("Activity Name ") ?></label>
                                <label id="commentActivityName" class="line"></label>
                            </li>
                            <li class="line">
                                <label><?php echo __("Date ") ?></label>
                                <label id="commentDate" class="line"></label>
                            </li>                    
                            <li>
                                <textarea name="leaveComment" id="timeComment"></textarea>
                            </li>
                        </ol>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button" id="commentCancel" class="reset" data-dismiss="modal" value="<?php echo __('Close'); ?>"/>
            </div>
        </div> <!-- commentDialog -->

        <!-- createTimesheet-Dialog -->
        <div class="modal hide" id="createTimesheet">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3><?php echo __('Add Timesheet'); ?></h3>
            </div>
            <div class="modal-body">
                <form  id="createTimesheetForm" action=""  method="post">
        <?php echo $createTimesheetForm['_csrf_token']; ?>
                    <fieldset>
                        <ol>
                            <li class ="line">
        <?php echo $createTimesheetForm['date']->renderLabel(__('Select a Day to Create Timesheet')); ?>
        <?php echo $createTimesheetForm['date']->render(); ?>
        <?php echo $createTimesheetForm['date']->renderError() ?>
                            </li>
                        </ol>
                    </fieldset>
                </form> 
            </div>
            <div class="modal-footer">
                <input type="button" id="addTimesheetBtn" class="" data-dismiss="modal" value="<?php echo __('Ok'); ?>"/>
                <input type="button" id="addCancel" class="reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
            </div>
        </div> <!-- createTimesheet -->

    <?php endif; ?>
<?php } ?>
<script type="text/javascript">
    var timesheetAction = "<?php echo $timesheetActon; ?>";
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var submitAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT; ?>";
    var approveAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_APPROVE; ?>";
    var rejectAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_REJECT; ?>";
    var resetAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_RESET; ?>";
    var employeeId = "<?php echo $timesheet->getEmployeeId(); ?>";
    var timesheetId = "<?php echo $timesheet->getTimesheetId(); ?>";
    var linkForViewTimesheet="<?php echo url_for('time/' . $actionName) ?>";
    var linkForEditTimesheet="<?php echo url_for('time/editTimesheet') ?>";
    var linkToViewComment="<?php echo url_for('time/showTimesheetItemComment') ?>";
    var date = "<?php echo $selectedTimesheetStartDate ?>";
    var actionName = "<?php echo $actionName; ?>";
    var erorrMessageForInvalidComment="<?php echo __("Comment should be less than 250 characters"); ?>";
    var validateStartDate="<?php echo url_for('time/validateStartDate'); ?>";
    var createTimesheet="<?php echo url_for('time/createTimesheet'); ?>";
    var returnEndDate="<?php echo url_for('time/returnEndDate'); ?>";
    var currentDate= "<?php echo $currentDate; ?>";
    var lang_noFutureTimesheets= "<?php echo __("Failed to Create: Future Timesheets Not Allowed"); ?>";
    var lang_overlappingTimesheets= "<?php echo __("Timesheet Overlaps with Existing Timesheets"); ?>";
    var lang_timesheetExists= "<?php echo __("Timesheet Already Exists"); ?>";
	var lang_oldTimesheet_cannot_create = "<?php echo __("Timesheet can not be created prior joining date"); ?>";
    var lang_invalidDate= "<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat()))));
?>";
                var dateList  = <?php echo json_encode($dateForm->getDateOptions()); ?>;
                var closeText = '<?php echo __('Close'); ?>';
    
</script>
