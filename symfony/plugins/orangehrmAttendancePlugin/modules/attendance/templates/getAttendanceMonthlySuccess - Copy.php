
<?php 
$breadcrumb[]['name'] = 'Monthly Attendance Records';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>  

    <div class = "box">
    <div class="head"><h1><?php echo __('Monthly Attendance report'); ?></h1></div>
        <div class="inner">
          
        <form id="download_attendance" class="spinner_form">
        <fieldset>
           <ol>
                <li>
                <?php echo $form['department']->renderLabel(__('Department'));?>
                <?php echo $form['department']->render();?>
                </li>
                <li>
                <?php echo $form['from_date']->renderLabel(__('From Date'))."&nbsp;&nbsp;";?>
                <?php echo $form['from_date']->render();?>
                </li>
                <li>
                <?php echo $form['to_date']->renderLabel(__('To Date'))."&nbsp;&nbsp;";?>
                <?php echo $form['to_date']->render();?>
                </li>
            </ol>
          
            <input type="button" id="download" class="btn btn-success download downloadBtn" value="<?php echo __('Download'); ?>" />
       </fieldset>
       </form>
        </div>
        </div>
       
    <?php include_partial('core/spinner_common_file'); ?>
          

<script>
$(document).ready(function(){
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';

    $(document).on('click','.download',function (event) {
        var from_date = $(document).find('.from_date').val();
        var to_date = $(document).find('.to_date').val();
        var department = $(document).find('.department').val(); 
        
        var form = $('#download_attendance');
        var link_str = '/ApiCaller/saralservice/download_attendance_data/'+from_date+'/'+to_date+'/'+department;
        form.attr('action',link_str);
        form.attr('method','POST');
        form.submit();
    });
});
</script>
