
<?php  $url= explode('expenseId=',$_SERVER['REQUEST_URI']); ?>

  
<?php  if(!empty($url['1'])){ 
  $breadcrumb['name'] = "View My Expense";
  $breadcrumb['link'] = "/symfony/web/index.php/expense/viewMyExpenseReport";  ?>

<?php  } 
 $breadcrumbnew['name']='Apply Expense';
  $breadcrumbnew['link'] = null;
$breadcrumb=array($breadcrumb,$breadcrumbnew);  ?>
<?php include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>


<?php 

use_stylesheets_for_form($form); 
 // use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/viewSystemUserSuccess')); 
?>
<?php use_javascripts_for_form($form); ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmExpensePlugin', 'js/editExpense')); ?>
<?php if ($form->hasErrors()): ?>
  <span class="error">
    <?php
    echo $form->renderGlobalErrors();

    foreach ($form->getWidgetSchema()->getPositions() as $widgetName) {
      echo $form[$widgetName]->renderError();
    }
    ?>
  </span>
<?php endif; ?>

<style>
em{
  color:red;
}
</style>


<div class="box searchForm toggableForm">
  <div class="head">
    <h1><?php echo __("Apply Expense"); ?></h1>
  </div>
  <div class="inner">
    <?php include_partial('global/flash_messages', array('prefix' => 'applyexpense')); ?>

    <form action="<?php echo url_for("expense/applyexpense?".http_build_query(array('expenseId' => $expenseId)))?>" id="travel_expense" name="employeeSelectForm" method="post" enctype="multipart/form-data">

     <?php echo $form['_csrf_token']; ?>
     <fieldset>
      <ol>
        <li>
          <?php
          echo $form['tripName']->renderLabel(__('Trip name/Purpose<em> *</em>')); 
          echo $form['tripName']->render(array("maxlength" => 20));
          ?>
          <label id="commentCustomerName"></label>
        </li>   
        <li>
          <?php
          echo $form['customerName']->renderLabel(__('Client Name<em> *</em>')); 
          echo $form['customerName']->render();
          ?>
          <label id="commentCustomerName"></label>
        </li>   
        <li>
          <?php
          echo $form['projectName']->renderLabel(__('Project Name<em> *</em>')); 
          echo $form['projectName']->render();
          ?>
          <label id="commentProjectName"></label>
        </li>

      </ol>
      <ol>
        <div id="validationMsg">
          <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
        </div>
        <table class="table">
          <thead>
            <tr><?php $i=0; ?>
            <!-- Heading for the apply expense -->
            <th> </th>
            <th> <?php echo __('Date <em> *</em>') ?></th>
            <th> <?php echo __('Expense Type <em> *</em>') ?> </th>
            <th> <?php echo __('Description <em> *</em>')?></th>
            <th> <?php echo __('Paid in advance <em> *</em>')?></th>
            <th> <?php echo __('Attachment <em> **</em>')?></th>
            <th> <?php echo __('Amount <em> *</em>')?></th>
            <th> <?php echo __('Currency <em> *</em>')?></th>  
            </tr>
        </thead>
        <tbody>
        </tbody>
        <?php  /*for showing first row for apply expense*/ 
        if ($expenseItemsValuesArray==null){ ?>
          <script type="text/javascript"> var editting = false;</script>
          <tr>
            <td id=""> <?php echo $form['initialRows'][$i]['toDelete'] -> render();?></td>
            <td> <?php echo $form['initialRows'][$i]['Date'] -> render();?></td>
            <td> <?php echo $form['initialRows'][$i]['expense_type']-> render();?></td>
            <td> <?php echo $form['initialRows'][$i]['message']-> render(array("maxlength" => 200));?></td>
            <td> <?php echo $form['initialRows'][$i]['paid_by_company']-> render();?></td>
            <td class="file"> <?php echo $form['initialRows'][$i]['attachment']-> render();?> <!-- <?php //echo $form['initialRows'][$i]['noAttachment']-> render();?> --></td>
            
            <td> <?php echo $form['initialRows'][$i]['amount']->render();?></td>
          <td> <?php echo $form['initialRows'][$i]['currency']->render();?></td>
          </tr> 
          <?php $i++;}
          else {
            /*for showing the saved expense record for editting*/
            foreach ($expenseItemsValuesArray as $row) {?>
              <script type="text/javascript"> var editting = true;</script>
              <tr>
               
                <td id="<?php echo $row['item_id'];?>"><?php echo $form['initialRows'][$i]['toDelete'] -> render();?></td>
                <td> <?php echo $form['initialRows'][$i]['Date'] -> render();?></td>
                <td> <?php echo $form['initialRows'][$i]['expense_type']-> render();?></td>
                <td> <?php echo $form['initialRows'][$i]['message']-> render(array("maxlength" => 50));?></td>
                <td> <?php echo $form['initialRows'][$i]['paid_by_company']-> render();?></td>
                <td class="file" >
                  <div id="display_file_<?php echo $i ;?>">
                    <?php
                      if ($row['file_name']!=null){
                        echo $row['file_name'];?>
                        <br>
                        <?php } ?>
                  </div>
                  <?php echo $form['initialRows'][$i]['attachment']->render();
                    if ($row['file_name']!=null) { ?>
                       <input type="button" id= "remove_attachment_button_<?php echo $i ;?>" class="delete" onclick="removeAttachment(<?php echo $row['id'].','.$i ?>)" value="x" style="background-color: #aa4935;color: white;height: 30px; width: 4%; padding-left: 15px; padding-right: 20px; padding-top: 1px; padding-bottom: 2px;"/>
                  <?php } ?>
                </td>
                <td> <?php echo $form['initialRows'][$i]['amount']->render();?></td>
                 <td> <?php echo $form['initialRows'][$i]['currency']->render();?></td>
              </tr>
              <script type="text/javascript"> var filename = <?php  echo $row['file_name']; ?></script>
              <?php $i++;}} ?> 
              <tr id="extraRows">

              </tr>
            </tbody>


          </table>

        </ol>
        <p style="float: left;" class='required'>
          <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?> <br>
          <em>**</em><?php echo " " . __('Accepts all types of files up to 5MB');?><br>
        <!-- <em>**</em><?php //echo " " . __('Tick the check box to declare no attachment');?> -->
        </p>
        <p style="float: right;">
          <?php sfContext::getInstance()->getUser()->setFlash('employeeId', $employeeId); ?>
          <?php if($submitStatus!='SUBMITTED' ) {?>
          <input type="submit" class="" value="<?php echo __('Submit') ?>" name="btnSave" id="submitSave"/>
          <?php }?>
          <input type="submit" class="" value="<?php echo __('Save') ?>" name="btnSaveOnly" id="saveOnly"/>
    
          <input type="button" class="" id="btnAddRow" value="<?php echo __('Add Row') ?>" name="btnAddRow">
          <input type="button" class="delete" id="submitRemoveRows" value="<?php echo __('Remove Rows') ?>" name="btnRemoveRows">
          <?php echo button_to(__('Reset'), 'expense/applyExpense?', array('class' => 'reset', 'id' => 'btnReset')) ?>
          <?php  ?>
        </p>

      </fieldset>
    </form>
  </div>
