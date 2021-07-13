<?php $breadcrumb[]['name'] = 'Audit Log';
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>

<?php use_javascript('orangehrm.datepicker.js'); ?>

<div class="box" >
<div class="personalDetails" id="attendance-summary" >
     <div class="head"><h1><?php echo __('Audit Report'); ?></h1></div> 
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
            <form action="" id="auditLogForm" 
            class="spinner_form" name="auditLogForm" method="post">
            <fieldset>
                <ol>
                    <li>
                        <?php
                        echo $form['modules']->renderLabel(__('Module Name')); 
                        echo $form['modules']->render();
                        ?>
                    </li>
                    <li >
                        <?php
                        echo $form['sections']->renderLabel(__('Sections')); 
                        echo $form['sections']->render();
                        ?>
                    </li>
                    <li>
                       <?php 
                        echo $form['from_date']->renderLabel(__('From'.'<em>*</em>')); 
                        echo $form['from_date']->render();
                        ?>  
                    </li> 
                    <li>
                       <?php 
                        echo $form['to_date']->renderLabel(__('To'.'<em>*</em>')); 
                        echo $form['to_date']->render(); 
                        ?>
                    </li> 
                    <li>
                        <?php
                        echo $form['actions']->renderLabel(__('Actions')); 
                        echo $form['actions']->render();
                        ?>
                    </li>
                    <li>
                        <?php
                         echo $form['action_owner']->renderLabel(__('Action User')); 
                         echo $form['action_owner']->render();
                        ?>
                    </li>
                    <li >
                        <?php
                         echo $form['affected_employee']->renderLabel(__('Affected Employee')); 
                         echo $form['affected_employee']->render();
                        ?>
                    </li>
                </ol>
                <ol>
                    <li class="required">
                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                   </li>
                </ol>
              
                <p>
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <!-- <input type="hidden" name="download" id="download"  value="<?php echo $_POST['Download'] ?>" /> -->
                    <input type="button" id="searchBtn" class="searchbutton" value="<?php echo __("Search") ?>" name="_search" />
                   
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />    
                    <input type="button" name="download" class="downloadBtn" id="downloadBtn" value="<?php echo _('Download') ;?>" />    
                </p>
            </fieldset>
        </form> 
    </div>
</div></div>
<?php if (!($actionLogRecords == null)): ?>
<div class="box miniList noHeader">
<div class="inner">
<?php if ($pager->haveToPaginate()):?>
            <div class="top">
                <?php include_partial('core/report_paging', array('pager' => $pager));?>                
            </div>
            <?php endif; ?>    
<table cellpadding="0" cellspacing="0" width="100%" class="table hover" style="overflow:auto">
     <thead>
            <tr>
                <th  width="8%" style="text-align:center"><?php echo __('Date and Time'); ?></th>
                <th width="4%" style="text-align:center"><?php echo __('User Employee Id'); ?></th>
                <th width="8%" style="text-align:center"><?php echo __('User'); ?></th>
                <th width="10%" style="text-align:center"><?php echo __('Entity Id'); ?></th>
                <th width="10%" style="text-align:center"><?php echo __('Affected Entity'); ?></th>
                <th  width="5%" style="text-align:center"><?php echo __('Action Name'); ?></th>
                <th  width="2%" style="text-align:center"><?php echo __('Table Name'); ?></th>
                <th width="10%" style="text-align:center" ><?php echo __('Old Value(s)'); ?></th>
                <th width="10%" style="text-align:center;"><?php echo __('Updated Value(s)'); ?></th>
                <th width="6%" style="text-align:center"><?php echo __('Compare'); ?></th>
            </tr>
     </thead>
