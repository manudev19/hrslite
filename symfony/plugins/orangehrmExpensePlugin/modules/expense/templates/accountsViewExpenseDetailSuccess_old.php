<?php echo javascript_include_tag(plugin_web_path('orangehrmExpensePlugin', 'js/editExpense')); ?>
<div class="box">
 <div class="head"style="text-align: center;">
  <h1><b>Expense Report</b></h1>

</div>

<div class="inner" >
  <fieldset>
    <table class="table">
      <thead>
        <tr>
          <th>Expense Id</th>
          <th>Employee Id</th>
          <th>Employee Name</th>
          <th>Client Name</th>
          <th>Project Name </th>
          <th>Submitted On </th>
          <?php foreach ($data['dueamount'] as $i =>$amountData) {?>
              <th>Expense Due(<?php echo $amountData['currency'] ?>) </th>
         <?php }  ?>

        </tr>
      </thead>
      <tbody>
        <tr>
          <td> <?php echo ($data['expenseNumber']); ?> </td>
          <td> <?php echo ($data['empnum']); ?> </td>
          <td><?php echo ($data['name']); ?> </td>
          <td><?php echo ($data['clientName']); ?> </td>
          <td><?php echo ($data['projectName']); ?> </td>
          <td><?php echo ($data['date']); ?> </td>
          <?php foreach ($data['dueamount'] as $i =>$amountData) {?>
              <td><?php echo $amountData['amount'] ?></td>
         <?php }  ?>

        </tr>

        <tr>

        </tr>
      </tbody>


    </table>
  </fieldset>
</div>

<?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
</div>
<div class="box">
  <div class="head" style="text-align: center;">
  <h1><b>Detailed Expense Report</b></h1>
  </div>
</div>
<?php include_component('core', 'ohrmList'); ?>
<style type="text/css">#total{
    text-align: right;
    padding-right: 15px;
}
#totalVertical{
    padding-left: 20px;
    width: 1945px;
    height: 25px;
    font-size: 14px;
    padding-top: 10px;
}
#totalrow1{
  font-size: 14px;
  width: 189px;
  padding-left: 7px;
}
#totalrow2{
  font-size: 14px;
  width: 426px;

}
</style>
<div class="box"><table>
  <tbody>

      <?php foreach ($data['totalamount'] as $i =>$amountData) {?>
           <tr class="total">
              <td id="totalVertical">Total Expense </td>
                <td id="totalrow1"><?php echo $amountData['amount'] ?></td>
                <td id="totalrow2"><?php echo $amountData['currency'] ?></td>
            </tr>
         <?php }  ?>

  </tbody>
</table>
</div>

     <!-- Approve and reject box -->

     <?php 
     if( $data['financeStatus'] == '1' && $data['state'] != 'REJECTED') { ?>
      <div class="box">
        <div class="head" style="text-align: center;">
          <h1 >Expense Action </h1>
        </div>
        <div class="inner">
          <form id="expenseActionFrm" name="expenseActionFrm"  action="<?php echo url_for("expense/accountsViewExpenseDetail?expenseId=".$data['expenseId']); ?>" method="post">
            <?php echo $formToImplementCsrfToken['_csrf_token']; ?>
            <fieldset>
              <ol>
                <li class="largeTextBox">
                  <label><?php echo __("Comment") ?></label>
                  <textarea name="Comment" id="txtComment" maxlength= '250'></textarea>
                </li>
              </ol>
              <p>
                <input type="submit" class=""  name="btnComment" id="CommentButton" value="<?php echo __('Comment') ?>" />
                <!-- <?php //if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_APPROVE])): ?> -->
                <input type="submit" class="" name="btnApprove" id="ApproveButton" value="<?php echo __('Comment & Process') ?>" />
            <!-- <?php //endif; ?>
            <?php //if (isset($allowedActions[WorkflowStateMachine::TIMESHEET_ACTION_REJECT])) : ?> -->
            <input type="submit" class="delete"  name="btnReject" id="RejectButton" value="<?php echo __('Reject') ?>" />
            <!-- <?php //endif; ?>  -->
          </fieldset>
        </form>
      </div>
    </div>
  <?php } ?>
  <div class="box miniList">

    <div class="head" style="text-align: center;">
      <h1 id="actionLogHeading"><?php echo __("Actions Performed on the Expense Report"); ?></h1>
    </div>

    <div class="inner">
      <table border="0" cellpadding="5" cellspacing="0" class="table">
        <thead>
          <tr>
            <th id="actionlogStatus" width="15%"><?php echo __('Action'); ?></th>
            <th id="actionlogPerform" width="25%"><?php echo __('Performed By'); ?></th>
            <th id="actionLogDate" width="15%"><?php echo __('Date'); ?></th>
            <th id="actionLogComment" width="45%"><?php echo __('Comment'); ?></th>
          </tr>
        </thead>
        <tbody><?php foreach ($actionDetails as $data) {?>
         <tr>
          <td><?php echo $data['action'] ?></td>
          <td><?php echo $data['emp_firstname'].' '.$data['emp_lastname']  ?></td>
          <td><?php echo $data['date_time'] ?></td>
          <td><?php echo $data['comment'] ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>

</div>
 <form action="<?php //echo url_for("expense/viewExpenseDetail?expenseId=".$data['expenseId']); ?>" id="ExpenseDownloadForm" name="ExpenseDownloadForm" method="post"><!-- inner -->
 <p> 
  <input type="submit"  name="btnDownload" onclick="downloadBTN()" id="downloadbutton" value="<?php echo __("Download") ?>"  />
  <input type="button" class="backbutton" id="btnBack" value="<?php echo __("Cancel")?>" tabindex="13" />
</p>

</form>
</div>

<!-- <?php //if ($sf_user->hasFlash('success')): ?>
  <div class="flash_error"><?php //echo $sf_user->getFlash('success') ?></div>
<?php //endif ?> -->

<script type="text/javascript">
  var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
  var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
  var submitAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT; ?>";
  var approveAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_APPROVE; ?>";
  var rejectAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_REJECT; ?>";
  var resetAction = "<?php echo WorkflowStateMachine::TIMESHEET_ACTION_RESET; ?>";
  var please_fill_comment = "<?php echo __('Please Fill the Comment Box'); ?>";
  function downloadBTN(){
    document.getElementById('download').value ="download";
    document.getElementById('ExpenseDownloadForm').submit();
  }
    //When Click back button 
  $("#btnBack").click(function() {
    location.href = "<?php echo url_for(public_path('../../symfony/web/index.php/expense/accountsExpenseReport')) ?>";  
  });
</script>
