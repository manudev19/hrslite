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
 <?php if ($_SESSION['empNumber'] == $employeeId) {
    $breadcrumb['name'] = 'Edit Timesheet';
    $breadcrumb['link'] = null; ?>
<?php } else if ($_SESSION['empNumber'] != $employeeId) { ?>
    <?php if ($timesheetActon == 'viewDepartmentTimesheet') {
        $breadcrumb['name'] = "Department Timesheets";
        $breadcrumb['link'] = "/symfony/web/index.php/time/viewDepartmentTimesheet"; ?>
    <?php }  else if ($timesheetActon == 'timesheetSummary') {
        $breadcrumb['name'] = "Timesheet Summary";
        $breadcrumb['link'] = "/symfony/web/index.php/time/displayDepartmentTimesheetSummaryReportCriteria"; ?>
    <?php } else if ($timesheetActon == 'deptTimesheetSummary') {
        $breadcrumb['name'] = "MGMT Timesheet Summary";
        $breadcrumb['link'] = "/symfony/web/index.php/time/displayDepartmentLeadTimesheetSummaryReportCriteria"; ?>
    <?php  }else {
        $breadcrumb['name'] = "Employee Timesheets";
        $breadcrumb['link'] = "/symfony/web/index.php/time/viewEmployeeTimesheet"; ?>
    <?php }
    $breadcrumbnew['name'] = 'Edit Timesheet';
    $breadcrumbnew['link'] = null; ?>
<?php }
$breadcrumb = array($breadcrumb, $breadcrumbnew);
?>

<?php include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>
<?php
$noOfColumns = sizeof($currentWeekDates);
$width = 450 + $noOfColumns * 75;
?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/editTimesheet')); ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmTimePlugin', 'js/editTimesheetPartial')); ?>

<style type="text/css">
form ol li.largeTextBox textarea {
    width: 365px;
    margin-bottom: 5px;
}
.modal-open form ol li.largeTextBox span.validation-error {
    left: 0;
}
.stateFields{
         font-size:22px;
         padding-left:7px;
         margin-top: -2px;

     }
     #timeBox {
        background-color: lightgray;
    }
</style>

<!--<div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : ''; ?></div>-->
<div class="box editTimesheet noHeader" id="edit-timesheet">
    <div class="inner">


    <div class="topnav">
  <a  class="timesheet_name"> <?php
                      $timesheetservice = new TimesheetService();
                      $idemp=$timesheetservice->getemployeeid($empId);
                        echo ($employeeName."- ".$idemp) ;?></a>

  <div class="topnav-right">
    <a  class="edit_timesheet_text"><?php
                        echo (isset($employeeName)) ? __('Edit Timesheet ') . " " . __('for week  ') . " " . __($headingText) . " " : __(' Edit Timesheet for ') . " " . __($headingText) . " ";
                        ?></a>
    <a >  <?php  echo $dateForm['startDates']->render(array('onchange' =>'clicked(event)')); ?></a>
    <a title=" AP - Approved<br>NA - Not Approved<br>NS - Not Submitted" class="tiptip " ><i class="fa fa-info-circle stateFields" ></i></a>
    <a ><?php if ($allowedToCreateTimesheets) : ?>


                   <?php /*
                      * Disabeled Add Timeseet only for ESS, Supervisor and for Admins, for their timesheet
                      * Visible for Employee Timesheet and Department Timesheet
                      */
                     ?>
           <?php if((strpos($_SERVER['REQUEST_URI'], 'viewMyTimesheet') != true )): ?>
       <a   data-toggle="modal" href="#createTimesheet" class="fieldHelpRight"  style="font-size:14px;margin-top:3px;text-decoration:underline;" ><?php echo __("Add Timesheet"); ?>   </a>
       <?php endif;
        endif;?> </a>
  </div>
</div>

<div class="department_name">
  <?php $timesheetservice = new TimesheetService();
                       $dept=$timesheetservice->getdepartmentonemployeeid($empId);
                       echo ('Department - '. $dept) ;?>

</div>
<br>
<?php $joinedDate = $timesheetservice->getJoinedDate($empId);
        $employeeJoinedDate = $joinedDate['joined_date'];
        $ter_date = $timesheetservice->getEmployeeTerminationDate($empId);
        $ter_Id=$timesheetservice->getEmployeeTerminationId($empId);
        $employeeTerminationId=$ter_Id['termination_id'];
