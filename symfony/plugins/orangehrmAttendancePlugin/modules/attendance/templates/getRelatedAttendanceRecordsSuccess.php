<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/getMyRelatedAttendanceRecordsSuccess')); ?>

    
<div class="box miniList noHeader" id="recordsTable">
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <?php echo isset($listForm) ? $listForm->render() : ''; ?>
        <form action="" id="employeeRecordsForm" method="post">
            <div class="top">
                <?php if ($allowedActions['Edit']) : ?>
                    <input type="button" class="edit" id="btnEdit" value="<?php echo __('Edit'); ?>" />
                <?php endif; ?>
                <?php if ($allowedActions['Delete']) : ?>
                    <input type="button" class="delete" id="btnDelete" value="<?php echo __('Delete'); ?>" />
                <?php endif; ?>
                <?php if ($allowedActions['PunchIn']) : ?>
                    <input type="button" class="punch" id="btnPunchIn" value="<?php echo __('Add Attendance Records'); ?>" />
                <?php endif; ?>
                <?php if ($allowedActions['PunchOut']) : ?>
                    <input type="button" class="punch" id="btnPunchOut" value="<?php echo __('Add Attendance Records'); ?>" />
                <?php endif; ?>
            </div>
            <table class="table">
                <thead id="tableHead" >
                    <tr>
                        <th style="width: 10%;"><?php echo __("Date"); ?></th>
                        <th style="width: 10%;"><?php echo __("Shift"); ?></th>
                        <th style="width: 15%;"><?php echo __("In Time"); ?></th>
                        <th style="width: 15%;"><?php echo __("Out Time"); ?></th>
                        <th style="width: 10%;"><?php echo __("Working Hours"); ?></th>
                        <th style="width: 10%;"><?php echo __("Over Time"); ?></th>
                        <th style="width: 10%;"><?php echo __("Break Time"); ?></th>
                        <th style="width: 10%;"><?php echo __("Actual Working Hours"); ?></th>
                        <th style="width: 10%;"><?php echo __("Status"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $class = 'odd'; ?>
                    <?php $i = 0; ?>
                    <?php $total = 0; ?>
                    <?php if ($records == null): ?>  
                        <tr>
                            <td id="noRecordsColumn" colspan="6">
                                <?php echo __("No attendance records to display") ?>
                            </td>
                        </tr> 
                    <?php else: ?>                
                        <?php foreach ($records as $record): ?>
                            <tr class="<?php echo $class; ?>">
                                <?php $class = $class == 'odd' ? 'even' : 'odd'; ?>
                                <?php $inUserTimeArray = explode(" ", $record->getPunchInUserTime()) ?>
                                <td>
                                    <?php echo $record->getLoginDate(); ?>
                                </td>
                                <td>
                                    <?php echo $record->getShift(); ?>
                                </td>
                                <td>
                                    <span style="color:#98a09f"><?php echo $record->getPunchInUserTime() ?></span>
                                </td>
                                <td>
                                    <span style="color:#98a09f"><?php echo $record->getPunchOutUserTime() ?></span>
                                </td>
                                <td>
                                    <?php echo $record->getWorkingHours() ?>
                                </td>
                                <td>
                                    <?php echo $record->getOverTime() ?>
                                </td>
                                <td>
                                    <?php echo $record->getBreakTime() ?>
                                </td>
                                <td>
                                    <?php echo $record->getActualWorkingHours() ?>
                                </td>
                                <td>
                                    <?php echo $record->getStatus(); ?>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<!-- Delete-confirmation -->
<div class="modal hide" id="dialogBox">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
            </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
            </div>
    <div class="modal-footer">
        <input type="button" class="btn" id="dialogOk" data-dismiss="modal" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
        </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
    var employeeId='<?php echo $employeeId; ?>';
    var date='<?php echo $date; ?>';
    var linkToEdit='<?php echo url_for('attendance/editAttendanceRecord'); ?>'
    var linkToDeleteRecords='<?php echo url_for('attendance/deleteAttendanceRecords'); ?>'
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>'
    var actionRecorder='<?php echo $actionRecorder; ?>';
    var lang_noRowsSelected='<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
</script>