<tbody>
      <?php if(count($actionLogRecords)>0){?>
       <?php  $i = 0;$value=['Action is INSERT','Not Updated','Action is DELETE'];

               foreach ($actionLogRecords as $row):
             
                 $date=date_create($row['action_timestamp']);?>
                  <tr class="<?php echo ($i & 1) ? 'even' : 'odd'; ?>">
                  <td id="action_timestamp"><?php echo date_format($date,"M j, Y, g:i a");?></td>
                  <td id="action_owner"><?php echo $row['action_owner_id']; ?></td>
                  <td id="action_owner_name"><?php echo $row['action_owner_name']; ?></td>
                  <td id="action_entity_id"><?php echo $row['entity_id']; ?></td>
                  <td id="action_entity_name"><?php echo $row['screen_name']; ?></td>
                  <td id="action"><?php echo $row['action']; ?></td>
                  <td id="action_table_name"><?php echo $row['action_table_name']; ?></td>
                   
                  <td id="action_old_data"><?php  if(!in_array($row['old_data'],$value)){
                    foreach ($row['old_data'] as $r):
                     $res1[]=$r; endforeach;
                     echo implode($res1,', ');
                     $res1=array();
                    } 
                     else{
                         echo $row['old_data'];
                    }?></td>

                  <td id="action_new_data"> <?php  if(!in_array($row['updated_data'],$value)){
                    foreach ($row['updated_data'] as $w):
                     $res2[]=$w; endforeach;
                     echo implode($res2,', ');
                     $res2=array();
                    } 
                     else{
                         echo $row['updated_data'];
                    }?></td>
                  <td class="compare"> <a href="#" onClick="ShowModal(this)" data-id="<?php echo $row['audit_id'];?>"><?php echo 'compare'; ?></td></a>
                </tr>
        <?php
        ++$i;
        endforeach;
            ?>
            <?php }else{?>
            <tr><td><?php echo __('No Records Found');?></td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <?php }?>
         </tbody>
    </table>
    <?php if ($pager->haveToPaginate()):?>
            <div class="bottom">
                <?php include_partial('core/report_paging', array('pager' => $pager));?>                
            </div>
            <?php endif; ?>    
</div>
</div>
<?php endif; ?> 
 
<?php include_partial('core/spinner_common_file'); ?>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="preview" >
  <div class="modal-header">
  
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('Audit Log Data'); ?></h3>
  </div>
  <div class="modal-body">
      <span><?php echo __('');?></span>
      <div id="employee_list">  
      </div>
  </div>
  <div class="modal-footer">
   <input type="button" class="btn" data-dismiss="modal" id="dialogBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
     var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
     var lang_validEmployee = '<?php echo __(ValidationMessages::INVALID); ?>';
     var getAuditDataUrl= '<?php echo url_for('pim/getAuditDataAjax');?>';
     var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
     var lang_old_data_delete  = '<?php echo __("Action Old Data Deleted ") ?>';
     var lang_new_data_insert  = '<?php echo __("Action New Data Inserted") ?>';
     var lang_old_data  = '<?php echo __("Action Old Data") ?>';
     var lang_updated_data  = '<?php echo __("Action Updated Data ") ?>';
     var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
     var lang_dateError = '<?php echo __("To date should be after from date") ?>';
     var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
     var lang_Daterequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	  
function submitPage(pageNo) {
        var actionUrl = $('#employeeSelectForm').attr('action') + '?pageNo=' + pageNo;
        $('#employeeSelectForm').attr('action', actionUrl).submit(); 
    }

    $('#downloadBtn').click(function() {
    $('#isDownloadable').val("Download"); 
        $('#auditLogForm').submit();
        $('#isDownloadable').val('');
    });
   
    $("#action_owner_empName").autocomplete(employees, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }  
            ,matchContains:true
        }).result(function(event, item) {
            $("#action_owner_empName").valid();
        });

        $("#affected_employee").autocomplete(employees, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }  
            ,matchContains:true
        }).result(function(event, item) {
            $("#affected_employee").valid();
        });

function employeeAutoFill(selector, filler, data) {
    var valid = true;
    if($("#" + selector).val()!='Type for hints...' && $("#" + selector).val()!=''){
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
    }
        return valid;
    }