?>

        <div id="validationMsg">
            <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
        </div>

        <?php include_partial('global/flash_messages', array('prefix' => 'edittimesheet')); ?>
        <form class="timesheetForm" method="post" id="timesheetForm" >
            <div class="tableWrapper" style="overflow:auto">
            <table style="width:100%" class="table">
                <thead>
                    <tr>
                        <th style="width:2%" class="center"><input type="checkbox" style="display:none"></th>
                        <th style="width:24%" id="projectName"><?php echo __('Project') ?></th>
                        <th style="width:18%" id="activityName"><?php echo __('Activity') ?></th>
                        <?php foreach ($currentWeekDates as $date): ?>
                            <th class="center" style="width:8%">
                                <?php echo __(date('D', strtotime($date))); ?>
                                <?php echo date('j', strtotime($date)); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0;
                    echo $timesheetForm['_csrf_token']->render(); ?>
                    <?php if ($timesheetItemValuesArray == null): ?>
                    <tr class="odd">
                        <td id=""><?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?></td>
                        <td>
                            <?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectName']->render(array("class" => "project", "size" => 25)) ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?>
                        </td>
                        <td>
                            <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->render(array("class" => "projectActivity")); ?>
                            <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?>
                        </td>
                        <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                        <?php if ($ter_date != null  && $employeeTerminationId!=null) {
                            if ($currentWeekDates[$j] >= $employeeJoinedDate && $ter_date >= $currentWeekDates[$j]) { ?>
                             <td class="center comments">
                                <?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>
                                <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => 'timeBox', "required" => true)) ?>
                                <!--                                <span class="" data-toggle="modal" href="#commentDialog">-->
                                <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon"); ?>
                                <!--                                </span>-->
                                <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                                </td>
                                <?php } else { ?>
                                <td class="center comments">
                                 <?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>
                                 <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => 'timeBox', 'id' => 'timeBox', "required" => true, 'readonly' => true)) ?>
                                 <!--                                <span class="" data-toggle="modal" href="#commentDialog">-->
                                <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon"); ?>
                                <!--                                </span>-->
                                <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                                </td>
                                <?php }
                                    } else { ?>
                                    <td class="center comments">
                                    <?php if ($currentWeekDates[$j] < $employeeJoinedDate) { ?>
                                    <?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>
                                    <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => 'timeBox', 'id' => 'timeBox', "required" => true, 'readonly' => true)) ?>
                                    <!--                                <span class="" data-toggle="modal" href="#commentDialog">-->
                                     <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon"); ?>
                                    <!--                                </span>-->
                                    <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                                    <?php } else { ?>
                                    <?php echo $timesheetForm['initialRows'][$i][$j]->renderError() ?>
                                    <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => 'timeBox', "required" => true)) ?>
                                    <!--                                <span class="" data-toggle="modal" href="#commentDialog">-->
                                    <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon"); ?>
                                    <!--                                </span>-->
                                    <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                                    <?php } ?>
                                   </td>
                                    <?php } ?>
                        <?php } ?>
                    </tr>
                    <?php
                    $i++;
                    else:
                    $x = 0;
                    foreach ($timesheetItemValuesArray as $row):
                        $dltClassName = ($row['isProjectDeleted'] == 1 || $row['isActivityDeleted'] == 1) ? "deletedRow" : ""; ?>
                        <tr class="<?php echo ($x & 1) ? 'even' : 'odd' ?>">
                            <td id="<?php echo $row['projectId'] . "_" . $row['activityId'] . "_" . $timesheetId . "_" . $employeeId ?>">
                                <?php echo $timesheetForm['initialRows'][$i]['toDelete'] ?>
                            </td>

                            <td>
                                <?php if ($row['isProjectDeleted'] == 1) { ?>
                                    <span class="required">*</span>
                                <?php } ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectName']->renderError() ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectName']->render(array("class" => $dltClassName." "."project", "size" => 25))?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectId'] ?>
                            </td>

                            <td>
                                <?php if (($row['isActivityDeleted'] == 1)) { ?>
                                    <span class="required">*</span>
                                <?php }  ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->renderError() ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectActivityName']->render(
                                        array("class" => $dltClassName." "."projectActivity")) ?>
                                <?php echo $timesheetForm['initialRows'][$i]['projectActivityId'] ?>
                            </td>
                            <?php for ($j = 0; $j < $noOfDays; $j++) { ?>
                            <?php if ($ter_date != null && $employeeTerminationId!=null) {
                            if ($currentWeekDates[$j] >= $employeeJoinedDate && $ter_date >= $currentWeekDates[$j]) { ?>
                            <td class="center comments">
                            <!-- title="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>" -->
                           <?php echo $timesheetForm['initialRows'][$i][$j]->renderError(); ?>
                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName . " " . 'timeBox', "required" => true)); ?>
                            <!--                                    <span class="" data-toggle="modal" href="#commentDialog">-->
                            <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon " . $dltClassName) ?>
                            <!--                                    </span>-->
                            <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                            </td>
                            <?php } else { ?>
                            <td class="center comments">
                            <!-- title="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>" -->
                            <?php echo $timesheetForm['initialRows'][$i][$j]->renderError(); ?>
                            <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName . " " . 'timeBox', "required" => true, 'id' => 'timeBox', 'readonly' => true)); ?>
                            <!--                                    <span class="" data-toggle="modal" href="#commentDialog">-->
                            <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon " . $dltClassName) ?>
                            <!--                                    </span>-->
                            <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                            </td>
                        <?php }
                         } else { ?>
                        <td class="center comments">
                        <?php if ($currentWeekDates[$j] < $employeeJoinedDate) { ?>
                        <!-- title="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>" -->
                         <?php echo $timesheetForm['initialRows'][$i][$j]->renderError(); ?>
                         <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName . " " . 'timeBox', "required" => true, 'id' => 'timeBox', 'readonly' => true)); ?>
                         <!--                                    <span class="" data-toggle="modal" href="#commentDialog">-->
                        <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon " . $dltClassName) ?>
                        <!--                                    </span>-->
                        <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                         <?php } else { ?>
                        <!-- title="<?php echo $row['projectId'] . "##" . $row['activityId'] . "##" . $currentWeekDates[$j] . "##" . $row['timesheetItems'][$currentWeekDates[$j]]->getComment(); ?>" -->
                         <?php echo $timesheetForm['initialRows'][$i][$j]->renderError(); ?>
                        <?php echo $timesheetForm['initialRows'][$i][$j]->render(array("class" => $dltClassName . " " . 'timeBox', "required" => true)); ?>
                        <!--                                    <span class="" data-toggle="modal" href="#commentDialog">-->
                        <?php echo image_tag(theme_path('images/comment.png'), 'id=commentBtn_' . $j . '_' . $i . " class=commentIcon " . $dltClassName) ?>
                        <!--                                    </span>-->
                        <?php echo $timesheetForm['initialRows'][$i]['TimesheetItemId' . $j] ?>
                        <?php } ?>
                         </td>
                        <?php }
                     } ?>
                 </tr>
                        <?php
                        $i++;
                        $x++;
                    endforeach;
                    endif; ?>

                        <tr id="extraRows"></tr>

                </tbody>
            </table>
            </div> <!-- tableWrapper -->

            <div class="bottom">
                <p class="required">
                    <em>*</em><?php echo " " . __('Deleted project activities are not editable') ?>
                </p>
                <p style="float: right;">
                    <?php sfContext::getInstance()->getUser()->setFlash('employeeId', $employeeId); ?>
                    <input type="submit" class="" value="<?php echo __('Save') ?>" name="btnSave" id="submitSave"/>
                    <input type="button" class="" id="btnAddRow" value="<?php echo __('Add Row') ?>" name="btnAddRow">
                    <input type="button" class="delete" id="submitRemoveRows" value="<?php echo __('Remove Rows') ?>" name="btnRemoveRows">
                    <?php echo button_to(__('Reset'), 'time/editTimesheet?timesheetId=' . $timesheetId . '&employeeId=' . $employeeId . '&actionName=' . $backAction. '&timeSheetAction=' . $timesheetActon, array('class' => 'reset', 'id' => 'btnReset')) ?>
                    <?php  if ($timesheetItemValuesArray != null) { ?>
                    <?php echo button_to(__('Cancel'), 'time/' . $backAction . '?timesheetStartDate=' . $startDate . '&employeeId=' . $employeeId. '&timeSheetAction=' . $timesheetActon, array('class' => 'reset', 'id' => 'btnBack')) ?>
                    <?php } ?>
                </p>

            </div>
        </form>
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
                                 <td ><?php $timeService =new TimesheetService(); echo $timeService->convertDurationToHours($breakTimeTotal); ?></td>
                                 <td ><?php echo $timeService->convertDurationToHours($wrkHrsTotal); ?></td>
                                 <td ><?php echo $timeService->convertDurationToHours($actualWrkHrsTotal); ?></td>
                                 <td ><?php echo $timeService->convertDurationToHours($overTimeTotal); ?></td>
                                 <td></td>
                             </tr>
                         </tbody>
                     </table>
                 </div> <!-- inner -->
             </div> <!-- Box-miniList -->

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


