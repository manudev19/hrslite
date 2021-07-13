
<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/viewMyAttendanceRecordSuccess')); ?>

<div class="box">
    <div class="head">
        <h1><?php echo __('My Attendance Records'); ?></h1>
    </div>
    <div class="inner">
        <div id="validationMsg">
            <?php echo isset($messageData) ? templateMessage($messageData) : ''; ?>
        </div>
        <form action="<?php echo url_for("attendance/viewMyAttendanceRecord"); ?>" id="reportForm" method="post">
            <fieldset>
                <ol class="normal">
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
                        <?php echo $form->renderHiddenFields(); ?>
                    </li>
                </ol>
            </fieldset>
        </form>
    </div>
</div>

<div id="recordsTable1"><!-- To appear table when search success --></div>

<script type="text/javascript">
var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>';
var employeeId='<?php echo $employeeId; ?>';
var actionRecorder='<?php echo $actionRecorder; ?>';
var dateSelected='<?php echo $date; ?>';
var trigger='<?php echo $trigger; ?>';
</script>

<style type="text/css">
.normal {
    display: inline;
}
.attendance-btn {
    width: 200px;
}

</style>