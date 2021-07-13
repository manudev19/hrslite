
<?php use_stylesheets_for_form($form); ?>
<?php 
use_stylesheet(plugin_web_path('orangehrmPimPlugin', 'css/viewPersonalDetailsSuccess.css'));
?>
<div class="box pimPane" id="employee-details">
    <?php echo include_component('pim', 'pimLeftMenu', array('empNumber'=>$empNumber,'txtEmployeeId'=>$id, 'form' => $form));?>
    <div class="personalDetails" id="pdMainContainer">
        <div class="head">
            <h1><?php echo __('Exit'); ?></h1>
        </div>    
        <div class="inner">
        <?php include_partial('global/flash_messages', array('prefix' => 'exitdetails')); ?>
            <form id="exitform" method="post" action="<?php echo url_for('pim/viewExitDetails'); ?>">
                <?php echo $form['_csrf_token']; ?>
                <?php echo $form['txtEmpID']->render(); ?>
                <fieldset>
                    <ol>
                        <li class="line nameContainer">
                            <label for="Full_Name" class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
                            <ol class="fieldsInLine">
                                <li>
                                    <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                                    <?php echo $form['txtEmpFirstName']->render(array("class" => "block default editable", "maxlength" => 30, "title" => __('First Name'))); ?>
                                </li>
                                <li>
                                    <div class="fieldDescription"><?php echo __('Last Name'); ?></div>
                                    <?php echo $form['txtEmpLastName']->render(array("class" => "block default editable", "maxlength" => 30, "title" => __('Last Name'))); ?>
                                </li>
                            </ol>    
                        </li>
                    </ol>
                    <ol>
                         <li>
                                <label for="personal_empNumber"><?php echo __('Employee Number'); ?></label>
                                <?php echo $form['empNumber']->render(array("maxlength" => 10, "class" => "editable"));?>
                        </li>
                        
                        <li>
                                <label for="personal_txtEmployeeId"><?php echo __('Employee Id'); ?></label>
                                <?php echo $form['txtEmployeeId']->render(array("maxlength" => 10, "class" => "editable"));?>
                        </li>
                        <li>
                            <?php echo $form['job_title']->renderLabel(__('Job Title')); ?>
                            <?php echo $form['job_title']->render(array("class" => "formSelect")); ?>    
                        </li>
                        <li>
                            <?php echo $form['sub_unit']->renderLabel(__('Sub Unit')); ?>
                            <?php echo $form['sub_unit']->render(array("class" => "formSelect")); ?>
                        </li>
                    </ol>
                    
<ol>  
                        <li>
                            <label for="personal_DOR"><em>*</em> <?php echo __("Date of Resignation"); ?></label>
                            <?php echo $form['DOR']->render(array("class"=>"editable","required" => true, "title" => __('Required'))); ?>
                            <span id="err_dor" class="validation-error">
              <?php if ($validationFordate != null) {
                echo $validationFordate;
              } ?>
            </span>
                        </li>
                        <li>
                            <label for="personal_emp_manager_id"><em>*</em> <?php echo __('Manager Email Id'); ?></label>
                            <?php echo $form['emp_manager_id']->render(array("maxlength" => 50, "class" => "editable","required" => true, "title" => __('Required'),"style"=> "width: 200px"));?>
                            <span id="err_mgr" class="validation-error">
              <?php if ($validationFordate != null) {
                echo $validationFordate;
              } ?>
              </span>
                        </li>
                    </ol>
                    <ol>
                    <li>
                        <label for="personal_emp_resion_of_resignation"><em>*</em> <?php echo __('Reason for Resignation:'); ?></label>
                        <?php echo $form['emp_resion_of_resignation']->render(array("maxlength" => 350, "class" => "textarea","required" => true, "title" => __('Required')))?>
                        <span id="err_resignation" class="validation-error">
                              <?php if ($validationFordate != null) {
                               echo $validationFordate;
                           } ?>
                                  </span>    
                          </li>