<?php echo $listForm ?>
<!-- comment-Dialog -->
<div class="modal hide" id="commentDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Comment'); ?></h3>
    </div>
    <div class="modal-body">
        <form action="updateComment" method="post" id="frmCommentSave" name="frmCommentSave">
            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li class="line">
                        <label><?php echo __("Project Name") ?></label>
                        <label id="commentProjectName" class="line"></label>
                    </li>
                    <li class="line">
                        <label><?php echo __("Activity Name") ?></label>
                        <label id="commentActivityName" class="line"></label>
                    </li>
                    <li class="line">
                        <label><?php echo __("Date") ?></label>
                        <label id="commentDate" class="line"></label>
                    </li>
                    <li class="largeTextBox">
                        <textarea id="timeComment" name="timeComment"></textarea>
                    </li>
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <input type="button" id="commentSave" class="" value="<?php echo __('Save'); ?>" />
        <input type="button" id="commentCancel" class="reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- commentDialog -->

<script type="text/javascript">
    var timsheetAction = "<?php echo $timesheetActon; ?>";
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var rows = <?php echo $timesheetForm['initialRows']->count() + 1 ?>;
    var link = "<?php echo url_for('time/addRow') ?>";
    var commentlink = "<?php echo url_for('time/updateTimesheetItemComment') ?>";
    var projectsForAutoComplete=<?php echo $timesheetForm->getProjectListAsJson(); ?>;
    var projects = <?php echo $timesheetForm->getProjectListAsJsonForValidation(); ?>;
    var projectsArray = eval(projects);
    var getActivitiesLink = "<?php echo url_for('time/getRelatedActiviesForAutoCompleteAjax') ?>";
    var timesheetId="<?php echo $timesheetId; ?>"
    var lang_not_numeric = '<?php echo __('Should Be Less Than 24 and in HH:MM'); ?>';
    var lang_if_alpha ='<?php echo __('Please enter only numeric values in HH:MM'); ?>';
    var rows_are_duplicate = "<?php echo __('Duplicate Records Found'); ?>";
    var project_name_is_wrong = '<?php echo __('Select a Project and an Activity'); ?>';
    var please_select_an_activity = '<?php echo __('Select a Project and an Activity'); ?>';
    var select_a_row = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
    var employeeId = '<?php echo $employeeId; ?>';
    var linkToGetComment = "<?php echo url_for('time/getTimesheetItemComment') ?>";
    var linkToDeleteRow = "<?php echo url_for('time/deleteRows') ?>";
    var editAction = "<?php echo url_for('time/editTimesheet') ?>";
    var currentWeekDates = new Array();
    var startDate='<?php echo $startDate ?>';
    var backAction='<?php echo $backAction ?>';
    var endDate='<?php echo $endDate ?>';
    var erorrMessageForInvalidComment="<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)); ?>";
    var numberOfRows='<?php echo $i ?>';
    var incorrect_total="<?php echo __('Total Hours for the day must be less than 24!!'); ?>";
    var typeForHints='<?php echo __('Type for hints').'...'; ?>';
    var lang_selectProjectAndActivity='<?php echo __('Select a Project and an Activity'); ?>';
    var lang_enterExistingProject='<?php echo __("Select a Project and an Activity"); ?>';
    var lang_noRecords='<?php echo __('Select Records to Remove'); ?>';
    var lang_removeSuccess = '<?php echo __('Successfully Removed')?>';
    var lang_noChagesToDelete = '<?php echo __('No Changes to Delete');?>';
    var closeText = '<?php echo __('Close');?>';
    var linkForViewTimesheet="<?php echo url_for('time/'.$backAction) ?>";
	var lang_oldTimesheet_cannot_create = "<?php echo __("Timesheet can not be created prior joining date"); ?>";
    var actionName = "<?php echo $actionName; ?>";
    var dateList  = <?php echo json_encode($dateForm->getDateOptions()); ?>;
    <?php
    for ($i = 0; $i < count($currentWeekDates); $i++) {
        echo "currentWeekDates[$i]='" . $currentWeekDates[$i] . "';\n";
    }
    ?>
</script>
