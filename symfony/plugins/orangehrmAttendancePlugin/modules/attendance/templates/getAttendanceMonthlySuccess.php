<?php 
$breadcrumb[]['name'] = 'Monthly Attendance Records';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>  

    <div class = "box">

    <div class="head"><h1><?php echo __('Monthly Attendance report'); ?></h1></div>
        <div class="inner">
          
        <form id="download_attendance">
        <fieldset>
           <ol>
                <li>
                <?php echo $form['department']->renderLabel(__('Department'));?>
                <?php echo $form['department']->render();?>
                </li>
                <li>
                <?php echo $form['from_date']->renderLabel(__('From Date'))."&nbsp;&nbsp;";?>
                <?php echo $form['from_date']->render();?>
                <span id="error_from_date" class="validation-error">
                </li>
                <li>
                <?php echo $form['to_date']->renderLabel(__('To Date'))."&nbsp;&nbsp;";?>
                <?php echo $form['to_date']->render();?>
                <span id="error_to_date" class="validation-error">
                </li>
            </ol>
          
            <input type="button" id="download" class="btn btn-success" value="<?php echo __('Download'); ?>" />
       </fieldset>
       </form>
        </div>
        </div>
       
    
        <?php include_partial('core/spinner_common_file'); ?>

<script>
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
     var lang_dateError = '<?php echo __("To date should be after from date") ?>';
     var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
     var lang_Daterequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';

     $(document).ready(function(){

$(document).on('click','#download',function (event) {
    var error_from_date=check_from_date();
    var error_to_date=check_to_date();
    if(error_from_date==false && error_to_date==false)
     {
        
    var from_date = $(document).find('.from_date').val();
    var to_date = $(document).find('.to_date').val();
    var department = $(document).find('.department').val(); 
    
    var form = $('#download_attendance');
    var link_str = '/ApiCaller/saralservice/download_attendance_data/'+from_date+'/'+to_date+'/'+department;
    form.attr('action',link_str);
    form.attr('method','POST');
    $('#download').val(lang_processing);
      $('body').css('pointer-events', 'none');
      $('#overlay').css('display', 'block');
      setTimeout(function() { 
          $('#download').val('Download');
          $('body').css('pointer-events', 'auto');
          $('#overlay').css('display', 'none');
      }, 6000);
  form.submit();
}
});
});

     $('#error_from_date').hide();
     $('#error_to_date').hide();

     var error_from_date=false;
     var error_to_date=false;

     $('#from_date').focusout(function()
    {
        var error_from_date=check_from_date();
        if(error_from_date==false)
        $('#error_from_date').hide();
       else
        $('#error_from_date').show();
    });
    $('#to_date').focusout(function()
    {
        var error_to_date= check_to_date();
        if(error_to_date==false)
       $('#error_to_date').hide();
       else
       $('#error_to_date').show();
   });

function check_from_date() 
{
   var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
   var regExLeapYear=  /^((18|19|20)[0-9]{2}[\-.](0[13578]|1[02])[\-.](0[1-9]|[12][0-9]|3[01]))|(18|19|20)[0-9]{2}[\-.](0[469]|11)[\-.](0[1-9]|[12][0-9]|30)|(18|19|20)[0-9]{2}[\-.](02)[\-.](0[1-9]|1[0-9]|2[0-8])|(((18|19|20)(04|08|[2468][048]|[13579][26]))|2000)[\-.](02)[\-.]29$/;
   var from_date = $("#from_date").val();
   if(from_date=='yyyy-mm-dd' )
   {
    $("#error_from_date").html("This field is required ");
        $('#error_from_date').show();
        error_from_date=true;
   }else if (!from_date.match(regEx)||!from_date.match(regExLeapYear) ) {
        $("#error_from_date").html("Invalid From Date");
        $('#error_from_date').show();
        error_from_date=true;
   }
   else{
     $('#error_from_date').hide();
     error_from_date=false;
   }
   return error_from_date;
}

function check_to_date() 
{
   var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
   var regExLeapYear=  /^((18|19|20)[0-9]{2}[\-.](0[13578]|1[02])[\-.](0[1-9]|[12][0-9]|3[01]))|(18|19|20)[0-9]{2}[\-.](0[469]|11)[\-.](0[1-9]|[12][0-9]|30)|(18|19|20)[0-9]{2}[\-.](02)[\-.](0[1-9]|1[0-9]|2[0-8])|(((18|19|20)(04|08|[2468][048]|[13579][26]))|2000)[\-.](02)[\-.]29$/;
   var to_Date = $("#to_date").val();
   var from_date = $("#from_date").val();
   if(to_Date=='yyyy-mm-dd' )
   {
    $("#error_to_date").html("This field is required ");
        $('#error_to_date').show();
        error_to_date=true;
   }else if (!to_Date.match(regEx) ||!to_Date.match(regExLeapYear) ) {
        $("#error_to_date").html("Invalid To Date");
        $('#error_to_date').show();
        error_to_date=true;
   }else if(Date.parse(from_date) > Date.parse(to_Date))
   {
    $("#error_to_date").html("To date should be after from date");
        $('#error_to_date').show();
        error_to_date=true;
   }
   else{
     $('#error_to_date').hide();
     error_to_date=false;
   }
   return error_to_date;
}

</script>
