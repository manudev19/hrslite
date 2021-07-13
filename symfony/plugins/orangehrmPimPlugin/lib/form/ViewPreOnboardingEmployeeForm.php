
<?php


/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class ViewPreOnboardingEmployeeForm extends sfForm
{

    private $jobTitleService;
    public $subunit;
    private $expenseService;

    public function getExpenseService() {

        if (is_null($this->expenseService)) {

            $this->expenseService = new ExpenseService();
        }

        return $this->expenseService;
    }
    public function getEmployeeService() {
        if(is_null( $this->EmployeeService))
        {
          $this->EmployeeService = new EmployeeService();
  
        }
        return $this->EmployeeService;
    }


    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }
    public function getCompanyStructureService()
    {

        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }
   

    private function _setSubunitWidget()
    {

        $subUnitList = array(0 => __("All"));
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
        $tree = $treeObject->fetchTree();
        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        return $subUnitList;
    }

    public function configure()
    {
        $employee = $this->getOption('candidateId');
     // var_dump($employee );
        $jobTitleId = $employee->job_title_code;
        $jobTitles = $this->_getJobTitles($jobTitleId);
        $location = array('' => __('Select'), 'Sun-1' => __('Sun-1'), 'Sun-2' => __('Sun-2'), 'Sun-3' => __('Sun-3'));
        $dedicated = array('' => __('Select'), 'Dedicated' => __('Dedicated'), 'Shared' => __('Shared'));
        $international = array('' => __('Select'), 'Domestic' => __('Domestic'), 'International' => __('International'));
      //  $current_date=date('Y-m-d');

        $this->subunit = $this->_setSubunitWidget();
         if($employee != null) {
           $employeeDetails=$this->getEmployeeItems($employee );
     
           $managerId=$employeeDetails[0]['reporting_manager'];
           $ManagerName=$this->getManagerItems($employee,$managerId);
           $ManagerDetails=preg_replace('# {2,}#', ' ',$ManagerName[0]['empName']);
          $this->setValidators($this->getFormValidators());
         //  $current_date=date('Y-m-d');
        
             $totalRows = count($employeeDetails);
     
             $expenseRows = new sfForm();
          
                 $rowForm = new ViewPreOnboardingEmployeeForm();
                 $widgets= $rowForm->getWidgets();
               
                
                 $rowForm->setWidgets($widgets);
      
                 $rowForm->setDefault('candidate_number',$employeeDetails[0]['candidate_number']);
               $rowForm->setDefault('issuing_date',$employeeDetails[0]['issuing_date']);
                 $rowForm->setDefault('firstname',$employeeDetails[0]['firstname']);
                 $rowForm->setDefault('middlename',$employeeDetails[0]['middlename']);
                 $rowForm->setDefault('lastname',$employeeDetails[0]['lastname']);
                 $rowForm->setDefault('dedicated',$employeeDetails[0]['dedicated']);
                $rowForm->setDefault('candidate_no',$employeeDetails[0]['slno']);
                 $rowForm->setDefault('joined_Date',$employeeDetails[0]['joined_Date']);
                 $rowForm->setDefault('international',$employeeDetails[0]['international']);
                 $rowForm->setDefault('designation',$employeeDetails[0]['designation']);
                 $rowForm->setDefault('department',$employeeDetails[0]['department']);
                 $rowForm->setDefault('reporting_manager',array('empName' =>$ManagerDetails,'empId'=>$employeeDetails[0]['reporting_manager']));
                 $rowForm->setDefault('locations',$employeeDetails[0]['locations']);
                 $rowForm->setDefault('workstation',$employeeDetails[0]['workstation']);
 
               $this->embedForm('initialRows', $rowForm);
            }
 
        else{
        $this->setWidgets(array(
            'candidate_number' => new sfWidgetFormInputText(array(), array('class' => 'candidate_number', 'id' => 'candidate_number', 'maxlength' => 30)),
            'firstname' => new sfWidgetFormInputText(array(), array('class' => 'firstname', 'id' => 'firstname', 'maxlength' => 30)),
            'middlename' => new sfWidgetFormInputText(array(), array('class' => 'middlelast', 'id' => 'middlename', 'maxlength' => 30)),
            'lastname' => new sfWidgetFormInputText(array(), array('class' => 'lastname', 'id' => 'lastname', 'maxlength' => 30)),
            'issuing_date' => new ohrmWidgetDatePicker(array(), array('class' => 'issuing_date','name'=>'issuing_date','id'=>'issuing_date')),
            'joined_Date' => new ohrmWidgetDatePicker(array(), array('id' => 'joined_Date', 'class' => 'joined_Date','name'=>'joined_Date')),
            'designation' => new sfWidgetFormSelect(array('choices' => $jobTitles), array('class' => 'designation', 'id' => 'designation', 'maxlength' => 50)),
            'dedicated' => new sfWidgetFormSelect(array('choices' => $dedicated), array('class' => 'dedicated', 'id' => 'dedicated', 'maxlength' => 50)),
            'international' => new sfWidgetFormSelect(array('choices' => $international), array('class' => 'international', 'id' => 'international', 'maxlength' => 50)),
            'locations' => new sfWidgetFormSelect(array('choices' => $location), array('class' => 'locations', 'id' => 'locations', 'maxlength' => 50)),
            'reporting_manager' => new ohrmWidgetEmployeeNameAutoFill(array(), array('class' => 'reporting_manager', 'id' => 'reporting_manager', 'maxlength' => 50)),
            'workstation' => new sfWidgetFormInputText(array(), array('class' => 'workstation', 'id' => 'workstation', 'maxlength' => 50)),
            'department' => new sfWidgetFormChoice(array('choices' => $this->subunit), array('id' => 'department', 'class' => 'department')),

        ));
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $this->setValidators(array(
            'firstname' => new sfValidatorString(array('required' => true, 'max_length' => 35, 'trim' => true)),
            'workstation' => new sfValidatorString(array('required' => true, 'max_length' => 35, 'trim' => true)),
            'joined_Date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true)),
            'issuing_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true)),
            'reporting_manager' => new ohrmValidatorEmployeeNameAutoFill(array('required' => true))
        ));
    }}
    private function _getJobTitles($jobTitleId) {

        $jobTitleList = $this->getJobTitleService()->getJobTitleList("", "", false);
        $choices = array('' => '-- ' . __('Select') . ' --');

        foreach ($jobTitleList as $job) {
            if (($job->getIsDeleted() == JobTitle::ACTIVE) || ($job->getId() == $jobTitleId)) {
                $name = ($job->getIsDeleted() == JobTitle::DELETED) ? $job->getJobTitleName() . " (".__("Deleted").")" : $job->getJobTitleName();
                $choices[$job->getId()] = $name;
            }
        }
        return $choices;
    }

    private function getWidgets()
    {
        $employee = $this->getOption('employee');
        $jobTitleId = $employee->job_title_code;
        $jobTitles = $this->_getJobTitles($jobTitleId);
        $location = array('' => __('Select'), 'Sun-1' => __('Sun-1'), 'Sun-2' => __('Sun-2'), 'Sun-3' => __('Sun-3'));
        $dedicated = array('' => __('Select'), 'Dedicated' => __('Dedicated'), 'Shared' => __('Shared'));
        $international = array('' => __('Select'), 'Domestic' => __('Domestic'), 'International' => __('International'));
      //  $current_date=date('Y-m-d');
       
        $widgetArray = array( 
            'candidate_number' => new sfWidgetFormInputHidden(array(), array('class' => 'candidate_number', 'id' => 'candidate_number', 'maxlength' => 30,'required' => true,'readonly'=>'readonly')),
           'firstname' => new sfWidgetFormInputText(array(), array('class' => 'firstname', 'id' => 'firstname', 'maxlength' => 30,'required' => true)),
            'middlename' => new sfWidgetFormInputText(array(), array('class' => 'middlelast', 'id' => 'middlename', 'maxlength' => 30)),
            'lastname' => new sfWidgetFormInputText(array(), array('class' => 'lastname', 'id' => 'lastname', 'maxlength' => 30)),
            'issuing_date' => new ohrmWidgetDatePicker(array(), array('id' => 'issuing_date', 'class' => 'issuing_date','required' => true,  'maxlength' => 10)),
            'joined_Date' => new ohrmWidgetDatePicker(array(), array('id' => 'joined_Date', 'class' => 'joined_Date','required' => true)),
            'designation' => new sfWidgetFormSelect(array('choices' => $jobTitles), array('class' => 'designation', 'id' => 'designation', 'maxlength' => 50,'required' => true)),
            'dedicated' => new sfWidgetFormSelect(array('choices' => $dedicated), array('class' => 'dedicated', 'id' => 'dedicated', 'maxlength' => 50,'required' => true)),
            'international' => new sfWidgetFormSelect(array('choices' => $international), array('class' => 'international', 'id' => 'international', 'maxlength' => 50,'required' => true)),
            'locations' => new sfWidgetFormSelect(array('choices' => $location), array('class' => 'locations', 'id' => 'locations', 'maxlength' => 50,'required' => true)),
            'reporting_manager' => new ohrmWidgetEmployeeNameAutoFill(array(), array('class' => 'reporting_manager', 'id' => 'reporting_manager', 'maxlength' => 50,'required' => true)),
            'workstation' => new sfWidgetFormInputText(array(), array('class' => 'workstation', 'id' => 'workstation', 'maxlength' => 50,'required' => true)),
            'department' => new sfWidgetFormChoice(array('choices' => $this->subunit), array('id' => 'department', 'class' => 'department','required' => true))
            
 );
        return $widgetArray;

    }
    public function getFormValidators()
    {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $ValidateArray = array(  'firstname' => new sfValidatorString(array('required' => true, 'max_length' => 35, 'trim' => true)),
  'workstation' => new sfValidatorString(array('required' => true, 'max_length' => 35, 'trim' => true)),
  'joined_Date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true)),
  'issuing_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true)),
  'reporting_manager' => new ohrmValidatorEmployeeNameAutoFill(array('required' => true))
    );
    return $ValidateArray;
    }
    public function getManagerItems($CId,$MId)
    {
        $candidateItems1 =$this->getEmployeeService()->getManagerDetail($CId,$MId); 
        return $candidateItems1;
    }
    public function getEmployeeItems($CId)
    {
        $candidateItems =$this->getEmployeeService()->getEmployeeDetail($CId);
      if($candidateItems!=null){
            return $candidateItems;
        }
        else{
            return NULL;
        }
    }
}
