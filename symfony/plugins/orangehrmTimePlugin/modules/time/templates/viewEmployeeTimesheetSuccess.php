<?php 
$breadcrumb[]['name'] = 'Employee Timesheets';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>

<?php use_javascript(plugin_web_path('orangehrmTimePlugin', 'js/viewEmployeeTimesheet')); ?>
<?php if($timesheetPermissions->canRead()){?>
<div class="box">
<div class="head">
        <h1><?php echo __("Timesheet Pending Action - Search By Employee"); ?></h1>
        </div>
    <div class="inner">
    	 <?php include_partial('global/flash_messages', array('prefix' => 'emptimesheet')); ?>
        <form action="<?php echo url_for("time/viewEmployeeTimesheet"); ?>" id="employeeSelectForm" 
        class="spinner_form" name="employeeSelectForm" method="post">
                  <?php echo $form->renderHiddenFields(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['employeeName']->renderLabel(__('Employee Name/Id') ); ?>
                        <?php echo $form['employeeName']->render(); ?>
                        <?php echo $form['employeeName']->renderError(); ?>

                    </li>
                     <li>
                        
                        <?php echo $form['location']->renderLabel(__('Location')); ?>
                        <?php echo $form['location']->render(); ?>
                    </li>  
                </ol>
                <p>
                    <input type="button" class="searchbutton" id="btnSearch" value="<?php echo __('Search') ?>" />
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />  
                </p>
            </fieldset>
        </form>
    </div>
</div>

<!-- Employee-pending-submited-timesheets -->
<?php if (!($pendingApprovelTimesheets == null)): ?>
    <div class="box noHeader">
            <div class="inner ">
            <?php if ($pager->haveToPaginate()):?>
            <div class="top">
                <?php include_partial('core/report_paging', array('pager' => $pager));?>                
            </div>
            <?php endif; ?> 
            <form action="<?php echo url_for("time/viewPendingApprovelTimesheet"); ?>" id="viewTimesheetForm" method="post" >        
                <table class="table">
                    <thead>
                        <tr>
                            <tr>
                            <th id="tablehead" style="width:4%"><?php echo __('SI.No'); ?></th>
                            <th id="tablehead" style="width:8%"><?php echo __('Employee Id'); ?></th>
                            <th id="tablehead" style="width:15%"><?php echo __('Employee Name'); ?></th>
                            <th id="tablehead" style="width:15%"><?php echo __('Timesheet Period'); ?></th>
                            <th id="tablehead" style="width:15%"><?php echo __('Department'); ?></th>
                            <th id="tablehead" style="width:10%"><?php echo __('Location'); ?></th>
                            <th style="width:6%"></th>
                        </tr>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($sf_data->getRaw('pendingApprovelTimesheets') as $pendingApprovelTimesheet):
                            ?>
                            <tr class="<?php echo ($i & 1) ? 'even' : 'odd'; ?>">
                        <input type="hidden" name="timesheetId" value="<?php echo $pendingApprovelTimesheet['timesheetId']; ?>" />
                        <input type="hidden" name="employeeId" value="<?php echo $pendingApprovelTimesheet['employeeId']; ?>" />
                        <input type="hidden" name="startDate" value="<?php echo $pendingApprovelTimesheet['timesheetStartday']; ?>" />
                        <td>
                              <?php echo $index; ?>
                        </td>
                        <td>
                            <?php echo $pendingApprovelTimesheet['emp_id'];?>
                        </td>
                        <td>
                            <?php echo $pendingApprovelTimesheet['employeeFirstName'] . " " . $pendingApprovelTimesheet['employeeLastName']; ?>
                        </td>
                        <td>
                            <?php echo set_datepicker_date_format($pendingApprovelTimesheet['timesheetStartday']) . " " . __("to") . " " . set_datepicker_date_format($pendingApprovelTimesheet['timesheetEndDate']) ?>
                        </td>
                        <td>
                            <?php $departmentName = $pendingApprovelTimesheet['department'];?>
                            <?php if($departmentName == 'Accounts & Finance' ||$departmentName == 'HR'||$departmentName == 'Sales & Marketing'||$departmentName == 'HR & Marketing Support' ){
                                echo $pendingApprovelTimesheet['department'] . ' (Head Office)';
                                    }
                                    else if($pendingApprovelTimesheet['department'] == 'Finance'){
                                    echo $pendingApprovelTimesheet['department'] .' (Accounts)';
                                    }else{
                                    echo $pendingApprovelTimesheet['department'];
                                    }
                            ?>   
                        </td>
                        <td>
                            <?php echo $pendingApprovelTimesheet['empLocation']; ?>
                        </td>
                        <td align="center" class="<?php echo $pendingApprovelTimesheet['timesheetId'] . "##" . $pendingApprovelTimesheet['employeeId'] . "##" . $pendingApprovelTimesheet['timesheetStartday'] ?>">
                            <a href="<?php
                    echo 'viewPendingApprovelTimesheet?timesheetId=' .
                    $pendingApprovelTimesheet['timesheetId'] . '&employeeId=' .
                    $pendingApprovelTimesheet['employeeId'] . '&timesheetStartday=' .
                    $pendingApprovelTimesheet['timesheetStartday'] . '&timeSheetAction=viewEmployeeTimesheet';
                            ?>" id="viewSubmitted">
                                <?php echo __("View"); ?>
                            </a>
                        </td>
                        </tr>                        
                        <?php
                        $i++; $index++;
                    endforeach;
                    ?>
                    </tbody>
                </table>
                <?php if ($pager->haveToPaginate()) : ?>
                    <div class="bottom">
                        <?php include_partial('core/report_paging', array('pager' => $pager)); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php endif; ?>
<?php }?>

<?php include_partial('core/spinner_common_file'); ?>

<script type="text/javascript">
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING); ?>';
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeesArray = eval(employees);
    var errorMsge;
    var lang_typeForHints = '<?php echo __("Type for hints") . '...'; ?>';
    
    function submitPage(pageNo) {
        var actionUrl = $('#employeeSelectForm').attr('action') + '?pageNo=' + pageNo;
        $('#employeeSelectForm').attr('action', actionUrl).submit();
    }
    
    $(document).ready(function() {
        $('#viewSubmitted').click(function() {
            var data = $(this).parent().attr("class").split("##");
            var url = 'viewPendingApprovelTimesheet?timesheetId='+data[0]+'&employeeId='+data[1]+'&timesheetStartday='+data[2];
            $(location).attr('href',url);
        });    
        
        $('#btnSearch').click(function() {
            $('#employeeSelectForm').submit();
        });
        $('#resetBtn').click(function() {
            var url = 'viewEmployeeTimesheet';
            $(location).attr('href',url);
        });
        
        $.validator.addMethod("validEmployeeName", function(value, element) {                

            return autoFill('employee', 'time_employeeId', employees);                 
        });
    });
    
    function autoFill(selector, filler, data) {
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
        return valid;
    }
    
</script>

