<?php use_javascripts_for_form($form); ?>
<?php

//use_javascript(plugin_web_path('orangehrmPimPlugin', 'js/preonboardingSuccess')); 

?>
<style>

#candidate_number{
  background-color:lightgray;
}

</style>

<br>
<?php include_partial('global/flash_messages', array('prefix' => 'PreOnboarding')); ?>
<div class="box ">
  <div class="head">
  <?php   if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") { ?>
    <h1><?php echo __('Pre On-boarding'); ?></h1>
  <?php }else
     if ($employeeValuesArray==null){ ?>
    <h1><?php echo __('Pre On-boarding Employee List'); ?></h1>
    <?php }else{?>
      <h1><?php echo __('Pre On-boarding'); ?></h1>
    <?php }?>
  </div>

  <div class="inner">
 <form action="" id="preonboarding" method="post"   enctype="multipart/form-data">
   
  <fieldset>
    <ol>
        <div id="validationMsg">
         <?php echo isset($messageData[0]) ? displayMainMessage($messageData[0], $messageData[1]) : ''; ?>
        </div>
      <ol>
     

       <?php  
        if ($employeeValuesArray==null){ ?>
         
         <?php   if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") { ?>
          <li>
<label class="fieldDescription"><?php echo __('Issuing Date'); ?><em>*</em></label>
  <?php echo $form['issuing_date']->render(array("class" => "editable","required" => false, "title" => __('Required'))); ?>
       <?php echo $form['issuing_date']->renderError(); ?>
       <span id="error_Issue_date" class="validation-error"></span>
     </li>
          <li>
           <label class="fieldDescription"><?php echo __('Joining Date'); ?><em>*</em></label>
            <?php echo $form['joined_Date']->render(array("required" => false, "title" => __('Required'))); ?>
            <?php echo $form['joined_Date']->renderError(); ?>
            <span id="error_date" class="validation-error"></span>
          </li>
          <li class="line nameContainer">
            
              <label for="Full_Name" class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
              <ol class="fieldsInLine">
                <li>
                  <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                  <?php echo $form['firstname']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
                  <span id="error_name" class="validation-error">
                  </span>
                </li>
                <li>
                  <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                  <?php echo $form['middlename']->render(array("class" => "block default editable")); ?>
                </li>
                <li>
                  <div class="fieldDescription"><?php echo __('Last Name'); ?></div>
                  <?php echo $form['lastname']->render(array("class" => "block default editable")); ?>
                </li>
              </ol>
          </li>
        
        <li>
                  <label class="fieldDescription"> <?php echo __('Designation'); ?><em>*</em></label>
                  <?php echo $form['designation']->render(array("class" => "block default editable")); ?>
                  <span id="error_designation" class="validation-error">
                  </span>
                </li>
          <li>
          <label class="fieldDescription"><?php echo __('Department'); ?><em>*</em></label>
            <?php echo $form['department']->render(array("class" => "block default editable")); ?>
            <span id="error_department" class="validation-error">
           </span>
          </li>
          <li>
            <label class="fieldDescription"><?php echo __('Reporting Manager'); ?><em>*</em></label>
            <?php echo $form['reporting_manager']->render(array("class" => "editable", "required" => false, "title" => __('Required'))); ?>
            <span id="error_manager" class="validation-error">
           </span>
          </li>
          <ul class="disc">
            <li>
            <?php echo __("Issuing Date should be current date"); ?>
            </li>
           </ul>
          <p>
  <li class="required new">
            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
          </li>
  </p>
          
          <?php } ?>
          <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] != "Yes"&&(isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor'] == "Yes"||isset($_SESSION['empNumber']))) { ?>
  <ul class="disc">
            <li>
            <?php echo __("Please click on <b>View</b> for Pre Onboarding Employee List"); ?>
            </li>
           
  </ul>
  
          <?php }}
      else{
  
 foreach ($employeeValuesArray as $row) {?>
        <li>
        <label class="fieldDescription"><?php echo __('Issuing Date'); ?><em>*</em></label>
          
            <?php echo $form['initialRows']['issuing_date']->render(array("class" => "editable")); ?>
            <span id="error_Issue_date" class="validation-error"></span>
         </li>

   
       <?php   if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") { ?>
        <li>
         <label class="fieldDescription"><?php echo __('Joining Date'); ?><em>*</em></label>
            <?php echo $form['initialRows']['joined_Date']->render(array("required" => false, "title" => __('Required'))); ?>
            <?php echo $form['initialRows']['joined_Date']->renderError(); ?>
             <span id="error_date" class="validation-error"></span>
          </li>
          
          <li class="line nameContainer">
          
              <label for="Full_Name" class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
              <ol class="fieldsInLine">
                <li>
                  <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                  <?php echo $form['initialRows']['firstname']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
                  <span id="error_name" class="validation-error">
                </li>

                <li>
                  <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                  <?php echo $form['initialRows']['middlename']->render(array("class" => "block default editable")); ?>
                </li>
                <li>
                  <div class="fieldDescription"><?php echo __('Last Name'); ?></div>
                  <?php echo $form['initialRows']['lastname']->render(array("class" => "block default editable")); ?>
                </li>
              </ol>
          </li>
        <li>
        <label class="fieldDescription"><?php echo __('Designation'); ?><em>*</em></label>
          <?php echo $form['initialRows']['designation']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
          <span id="error_designation" class="validation-error">
        </li>
        <li>
          <label class="fieldDescription"> <?php echo __('Department'); ?><em>*</em></label>
            <?php echo  $form['initialRows']['department']->render(array("class" => "block default editable")); ?>
            <span id="error_department" class="validation-error">
           </span>
          </li>
          
          <li>
            <label class="fieldDescription"><?php echo __('Reporting Manager'); ?><em>*</em></label>
            <?php echo $form['initialRows']['reporting_manager']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
            <span id="error_manager" class="validation-error">
          </li>
          <ul class="disc">
            <li>
            <?php echo __("Issuing Date should be current date"); ?>
            </li>
           </ul>
          <p>
  <li class="required new">
            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
          </li>
  </p>
          <?php } ?>
          <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] != "Yes"&&(isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor'] == "Yes"||isset($_SESSION['empNumber']))) { ?>
 <li>
          
            <?php echo$form['initialRows']['candidate_number']->render(array("class" => "editable")); ?>
          </li>
          <li>
          <label class="fieldDescription"><?php echo __('Joining Date'); ?><em>*</em></label>
            <?php echo $form['initialRows']['joined_Date']->render(array("disabled"=>"disabled","required" => false, "title" => __('Required'))); ?>
            <?php echo $form['initialRows']['joined_Date']->renderError(); ?>
            
          </li>
          
          <li class="line nameContainer">
          
              <label for="Full_Name" class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
              <ol class="fieldsInLine">
                <li>
                  <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                  <?php echo $form['initialRows']['firstname']->render(array("disabled"=>"disabled","class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
                  
                </li>

                <li>
                  <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                  <?php echo $form['initialRows']['middlename']->render(array("disabled"=>"disabled","class" => "block default editable")); ?>
                </li>
                <li>
                  <div class="fieldDescription"><?php echo __('Last Name'); ?></div>
                  <?php echo $form['initialRows']['lastname']->render(array("disabled"=>"disabled","class" => "block default editable")); ?>
                </li>
              </ol>
          </li>
        <li>
        <label class="fieldDescription"><?php echo __('Designation'); ?><em>*</em></label>
          <?php echo $form['initialRows']['designation']->render(array("disabled"=>"disabled","class" => "block default editable", "required" => false, "title" => __('Required'))); ?>

        </li>
        <li>
          <label class="fieldDescription"> <?php echo __('Department'); ?><em>*</em></label>
            <?php echo  $form['initialRows']['department']->render(array("disabled"=>"disabled","class" => "block default editable")); ?>
          </li>
       
          <li class="line nameContainer">
            <label for="Full_Name" class="hasTopFieldHelp"><?php echo __('System & Voice Services'); ?></label>
            <ol class="fieldsInLine">
              <li>
                <div class="fieldDescription" ><?php echo __('Dedicated / Shared'); ?><em>*</em></div>
                <?php echo $form['initialRows']['dedicated']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
                <span id="error_dedicated" class="validation-error">
              </li>
              <li></li>
              <li>
                <div class="fieldDescription"><?php echo __('International / Domestic'); ?><em>*</em></div>
                <?php echo $form['initialRows']['international']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
                <span id="error_international" class="validation-error">
              </li>
            </ol>
          </li>
          <li>
          <label class="fieldDescription"><?php echo __('Location'); ?><em>*</em></label>
            <?php echo $form['initialRows']['locations']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
            <span id="error_location" class="validation-error">
          </li>
          <li>
            
          <label class="fieldDescription"><?php echo __('Work Station No'); ?><em>*</em></label>
            <?php echo $form['initialRows']['workstation']->render(array("class" => "block default editable", "required" => false, "title" => __('Required'))); ?>
            <span id="error_workstation" class="validation-error">
          </li>
          <ul class="disc">
            <li>
            <?php echo __("Issuing Date should be current date"); ?>
            </li>
           </ul>
          <p>
  <li class="required new">
            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
          </li>
  </p>
        
<?php }}}?>

          </ol>
          </fieldset>
      <p >
         
       <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") { ?>
        <input type="submit" id="btnSave" name="btnSave" value="<?php echo __('Save'); ?>" /> 
       <?php if($employeeValuesArray!=null) {?>
      <input type="button" class="reset" id="cancelEditBtn" value="<?php echo __('Cancel'); ?>" />
       <?php }else{?>
       <input type="button" class="reset" id="cancelBtn" value="<?php echo __('Cancel'); ?>" />
        <?php }} ?>
        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] != "Yes"&&(isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor'] == "Yes"||isset($_SESSION['empNumber']))) { 
         if($employeeValuesArray!=null) {?>
         
        <input type="submit" id="btnSupSave" name="btnSave" value="<?php echo __('Save'); ?>" /> 
     <input type="button" class="reset" id="cancelEditedBtn" value="<?php echo __('Cancel'); ?>" />
        <?php }else{?>
       <input type="submit" id="btnView" name="btnView" value="<?php echo __('View'); ?>" />
      <?php }}?>

       
      </p>
      
     
    </form>
    
  </div>
