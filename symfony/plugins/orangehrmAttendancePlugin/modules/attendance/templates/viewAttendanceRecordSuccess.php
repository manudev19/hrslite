<?php 
$breadcrumb[]['name'] = 'Employee Records';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/viewAttendanceRecordSuccess')); ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/getRelatedAttendanceRecordsSuccess')); ?>

<?php if($attendancePermissions->canRead()){?>
<div class="box">

    <div class="head">
        <h1><?php echo __('View Attendance Record'); ?></h1>
    </div>
    
    <div class="inner">
    <?php include_partial('global/flash_messages', array('prefix' => 'employeeReport')); ?>
        <div id="validationMsg">
            <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
        </div>
             
        <form action="<?php echo url_for("attendance/viewAttendanceRecord"); ?>" id="reportForm" method="post" class="spinner_form" name="frmAttendanceReport">
            <fieldset>
                <ol>
                    <li>
                        <?php
                        echo $form['employeeName']->renderLabel(__('Employee Name/Id')); 
                        echo $form['employeeName'];
                        ?>
                    </li>
                    <li>
                        <?php echo $form['month']->renderLabel(__('Month')); ?>
                        <select id="month" name="month">
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>

                    </li>
                    <li>
                        <?php echo $form['year']->renderLabel(__('Year')); ?>
                        <select id="year" name="year">
                        <?php 
                            $attendanceStartYear   = "2015";
                            $attendanceCurrentYear = date("Y");
                            for ($i=$attendanceStartYear; $i <= $attendanceCurrentYear; $i++) {
                                echo "<option value=".$i.">".$i."</option>";
                            }                       
                        ?>
                        </select>
                    </li>
                    <?php echo $form->renderHiddenFields(); ?>                

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p class="formbuttons">
                    <input type="button" class="searchbutton" name="btView" id="btView" value="<?php echo __('View') ?>" />
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
                    <?php 
                    $downloadActionButtons = $form->getDownloadActionButtons();
                    foreach ($downloadActionButtons as $id => $button) {
                        echo $button->render($id), "\n";
                    }
                    ?>
                </p>
            </fieldset> 
        </form>
    </div>
</div>

<div id="recordsTable">
    <div id="msg" ><?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?></div>
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<?php include_partial('core/spinner_common_file'); ?>

<div id="punchInOut">

</div>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="dialogBox">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="okBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->
<?php }?>

<script type="text/javascript">
    
    // var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    // var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    // var errorForInvalidFormat='<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    // var errorMsge;
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>'
    var linkForProxyPunchInOut='<?php echo url_for('attendance/proxyPunchInPunchOut'); ?>'
    var trigger='<?php echo $trigger; ?>';
    var employeeAll='<?php echo __('All'); ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var dateSelected='<?php echo $date; ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>';
    var employeeSelect = '<?php echo __('Select an Employee') ?>';
    var invalidEmpName = '<?php echo __('Invalid Employee Name') ?>';
    var noEmployees = '<?php echo __('No Employees Available') ?>';
    var typeForHints = '<?php echo __("Type for hints") . '...'; ?>';
    var month='<?php echo $month; ?>';
    var year='<?php echo $year; ?>';
    // var linkToEdit='<?php echo url_for('attendance/editAttendanceRecord'); ?>'
    // var linkToDeleteRecords='<?php echo url_for('attendance/deleteAttendanceRecords'); ?>'
    // var lang_noRowsSelected='<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
    // var closeText = '<?php echo __('Close');?>';
    // var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';

    function submitPage(pageNo) {
        document.frmAttendanceReport.pageNo.value = pageNo;
        document.frmAttendanceReport.hdnAction.value = 'paging';
        document.getElementById('reportForm').submit();
    }
</script>