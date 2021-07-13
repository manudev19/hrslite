<?php $breadcrumb[]['name'] = 'Expense Report';
$breadcrumb[]['link'] = null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>



<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php if($timesheetPermissions->canRead()){?> 
<div class="box" id="attendance-summary">
     <div class="head"><h1><?php echo __('Employee Expense Report'); ?></h1></div> 
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>

         <form action="<?php echo url_for("expense/accountsExpenseReport"); ?>" id="employeeSelectForm" 
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
                        <?php
                        echo $form['projectName']->renderLabel(__('Project Name')); 
                        echo $form['projectName']->render();
                        ?>
                       <span class="validation-error">
                            <?php if($validation!=null){ echo $validation; } ?> 
                        </span>
                    </li>

                     <li>
                        <?php echo $form['from_date']->renderLabel(__('From Date').' <em>*</em>'); ?>
                        <?php echo $form['from_date']->render(); ?>
                        <?php echo $form['from_date']->renderError(); ?>
                    </li> 
                    <li>
                        <?php echo $form['to_date']->renderLabel(__('To Date').' <em>*</em>'); ?>
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
                    <input type="button" id="searchBtn" 
                        onclick="searchBTN()" value="<?php echo __("Search") ?>" name="_search" />
                   
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />    
                  
                    <input type="button"  class="hideden" onclick="downloadBTN()" id="searchBtn" value="<?php echo __("Download") ?>"  />
                </p>
            </fieldset>
        </form> 
    </div>
</div>
  
<?php if (!($pendingApprovelTimesheets == null)): ?>
        <?php include_component('core', 'ohrmList'); ?>
 <?php endif; ?> 

<?php }?>
<script type="text/javascript">

    $('#from_date').change(function(){
        var please_fill_date_field = "<?php echo __('Please Enter a Valid Date'); ?>";
        element = $(this);
        var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
        if(!$(element).val().match(regEx)||$(element).val()==""){
            console.log('1',$(element).val());
            $(element).addClass('validation-error'); 
            displayMessages('warning', please_fill_date_field); 
        }
    });

     $('#to_date').change(function(){
        var please_fill_date_field = "<?php echo __('Please Enter a Valid Date'); ?>";
        element = $(this);
        var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
        if(!$(element).val().match(regEx)||$(element).val()==""){
            $(element).addClass('validation-error'); 
            displayMessages('warning', please_fill_date_field); 
        }
    });
    
    function displayMessages(messageType, message) {
        $('#msgDiv').remove();
        if (messageType != 'reset') {
            $divClass = 'message '+messageType;
            $msgDivContent = "<div id='msgDiv' class=' " + $divClass + ">" + message + "<a class='messageCloseButton' href='#'>" + "</div>";           
             $('#validation-error').append($msgDivContent);
        }
    }

    function _clearMessage() {
    $('#validation-error div[generated="true"]').remove();
    }

    function searchBTN() {
        document.getElementById('download').value ="";
        var fromDate=document.getElementById('from_date').value;
        var toDate=document.getElementById('to_date').value;     
        var getDate=new Date(fromDate);
        var getToDate=new Date(toDate);
        var regEx = /^\d{4}-\d{2}-\d{2}$/;
        // if(fromDate == '' || toDate == ''){
        //     document.getElementById("error").innerHTML 
        //         = "Date field cannot be empty";
        //     return false;
        // }
        if(!fromDate.match(regEx)|| !toDate.match(regEx)) {
            document.getElementById("error").innerHTML 
                = "Should be a valid date in yyyy-mm-dd format";
            return false;
        }

        if(getDate.getTime() > getToDate.getTime()){
            document.getElementById("error").innerHTML = "To date cannot be lesser than From date";
         return false;
        }
        
        document.getElementById('employeeSelectForm').submit();
    } 

    function submitPage(pageNo) {    
        document.employeeSelectForm.pageNo.value = pageNo;
        var frmdate=document.getElementById('from_date').value;
        var todate=document.getElementById('to_date').value;
        var getDate=new Date(frmdate);
        var getToDate=new Date(todate);
        if(getDate.getTime() > getToDate.getTime()){
            document.getElementById("error").innerHTML = "To date cannot be lesser than From date";
             return false;
       }
           document.getElementById('download').value ="";
           document.getElementById('employeeSelectForm').submit();
    } //if ended

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
            document.getElementById("error").innerHTML = "To date cannot be lesser than From date";
         return false;
        }
        document.getElementById('employeeSelectForm').submit();
    }
    
    $(document).ready(function() {
       
        $('#resetBtn').on('click',function(){

            var url = "<?php echo url_for('expense/accountsExpenseReport')?>";
            window.location = url;
        });

    });
 
 
</script>

  