</ol>
<!-- changing here -->

    <?php if (isset($_SESSION['isSupervisor']) && $_SESSION['isSupervisor'] == "Yes") { ?>
        <div>    
        <ol>
                    <li>
                        <?php 
                             if(!($empNumber==$_SESSION['empNumber'])){
                             if (($_SESSION['userRole'] !="Team Lead")):?>
                            
                            <label for="personal_Manager_Status"><em>*</em><?php echo __('Exit Status'); ?></label>
                            <?php echo $form['Manager_Status']->render(array("class"=>"formSelect","required" => true, "title" => __('Required'))); ?>
                    </li>
                    </ol>
                        <?php if ($personalInformationPermission->canUpdate()) : ?>
                    <p><input type="button" id="manager" value="<?php echo __("Submit"); ?>" /></p>
                    
                  <?php endif;  ?>
                  <ol>
                    <?php endif; }?>
                   
                   
        </div>
    <?php } else { ?> 
                    <div>
                        
                        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") {?>
                       
                        
                <?php if(!($empNumber==$_SESSION['empNumber'])):?>
                <ol>
               <li>
                            <label for="personal_HR_Status"><em>*</em><?php echo __('Exit Status'); ?></label>
                            <?php echo $form['HR_Status']->render(array("class"=>"formSelect","required" => true, "title" => __('Required'))); ?>       
                        </li>
                        </ol>
                        
                        
                           <?php if ($personalInformationPermission->canUpdate()) : ?>
                            <p><input type="button" id="admin" value="<?php echo __("Submit"); ?>" />
                    </p>
                    <?php endif;  ?>     
                    <?php else : ?>   
                   
                    <?php endif;} ?>  
            </div><?php }  ?> 
                

                   <!-- to here -->
                   <ol>                       
                        <?php if(($empNumber==$_SESSION['empNumber'])):?>
                          <p><input type="button" id="employee" value="<?php echo __("Submit"); ?>" /></p> 
                          <?php else : ?>
                        <?php endif;  ?>
                     </ol>      
                    
                        
      </fieldset>
             
            </form>
            </div> <!-- inner -->
        </div> <!-- pdMainContainer -->


    </div> <!-- employee-details -->



    <!-- <script type="text/javascript">
var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
</script> -->
<script> 
 $(document).ready(function(){
    $("#exitform").validate({
      rules: {
        emp_resion_of_resignation: {
          required: true,
        },
        
      }
      
    });
    $("#employee").click(function(){
     
     var personal_txtEmployeeId = $("#personal_txtEmployeeId").val();
     var emp_resion_of_resignation = $("#personal_emp_resion_of_resignation").val();
     var personal_DOR = $("#personal_DOR").val();
     var emp_manager_id = $("#personal_emp_manager_id").val();    
     var empNumber = $("#personal_empNumber").val();   
     
     var date = new Date();
     var val = date.getFullYear() + "-" + (("0" + (date.getMonth() + 1)).slice(-2)) + "-" + (("0" + date.getDate()).slice(-2));
     var regexp = /^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@(suntechnologies|SUNTECHNOLOGIES])+\.(com)$/;
     var reg=/[%?'{}/<>[]/;
   
     var regEx = /^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/;
      
    if(personal_DOR<val ||!personal_DOR.match(regEx))
    {
        $("#err_dor").html("Select the valid Date");
      } 
      else if(!emp_manager_id.match(regexp))
      {
        $("#err_mgr").html("Enter valid Email ID");
      }
      else if(emp_resion_of_resignation.match(reg)) 
      {
        $("#err_resignation").html("Accepts only alphanumeric characters "); 
        
      }
      else if(personal_DOR=>val  )
    { 
      
        $("#exitform").validate({
      rules: {
        emp_resion_of_resignation: {
          required: true,
        },
        personal_DOR: {
          required: true,
        },
        emp_manager_id: {
          required: true,
        }
      }
   
    });
    if(emp_resion_of_resignation =='')
    {
      $("#err_resignation").html( "   Required ");
    }
    else
    {
         var check=checkAll();
   }
 
  if (check == true) {
        var form = $('#exitform');
        var link_str = '/ApiCaller/Insertservice/hs_hr_employee/'+personal_txtEmployeeId+'/'+emp_resion_of_resignation+'/'+personal_DOR+'/'+emp_manager_id+'/'+empNumber;
        form.attr('action', link_str); 
        form.attr('method', 'POST');
        form.submit();
        }
        else{
          if(emp_resion_of_resignation =='')
    {
      $("#err_resignation").html("   Required ");
    }
    else
    {
          alert("Request Canceled");
    }
        
    }
  }
  });

    
  });
  $('#manager').click(function() {
     
          var personal_txtEmployeeId = $("#personal_txtEmployeeId").val();
          var personal_Manager_Status = $("#personal_Manager_Status").val();
          var empNumber = $("#personal_empNumber").val();
          var form = $('#exitform');
          $("#exitform").validate({
           rules: {
             personal_Manager_Status: {
               required: true
             }
           }
           });
            
          var link_str = '/ApiCaller/Insertservice/manager_aproval/'+personal_txtEmployeeId+'/'+personal_Manager_Status+'/'+empNumber;
          form.attr('action', link_str);
          form.attr('method', 'POST');  
     
          form.submit();
     
      });
      $('#admin').click(function() {
   
       var personal_txtEmployeeId = $("#personal_txtEmployeeId").val();
      var personal_HR_Status = $("#personal_HR_Status").val();
      var empNumber = $("#personal_empNumber").val();
        var form = $('#exitform');
        $("#exitform").validate({
        rules: {
           personal_HR_Status: {
             required: true
           }
        }
       });
       
        var link_str = '/ApiCaller/Insertservice/hr_aproval/'+personal_txtEmployeeId+'/'+personal_HR_Status+'/'+empNumber;
        form.attr('action', link_str);
        form.attr('method', 'POST');
        form.submit();   
       
    });
      
  function checkAll()
 {
   if(confirm("Are You Sure You Want To Submit"))
   {
     return true;
   }
   else{
     return false;
   }
 }
</script>
