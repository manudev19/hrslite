<?php 
$breadcrumb[]['name'] = 'Attendance Record';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>  

<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php if($timesheetPermissions->canRead()){?> 
<div class="box" id="attendance-summary">
     <div class="head"><h1><?php echo __('Generate Attendance Report'); ?></h1></div> 
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>

         <form action="<?php echo url_for("attendance/displayDepartmentAttendanceRecords"); ?>" id="employeeSelectForm" 
              name="employeeSelectForm" method="post">
                  <?php echo $form->renderHiddenFields(); ?>
            <fieldset>
                <ol>
                    
                     <li>
                        <?php echo $form['sub_unit']->renderLabel(__('Department Name'). ' <em>*</em>');?> 
                        <?php echo $form['sub_unit']->render(); ?>

                        <!-- <span class="validation-error">
                            <?php //if($validation!=null){ echo $validation; } ?> 
                        </span> -->
                     </li>

                    <li>
                        <?php echo $form['month']->renderLabel(__('Month*')); ?>
                        <?php echo $form['month']->render(); ?>
                    </li>

                    <li>
                        <?php echo $form['year']->renderLabel(__('Year*')); ?>
                        <?php echo $form['year']->render(); ?>
                    </li>


                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                
                <p>
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <input type="hidden" name="download" id="download" value="" />
                    <input type="hidden" name="weekdays" id="weekdays" value="" />
                   
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />    
                  
                    <input type="button"  onclick="downloadBTN()" id="searchBtn" value="<?php echo __("Download") ?>"  />
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
<script type="text/javascript">

    function searchBTN() {
        document.getElementById('download').value ="";
        document.getElementById('employeeSelectForm').submit();
    } 

    function submitPage(pageNo) {    
        document.employeeSelectForm.pageNo.value = pageNo;

        document.getElementById('download').value ="";
           document.getElementById('employeeSelectForm').submit();
    } //if ended

function downloadBTN(){  
        
       document.getElementById('download').value ="download"; 

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

         document.getElementById('employeeSelectForm').submit();

    }
    
    $(document).ready(function() {
       
        $('#resetBtn').on('click',function(){

            $("#sub_unit").val('0');
            /*$('#submitted').attr('checked', false);
            $('#Approved').attr('checked', false);
            
            var d = new Date();
            var curr_month = d.getMonth()+1;
            var curr_year = d.getFullYear();
            var lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0);
            $('#from_date').val(curr_year+"-"+curr_month+"-01");
            $('#to_date').val(curr_year+"-"+curr_month+"-"+lastDay.getDate());*/
            var CurrentDate = new Date();
            $("#month").val(CurrentDate.getMonth());
            $("#year").val(CurrentDate.getYear());
            $('#status_NOT_SUBMITTED').attr('checked', true);        
        });

    });
 
 
</script>

  