function ownerAutoFill(selector, filler, data) {
    var valid = true;
    if($("#" + selector).val()!='Type for hints...' && $("#" + selector).val()!=''){
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
    }
        return valid;
    }

    $.validator.addMethod("validEmployeeName", function(value, element) { 
        return employeeAutoFill('affected_employee', 'affected_employee_empId', employees);  
    });

    $.validator.addMethod("validOwnerName", function(value, element) {   
        return ownerAutoFill('action_owner_empName', 'action_owner_empId', employees);  
    });

    $("#auditLogForm").validate({
            rules: {
                'action_owner[empName]':{
                    validOwnerName: true,
                    onkeyup: false
                },
                'from_date' : {
                    required:true,
                    valid_date: function() {
                        return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                    }},
                'to_date' : {
                    required:true,
                    valid_date: function() {
                        return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat
                    }
                    },
                    date_range: function() {
                        return {
                        format:datepickerDateFormat,
                        displayFormat:displayDateFormat,
                        fromDate:$('#from_date').val()
                    }
                    }
                },
                 
                'affected_employee[empName]':{
                    validEmployeeName: true,
                    onkeyup: false
                },

            },
            messages: {
                'action_owner[empName]':{
                  validOwnerName: lang_validEmployee
              },
                'from_date' : {
                    required: lang_Daterequired,
                    valid_date: lang_validDateMsg
                },
                'to_date' : {
                    required: lang_Daterequired,
                    valid_date: lang_validDateMsg,
                    date_range:lang_dateError
                },
               
              'affected_employee[empName]':{
                  validEmployeeName: lang_validEmployee
              }

            }

    });
       
   
  $('#searchBtn').click(function(){ 
      $('#auditLogForm').submit();
  });

function ShowModal(elem){
    var dataId = $(elem).data("id");
    fetchEmployees(dataId);
    $('#preview').modal();
}

function submitPage(pageNo) {    
        document.auditLogForm.pageNo.value = pageNo;
        document.getElementById('auditLogForm').submit();
    }

$(document).ready(function() {
        $('#resetBtn').on('click',function(){
          //  $('#auditLogForm').trigger('reset');
            var url = "<?php echo url_for('pim/auditLog')?>";
             window.location = url;
        });
     
});
   
function fetchEmployees(dataId) {
        var params = '';
        $('div#employee_list').html(''); 
    
        params ='id=' + dataId;
        $.ajax({
            type: 'GET',
            url: getAuditDataUrl,
            data: params,
            dataType: 'json',
            success: function(results) {  
               
                var oldData = results.old;
                var newData = results.new;
                var action =results.action;
                var html = '';            
              
                var rows = $('div#employee_list table tr').length - 1;
                 if (action=='INSERT') {
                    var newCount=newData.length;
                    $('div#employee_list').html("<table class='table'><tr><th>"+lang_new_data_insert+
                     "</th></tr></table>"); 
                    for (var i = 0; i < newCount; i++) {
                        rows++;
                        var css = rows % 2 ? "even" : "odd";       
                     html = html + '<tr class="' + css + '"><td>'+$("<div/>").text(JSON.parse(newData[i])).html()+'</td></tr>';
                    }
                    $('div#employee_list table.table').append(html);

                }else if(action=='DELETE') {
                        var oldCount=oldData.length;
                       $('div#employee_list').html("<table class='table'><tr><th>"+lang_old_data_delete+
                        "</th></tr></table>");

                        for (var i = 0; i < oldCount; i++) {
                        rows++;  
                        var css = rows % 2 ? "even" : "odd";       
                        html = html + '<tr class="' + css + '"><td>'+$("<div/>").text(JSON.parse(oldData[i])).html()+'</td></tr>';
                       
                        }
                        $('div#employee_list table.table').append(html);
                    } else{
                        var oldCount=oldData.length;
                         $('div#employee_list').html("<table class='table'><tr><th>"+lang_old_data+
                           "</th><th>"+lang_updated_data+"</th></tr></table>");                    
                    
                           var html = '';            
                    
                           for (var i = 0; i < oldCount; i++) {  
                               rows++;              
                           var css = rows % 2 ? "even" : "odd";        
                            html = html + '<tr class="' + css + '"><td>'+$("<div/>").text(JSON.parse(oldData[i])).html()+'</td><td>'+$("<div/>").text(JSON.parse(newData[i])).html()+'</td></tr>';
                            }
                            $('div#employee_list table.table').append(html);
                            
                    }
            }
        });        
}

</script>

  

