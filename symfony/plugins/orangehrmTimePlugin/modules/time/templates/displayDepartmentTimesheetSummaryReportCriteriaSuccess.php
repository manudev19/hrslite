<?php 
$breadcrumb[]['name'] = 'Timesheet Summary';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?> 
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php if($timesheetPermissions->canRead()){?> 
<div class="box" id="attendance-summary">
     <div class="head"><h1><?php echo __('Department timesheet report'); ?></h1></div> 
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>

         <form action="<?php echo url_for("time/displayDepartmentTimesheetSummaryReportCriteria"); ?>" class="spinner_form" id="employeeSelectForm" 
              name="employeeSelectForm" method="post">
                  <?php echo $form->renderHiddenFields(); ?>
            <fieldset>
                <ol>

                    <li>
                        <?php
                        echo $form['employeeName']->renderLabel(__('Employee Name/Id')); 
                        echo $form['employeeName'];
                        ?>
                    </li>
                    
                     <li>
                        <?php echo $form['sub_unit']->renderLabel(__('Department Name'));?> 
                        <?php echo $form['sub_unit']->render(); ?>

                        <span class="validation-error">
                            <?php if($validation!=null){ echo $validation; } ?> 
                        </span>
                     </li>

                     <li>
                        <?php echo $form['from_date']->renderLabel(__('From').' <em>*</em>'); ?>
                        <?php echo $form['from_date']->render(); ?>
                        <?php echo $form['from_date']->renderError(); ?>
                    </li> 
                    <li>
                        <?php echo $form['to_date']->renderLabel(__('To').' <em>*</em>'); ?>
                        <?php echo $form['to_date']->render(); ?>
                        <?php echo $form['to_date']->renderError(); ?>
                        <span id="error" class="validation-error">
                            <?php if($validationFordate!=null) { echo $validationFordate; } ?> 
                        </span>
                    </li> 


                    <li class="radio">
                        <?php echo $form['status']->renderLabel(__('Status*')); ?>
                        <?php echo $form['status']->render(); ?>
                         <span class="validation-error">
                            <?php if($validation!=null){ echo $validation; } ?> 
                        </span>
                    </li>

                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                
                <p>
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <input type="hidden" name="download" id="download" value="" />
                    <input type="hidden" name="weekdays" id="weekdays" value="" />
                    <input type="button" id="searchBtn" class="searchbutton"
                        onclick="searchBTN()" value="<?php echo __("Search") ?>" name="_search" />
                   
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />    
                  
                    <input type="button" onclick="downloadBTN()" class="btnDownload downloadBtn" id="searchBtn" value="<?php echo __("Download") ?>"  />
                </p>
            </fieldset>
          <!-- viewDepartmentTimesheet -->
        </form> 
    </div>
</div>
  
<?php if (!($pendingApprovelTimesheets == null)): ?>
        <?php include_component('core', 'ohrmList'); ?>
 <?php endif; ?> 

<?php }?>

<?php include_partial('core/spinner_common_file'); ?>

<script type="text/javascript">
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';

   /* var selectedEmployee = document.getElementById('selected_employee').value;
    var selectedEmployeeName = document.getElementById('selected_employee_name').value;
        //alert(selectedEmployee);
        if (selectedEmployee != undefined && selectedEmployee != '') {
            //alert("Condition acheived");
            $('#employeeSelectForm').append('<input type="hidden" name="employeeName[empId]" id="employeeName_empId" value="'+selectedEmployee+'">');
            $('#employeeSelectForm').append('<input class="formInputText ac_input" type="text" name="employeeName[empName]" value="'+selectedEmployeeName+'" id="employeeName_empName" autocomplete="off">');
            $('#employeeName_empName').hide();
            $('#employeeName_empName').prev().hide();
        }*/

    function searchBTN() {
        document.getElementById('download').value ="";
        var fromDate=document.getElementById('from_date').value;
        var toDate=document.getElementById('to_date').value;     
        var getDate=new Date(fromDate);
        var getToDate=new Date(toDate);
        var regEx = /^\d{4}-\d{2}-\d{2}$/;
        if(!fromDate.match(regEx)|| !toDate.match(regEx)) {
            document.getElementById("error").innerHTML 
                = "Should be a valid date in yyyy-mm-dd format";
            return false;
        }

        if(getDate.getTime() > getToDate.getTime()){
            document.getElementById("error").innerHTML = "To date should be after from date";
         return false;
        }
        
        /*<input type="hidden" name="employeeName[empId]" id="employeeName_empId" value="">
        <input class="formInputText ac_input" type="text" name="employeeName[empName]" value="" id="employeeName_empName" autocomplete="off">*/
        
        document.getElementById('employeeSelectForm').submit();
    } 

    function submitPage(pageNo) {    
        document.employeeSelectForm.pageNo.value = pageNo;
        var frmdate=document.getElementById('from_date').value;
        var todate=document.getElementById('to_date').value;
        var getDate=new Date(frmdate);
        var getToDate=new Date(todate);
        if(getDate.getTime() > getToDate.getTime()){
            document.getElementById("error").innerHTML = "To date should be after from date";
             return false;
       }
           document.getElementById('download').value ="";
           document.getElementById('employeeSelectForm').submit();
    } //if ended


    /*function searchBTN() {
        document.getElementById('download').value ="";
        document.getElementById('employeeSelectForm').submit();
    } 



    function submitPage(pageNo) {    
        document.employeeSelectForm.pageNo.value = pageNo;

        document.getElementById('download').value ="";
           document.getElementById('employeeSelectForm').submit();
    } //if ended*/

function downloadBTN(){
        
       document.getElementById('download').value ="download"; 

       var fromDate=document.getElementById('from_date').value;
        var toDate=document.getElementById('to_date').value;      
        var getDate=new Date(fromDate);
        var getToDate=new Date(toDate);
        var regEx = /^\d{4}-\d{2}-\d{2}$/;
        if(!fromDate.match(regEx)|| !toDate.match(regEx)) {
            document.getElementById("error").innerHTML 
                = "Should be a valid date in yyyy-mm-dd format";
            return false;
        }

        if(getDate.getTime() > getToDate.getTime()){
            document.getElementById("error").innerHTML = "To date should be after from date";
         return false;
        }
        document.getElementById('employeeSelectForm').submit();
      /* var frmdate=document.getElementById('from_date').value;
       var todate=document.getElementById('to_date').value;
       var getDate=new Date(frmdate);
       var getToDate=new Date(todate);

       var between=  Array(Math.round((getToDate- getDate) / 86400000) + 1).fill().map((_, idx) => (new Date(getDate.getTime()+ idx * 86400000)));
         /*alert (between);

         var weekDays= new Array();
         for(var i=6; i< between.length; i+=7){
                //alert(between[i]);
                var mn=('0' + (between[i].getMonth() + 1)).slice(-2);
                var dt=('0' + between[i].getDate()).slice(-2);
                var dates=between[i].getFullYear()+"-"+mn+"-"+dt;
                
                weekDays.push(dates);
            }
            var myJSON = JSON.stringify(weekDays);
            var weekdy=document.getElementById('weekdays');
            weekdy.setAttribute('value',myJSON);*/
    }
    
    $(document).ready(function() {
       
        $('#resetBtn').on('click',function(){

            var url = "<?php echo url_for('time/displayDepartmentTimesheetSummaryReportCriteria')?>";
            window.location = url;
        });

    });
 
 
</script>

  

