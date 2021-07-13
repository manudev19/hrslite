<?php 
    $breadcrumb['name'] = "Cost Center Mapping";
    $breadcrumb['link'] = "/symfony/web/index.php/admin/viewCostCenterMapping"; ?>

    <?php 
        $breadcrumbnew['name']= "Cost Center Admin";
        $breadcrumbnew['link'] = null;
    ?>
    
<?php $breadcrumb=array($breadcrumb,$breadcrumbnew);?>
<?php include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>

<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/saveCostCenterMappingSuccess')); 
?>
<?php $form->getCostCenterMappingListAsJson(); ?>
<div id="addProject" class="box">
    
    <div class="head">
        <h1 id="addProjectHeading"><?php echo __("Cost Center Admin"); ?></h1>
    </div>
    
    <div class="inner">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
        <?php include_partial('global/flash_messages', array('prefix' => 'project')); ?>
        
        <form name="frmAddProject" id="frmAddProject" method="post" action="<?php echo url_for('admin/saveCostCenterMapping'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>
                    
                    <li>
                        <?php echo $form['costCenterId']->renderLabel(__('Cost Center Name') . ' <em>*</em>'); ?>
                        <?php echo $form['costCenterId']->render(array("class" => "formInputCostCenter")); ?>
                    </li>
                    
                    <li>
                        <?php echo $form['location']->renderLabel(__('Location') . ' <em>*</em>'); ?>
                        <?php echo $form['location']->render(array("class" => "formSelect")); ?>
                    </li>

                    <li>
                        <?php echo $form['empId']->renderLabel(__('Admin') . ' <em>*</em>'); ?>
                        <?php echo $form['empId']->render(array("class" => "formInputProjectAdmin", "maxlength" => 100)); ?>
                    </li>

                </ol>
                <ol>
                    <p style="float: left;" class='required'>
                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?> <br>
                    </p>
                </ol>
                <ol>
                    <div id="validationMsg">
                        <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
                    </div>   
                </ol>

                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
              
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>
        
        </form>
    
    </div>

</div>

<script type="text/javascript">
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeeList = eval(employees);
    var customers = <?php echo str_replace('&#039;', "'", $form->getCostCenterMappingListAsJson()); ?> ;
    var customerList = eval(customers);
    var numberOfProjectAdmins = <?php echo $form->numberOfProjectAdmins; ?>;
    var lang_typeHint = '<?php echo __("Type for hints") . "..."; ?>';
    var lang_nameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    // var closeText = '<?php //echo __('Close');?>';
    var please_fill_field = "<?php echo __('Please fill the required fields'); ?>";

    //var lang_activityNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validCustomer = '<?php echo __(ValidationMessages::INVALID); ?>';
    var lang_projectRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_exceed50Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
    var lang_exceed255Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
    var lang_exceed100Chars = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
    var custUrl = '<?php echo url_for("admin/saveCustomerJson"); ?>';
    var projectUrl = '<?php echo url_for("admin/saveCostCenterMapping"); ?>';
    var urlForGetActivity = '<?php echo url_for("admin/getActivityListJason?id="); ?>';
    //This triggers the url cal so comment it
      //var urlForGetProjectList = '<?php //echo url_for("admin/getProjectListJson?customerId="); ?>';
    var cancelBtnUrl = '<?php echo url_for("admin/viewCostCenterMapping"); ?>';
    var lang_enterAValidEmployeeName = '<?php echo __(ValidationMessages::INVALID); ?>';
    var lang_identical_rows = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_noActivities = "<?php echo __("No assigned activities"); ?>";
    var lang_noActivitiesSelected = "<?php echo __("No activities selected"); ?>";
    var id = '<?php echo $id; ?>';
    var custId = '<?php echo $custId; ?>';
    var lang_edit = '<?php echo __("Edit"); ?>';
    var lang_save = "<?php echo __("Save"); ?>";
    var lang_editProject = '<?php echo __("Edit Project"); ?>';
    var lang_Project = '<?php echo __("Project"); ?>';
    var lang_uniqueCustomer = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    //var lang_editActivity = '<?php echo __("Edit Project Activity"); ?>';
    //var lang_addActivity = '<?php echo __("Add Project Activity"); ?>';
    //var isProjectAdmin = '<?php echo $isProjectAdmin; ?>';
    // var dontHavePermission = '<?php //echo (!$projectPermissions->canCreate() || !$projectPermissions->canUpdate()); ?>';
</script>