</div>



<script type="text/javascript">
  var link = "<?php echo url_for('expense/addRow') ?>";
  var rows = <?php echo $form['initialRows']->count() + 1 ?>;
  var linkToDeleteRow = "<?php echo url_for('expense/deleteRows') ?>";
  var linkToRemoveAttachment = "<?php echo url_for('expense/deleteAttachment') ?>";
  var lang_removeSuccess = '<?php echo __('Successfully Removed')?>';
  var lang_noRecords='<?php echo __('Select Records to Remove'); ?>';
  var lang_noChagesToDelete = '<?php echo __('Remove Row Not Allowed; Minimum One Row Required');?>';
  var closeText = '<?php echo __('Close');?>';
  var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
  var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
  var insertText = '<?php echo __("Should be Filled"); ?>';
  var selectText = '<?php echo __("Should be selected"); ?>';
  var validDateMsg = '<?php echo __("Should be a date"); ?>';
  var validNumberMsg = '<?php echo __("Should be a number"); ?>';
  var validDescriptionMsg = '<?php echo __("Should be a text"); ?>';
  var lang_negativeAmount = "<?php echo __("Should be a positive number"); ?>";
  var lang_tooLargeAmount = "<?php echo __("Should be less than %amount%", array("%amount%" => '1000,000,000.00')); ?>";
  var validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
  var please_fill_field = "<?php echo __('Please fill the required fields'); ?>";
  var please_fill_date_field = "<?php echo __('Please Enter a Valid Date'); ?>";
  var please_fill_attachment = "<?php echo __('Please add an attachment'); ?>";
  var please_check_attachment = "<?php echo __('File size exceeded'); ?>";
  var please_fill_amount = "<?php echo __('Amount Cannot be Blank'); ?>";
  var please_fill_amount_field = "<?php echo __('Please Enter a Valid Amount'); ?>";
  var getProjectLink = "<?php echo url_for('expense/getProjectLinkAjax') ?>";

  function removeAttachment(attachmentId, rowNumber){
    var expenseItemId = attachmentId;
    var answer = confirm('Are you sure you want to remove the attachments?');
    if (answer)
        {
            $.ajax({
                type: 'POST',
                url: linkToRemoveAttachment,
                data: "expenseItemId="+expenseItemId,
                async: false,
                success: function(state){
                  $('#display_file_'+rowNumber).hide();
                  $('#remove_attachment_button_'+rowNumber).hide();
                }
            });
        }
        else
          {
              console.log('cancel');
          }
  }
  </script>


