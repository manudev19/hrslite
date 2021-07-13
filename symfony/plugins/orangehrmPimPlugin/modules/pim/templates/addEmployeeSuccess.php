<?php $url = explode('1/', $_SERVER['REQUEST_URI']); ?>


<?php if (!empty($url['1'])) {
    $breadcrumb['name'] = "Employee List";
    $breadcrumb['link'] = "/symfony/web/index.php/pim/viewEmployeeList/reset/1";  ?>

<?php  }
$breadcrumbnew['name'] = 'Add Employee';
$breadcrumbnew['link'] = null;
$breadcrumb = array($breadcrumb, $breadcrumbnew);  ?>
<?php include_partial('core/breadcrumb_page', array('breadcrumb' => $breadcrumb)); ?>



<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/addEmployeeSuccess')); ?>

<div class="box">

<?php if (isset($credentialMessage)) { ?>

<div class="message warning">
    <?php echo __(CommonMessages::CREDENTIALS_REQUIRED) ?> 
</div>

<?php } else { ?>

    <div class="head">
        <h1><?php echo __('Add Employee'); ?></h1>
    </div>

    <div class="inner" id="addEmployeeTbl">
        <?php include_partial('global/flash_messages'); ?>        
        <form id="frmAddEmp" class="spinner_form" method="post" action="<?php echo url_for('pim/addEmployee'); ?>" 
              enctype="multipart/form-data">
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="searchbutton" id="btnSave" value="<?php echo __("Save"); ?>"  />
                </p>
            </fieldset>
        </form>
    </div>

<?php } ?>

<?php include_partial('core/spinner_common_file'); ?>
    
</div> <!-- Box -->    

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_processing = '<?php echo __(CommonMessages::LABEL_PROCESSING);?>';
    var lang_employeeIdRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var incorrectEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var lang_userNameRequired = "<?php echo __("Should have at least %number% characters", array('%number%' => 5)); ?>";
    var lang_passwordRequired = "<?php echo __("Should have at least %number% characters", array('%number%' => 4)); ?>";
    var lang_unMatchingPassword = "<?php echo __("Passwords do not match"); ?>";
    var lang_statusRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_locationRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var cancelNavigateUrl = "<?php echo public_path("../../index.php?menu_no_top=hr"); ?>";
    var createUserAccount = "<?php echo $createUserAccount; ?>";
    var ldapInstalled = '<?php echo ($sf_user->getAttribute('ldap.available')) ? 'true' : 'false'; ?>';

    var fieldHelpBottom = <?php echo '"' . __(CommonMessages::FILE_LABEL_IMAGE) . '. ' . __('Recommended Size (Pixels): 200X200') . '"'; ?>;
//]]>
</script>