</div>

<script  >


$(document).ready(function(){
  
 
  $(function() {
    var cancelBtnUrl = "<?php echo url_for("pim/viewMyPreOnboarding"); ?>";
   var cancelEditBtnUrl="<?php echo url_for("pim/viewpreonboardingemployeetable"); ?>";
   var cancelEditedBtnUrl="<?php echo url_for("pim/viewpreonboardingemployeetable"); ?>";


    $('#error_date').hide();
    $('#error_name').hide();
    $('#error_designation').hide();
    $('#error_department').hide();
    $('#error_manager').hide();
    $('#error_dedicated').hide();
    $('#error_international').hide();
    $('#error_location').hide();
    $('#error_workstation').hide();
    $('#error_Issue_date').hide();

    var error_date=false;
    var error_name=false;
    var error_manager=false;
    var error_designation=false;
    var error_department=false;
    var error_dedicated=false;
    var error_international=false;
    var error_location=false;
    var error_workstation=false;
    var error_Issue_date=false;


    $('#joined_Date').focusout(function()
    {
check_date();
    });
    $('#issuing_date').focusout(function()
    {
check_Issue_date();
    });
    $('#firstname').focusout(function()
    {
check_name();
    });
    $('#designation').focusout(function()
    {
check_designation();
    });
    $('#department').focusout(function()
    {
check_department();
    });
    $('#reporting_manager').focusout(function()
    {
     
check_manager();
   });
     $('#dedicated').focusout(function()
    {
check_dedicated();
    });
    
    $('#international').focusout(function()
    {
     
check_international();
    });
    $('#locations').focusout(function()
    {
check_location();
    });
    $('#workstation').focusout(function()
    {
check_workstation();
    });
  
 
    $('#cancelBtn').click(function() {
      window.location.replace(cancelBtnUrl);
    });
    
    $('#cancelEditBtn').click(function() {
      window.location.replace(cancelEditBtnUrl);
    });
    
    $('#cancelEditedBtn').click(function() {
      window.location.replace(cancelEditedBtnUrl);
    });
  

function check_date() 
{
  var date = new Date();
  var currentDate = date.getFullYear() + "-" + (("0" + (date.getMonth() + 1)).slice(-2))
   + "-" + (("0" + date.getDate()).slice(-2));
 
   var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
   var regExLeapYear=  /^((18|19|20)[0-9]{2}[\-.](0[13578]|1[02])[\-.](0[1-9]|[12][0-9]|3[01]))|(18|19|20)[0-9]{2}[\-.](0[469]|11)[\-.](0[1-9]|[12][0-9]|30)|(18|19|20)[0-9]{2}[\-.](02)[\-.](0[1-9]|1[0-9]|2[0-8])|(((18|19|20)(04|08|[2468][048]|[13579][26]))|2000)[\-.](02)[\-.]29$/;
   var joined_Date = $("#joined_Date").val();
   if(joined_Date=='' )
   {
    $("#error_date").html("This field is required ");
        $('#error_date').show();
        error_date=true;
   }else if (!joined_Date.match(regEx) || joined_Date<currentDate||!joined_Date.match(regExLeapYear) ) {
        $("#error_date").html("Invalid Joining Date");
        $('#error_date').show();
        error_date=true;
   }
   else{
     $('#error_date').hide();
   }
}


function check_Issue_date() //done
{
  var date = new Date();
  var currentDate = date.getFullYear() + "-" + (("0" + (date.getMonth() + 1)).slice(-2))
   + "-" + (("0" + date.getDate()).slice(-2));
 
   var regEx = /^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$/;
   var issuing_date = $("#issuing_date").val();
   if(issuing_date=='' )
   {
    $("#error_Issue_date").html("This field is required ");
        $('#error_Issue_date').show();
        error_Issue_date=true;
   }else if (!issuing_date.match(regEx)||issuing_date!=currentDate ) {
        $("#error_Issue_date").html("Invalid Issuing Date");
        $('#error_Issue_date').show();
        error_Issue_date=true;
   }
   else{
     $('#error_Issue_date').hide();
     error_Issue_date=false;
   }
}

function check_name() 
{
  //var regex=/^[a-zA-Z -]+$/;
  var regex=/^[A-Za-z]+(?: +[A-Za-z]+)*$/
  var firstname = $("#firstname").val();
  if(firstname=='')
  {
    $("#error_name").html("This field is required ");
        $('#error_name').show();
        error_name=true;
  }
  else if (!firstname.match(regex)) {
    $("#error_name").html("Invalid-Only alphabets are allowed");
        $('#error_name').show();
        error_name=true;
   }
   else{
     $('#error_name').hide();
   }
}

function check_manager()
{

   var reporting_manager = $("#reporting_manager").val();

   if(reporting_manager=='')
   {
   
    $("#error_manager").html("This field is required ");
        $('#error_manager').show();
        error_manager=true;
   }else if (reporting_manager=='Type for hints...') {
   $("#error_manager").html("This field is required ");
        $('#error_manager').show();
        error_manager=true;
   }
   else{
     $('#error_manager').hide();
   }
}

function check_designation() 
{
  var designation = $("#designation").val();
   if (designation=='') {
    $("#error_designation").html("This field is required ");
        $('#error_designation').show();
        error_designation=true;
   }
   else{
     $('#error_designation').hide();
   }
}

function check_department() 
{  
  var department = $("#department").val();
  if (department=='0') {
    $("#error_department").html("Required- Choose any department");
        $('#error_department').show();
        error_department=true;
   }
   else{
     $('#error_department').hide();
   }
}

function check_dedicated()
{
   var dedicated = $("#dedicated").val();
   if (dedicated=='' ) {
    $("#error_dedicated").html("This field is required ");
        $('#error_dedicated').show();
        error_dedicated=true;
   }
   else{
     $('#error_dedicated').hide();
   }
}

function check_international() //done
{
   var international = $("#international").val();
 
   if (international=='' ) {
    $("#error_international").html("This field is required ");
        $('#error_international').show();
        error_international=true;
   }
   else{
     $('#error_international').hide();
   }
}

function check_location()  //done
{ 
   var location = $("#locations").val();
   if (location=='' ) {
    $("#error_location").html("This field is required ");
        $('#error_location').show();
        error_location=true;
   }
   else{
     $('#error_location').hide();
   }
}

function check_workstation() //done
{ 
  var regex=/^[A-Za-z0-9]+(?: +[A-Za-z0-9]+)*$/
   var workstation = $("#workstation").val();
   if (workstation=='') {
    $("#error_workstation").html("This field is required ");
        $('#error_workstation').show();
        error_workstation=true;
   }else if(!workstation.match(regex) && workstation!='' )
   {
    $("#error_workstation").html("Invalid- Only alphanumerics are allowed");
        $('#error_workstation').show();
        error_workstation=true;
   } 
   else{
     $('#error_workstation').hide();
   }
}

$('#btnSave').click(function() {
$('#preonboarding').submit(function()
{
  
error_Issue_date=false;
error_date=false;
error_name=false;
error_manager=false;
error_designation=false;
error_department=false;


check_Issue_date();
check_date();
check_name();
check_manager();
check_designation();
check_department();


<?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") { ?>

if(error_Issue_date==false && error_date==false && error_name==false &&error_manager==false && error_designation==false &&
error_department==false)
{
  return true;
}
else
{
 return false;
}
<?php }?>
 
 
});
});
$('#btnSupSave').click(function() {
$('#preonboarding').submit(function()
{
  error_Issue_date=false;
  error_dedicated=false;
error_international=false;
error_location=false;
error_workstation=false;

check_Issue_date();
check_dedicated();
check_international();
check_location();
check_workstation();

<?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] != "Yes"&&(isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor'] == "Yes"||isset($_SESSION['empNumber']))) { ?>
        
   
 if(error_Issue_date==false && error_dedicated==false && error_international==false && error_location==false && error_workstation==false)
 {
  return true;
}
else
{
return false;
}
<?php }?>
  });});
  });
 });
</script>
