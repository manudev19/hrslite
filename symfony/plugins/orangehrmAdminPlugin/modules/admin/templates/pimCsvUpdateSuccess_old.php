<?php $breadcrumb[]['name'] = 'Data Update';
$breadcrumb[]['link']=null;
include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>


<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/pimCsvUpdate')); ?>

<div id="pimCsvUpdate" class="box">
    
    <div class="head">
        <h1 id="pimCsvUpdateHeading"><?php echo __("CSV Data Update"); ?></h1>
    </div>
            
    <div class="inner">
        
        <?php include_partial('global/flash_messages', array('prefix' => 'csvUpdate')); ?>
                
        <form name="frmPimCsvUpdate" id="frmPimCsvUpdate" method="post" action="<?php echo url_for('admin/pimCsvUpdate'); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
            
            <fieldset>
                
                <ol class="normal">
                    
                    <li class="fieldHelpContainer">
                        <?php echo $form['csvFile']->renderLabel(__('Select File').' <em>*</em>'); ?>
                        <?php echo $form['csvFile']->render(); ?>
                    </li>
                    <li>
                    <label class="fieldHelpBottom"><?php echo __(CommonMessages::FILE_LABEL_SIZE); ?></label>

                    </li>
                </ol>
                
                  <ul class="disc">
                  <li>
                        <?php echo  ' <span class="boldText">If you are changing cell Data type, do not close the file</span> ' . __('or') . ' <span class="boldText">Save it and Upload</span>'; ?>
                    </li>
                    <li>
                        <?php echo __("Employee ID, First Name are compulsory");?>
                    </li>
                    <li>
                        <?php echo __("Column order should not be changed"); ?>
                    </li>
                    <li>
                        <?php echo __("All date fields should be in YYYY-MM-DD format");?>
                    </li>
                    <li>
                        <?php echo __("If gender is specified, value should be either") . ' <span class="boldText">Male</span> ' . __('or') . ' <span class="boldText">Female</span>'; ?>
                    </li>
                    <li>
                        <?php echo __("Marital Status value should be either") . ' <span class="boldText">Single</span>' . __(',') . ' <span class="boldText">Married</span> ' . __('or') . ' <span class="boldText">Other</span>'; ?>
                    </li>
                    <li>
                        <?php echo __("If numbers length is greater than 9 digit, cell format should be a Number ");?>
                    </li>
                    <li>
                        <?php echo __("Each Update file should be configured for 100 records or less");?>
                    </li>
                    <li>
                        <?php echo __("Multiple Update files may be required");?>
                    </li>
                    <li><?php echo __("Sample CSV file").': '; ?>
                        <a title="<?php echo __("Download"); ?>" target="_blank" class="download" 
                           href="<?php echo url_for('admin/sampleCsvUpdateDownload');?>"><?php echo __("Download"); ?></a>
                    </li>
                 </ul>
                
                <ol>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Upload"); ?>"/>
                </p>
                
            </fieldset>
    
        </form>
    
    </div>
    
</div>

<script type="text/javascript">
	var linkForDownloadCsv = '<?php url_for('admin/sampleCsvDownload');?>';
	var lang_csvRequired = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
</script>