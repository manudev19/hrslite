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

class EmployeeExitDetailsForm extends BaseForm {

    private $nationalityService;
    private $employeeService;
    private $readOnlyWidgetNames = array();
    private $gender;
    private $employee;
    public $fullName;
    private $jobTitleService;
  
    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    const CONTRACT_KEEP = 1;
    const CONTRACT_DELETE = 2;
    const CONTRACT_UPLOAD = 3;

    /**
     * Get NationalityService
     * @returns NationalityService
     */
    public function getNationalityService() {
        if (is_null($this->nationalityService)) {
            $this->nationalityService = new NationalityService();
        }
        return $this->nationalityService;
    }
    

    /**
     * Set NationalityService
     * @param NationalityService $nationalityService
     */
    public function setNationalityService(NationalityService $nationalityService) {
        $this->nationalityService = $nationalityService;
    }

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployseeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }
   
    public function configure()
     {
        $this->personalInformationPermission = $this->getOption('personalInformationPermission');
        $this->canEditSensitiveInformation = $this->getOption('canEditSensitiveInformation');
        $empNumber = $this->getOption('empNumber');  
        $jobTitleId = $employee->job_title_code;
        $jobTitles = $this->_getJobTitles($jobTitleId);
        $empService = new EmployeeService();
        $this->employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->gender = ($this->employee->emp_gender != "") ? $this->employee->emp_gender : "";
       $this->fullName = $this->employee->getFullName();
        $this->setWidgets(array('job_title' => new sfWidgetFormSelect(array('choices' => $jobTitles))));
        $widgets = array('txtEmpID' => new sfWidgetFormInputHidden(array(), array('value' => $this->employee->empNumber)));
        $validators = array('txtEmpID' => new sfValidatorString(array('required' => true)));
         // Default values
         $this->setDefault('emp_number', $empNumber);
         $this->setDefault('emp_status', $employee->emp_status);
         if (!empty($jobTitleId)) {
             $this->setDefault('job_title', $jobTitleId);
             $jobTitle = $this->getJobTitleService()->getJobTitleById($jobTitleId);
             $this->jobSpecAttachment = $jobTitle->getJobSpecificationAttachment();
         }
        if ($this->personalInformationPermission->canRead()) {

            $personalInfoWidgets = $this->getPersonalInfoWidgets();
            $personalInfoValidators = $this->getPersonalInfoValidators();

            if (!$this->personalInformationPermission->canUpdate()) {
                foreach ($personalInfoWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                    $this->readOnlyWidgetNames[] = $widgetName;
                }
            }
            $widgets = array_merge($widgets, $personalInfoWidgets);
            $validators = array_merge($validators, $personalInfoValidators);
            $sensitiveInfoWidgets = $this->getSensitiveInfoWidgets();
            $sensitiveInfoValidators = $this->getSensitiveInfoValidators();
            if (!$this->canEditSensitiveInformation) {
                foreach ($sensitiveInfoWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                    $this->readOnlyWidgetNames[] = $widgetName;
                }
            }
            $widgets = array_merge($widgets, $sensitiveInfoWidgets);
            $validators = array_merge($validators, $sensitiveInfoValidators);
        }
        $this->setWidgets($widgets);
        $this->setValidators($validators);
        $this->widgetSchema->setNameFormat('personal[%s]');
    }

    public function getReadOnlyWidgetNames() {
        return $this->readOnlyWidgetNames;
    }
    private function getNationalityList() {
        $nationalityService = $this->getNationalityService();
        $nationalities = $nationalityService->getNationalityList();
        $list = array(0 => "-- " . __('Select') . " --");

        foreach ($nationalities as $nationality) {
            $list[$nationality->getId()] = $nationality->getName();
        }
        return $list;
    }
   
    private function getPersonalInfoWidgets() {
        if (!empty($jobTitleId)) {
            $this->setDefault('job_title', $jobTitleId);
            $jobTitle = $this->getJobTitleService()->getJobTitleById($jobTitleId);
            $this->jobSpecAttachment = $jobTitle->getJobSpecificationAttachment();
        }
        $jobTitles = $this->_getJobTitles($jobTitleId);
        $subDivisions = $this->_getSubDivisions();
        $widgets = array(
            'empNumber' => new sfWidgetFormInputText(),
            'txtEmpLastName' => new sfWidgetFormInputText(),
            'txtEmpFirstName' => new sfWidgetFormInputText(),
            'txtEmpMiddleName' => new sfWidgetFormInputText(),
            'txtEmpNickName' => new sfWidgetFormInputText(),
            'job_title' => new sfWidgetFormSelect(array('choices' => $jobTitles)),
            'sub_unit' => new sfWidgetFormSelect(array('choices' => $subDivisions)),
              'emp_resion_of_resignation' => new sfWidgetFormTextArea(),
            'role_in_department' => new sfWidgetFormInputCheckbox(  array('value_attribute_value' => '1', 'default' => false)), 
            'Manager_Status' => new sfWidgetFormSelect(array('choices' => array('' => '-- ' . __('Select') . ' --', 'Approved' => __('Approved'), 'Rejected' => __('Rejected')))),
            'HR_Status' => new sfWidgetFormSelect(array('choices' => array('' =>'-- ' . __('Select') . ' --', 'Approved' => __('Approved'), 'Rejected' => __('Rejected'))))
            
        );
       //setting default values
        $widgets['empNumber']->setAttribute('value', $this->employee->empNumber);
        $widgets['txtEmpLastName']->setAttribute('value', $this->employee->lastName);
        $widgets['txtEmpFirstName']->setAttribute('value', $this->employee->firstName);
        $widgets['txtEmpMiddleName']->setAttribute('value', $this->employee->middleName);
        $widgets['txtEmpNickName']->setAttribute('value', $this->employee->nickName);
        $widgets['job_title']->getAttribute('value', $this->employee->job_title_code);
        $widgets['sub_unit']->setAttribute('value', $this->employee->work_station);
       $widgets['Manager_Status']->setAttribute('value',$this->employee->Manager_Status);
       $widgets['HR_Status']->setAttribute('value',$this->employee->HR_Status);
         $widgets['emp_resion_of_resignation']->setAttribute('value',$this->employee->emp_resion_of_resignation);
        
        if (!empty($jobTitleId)) 
        {
            $this->setDefault('job_title', $jobTitleId);
            $jobTitle = $this->getJobTitleService()->getJobTitleById($jobTitleId);
        }
        $this->setDefault('eeo_category', $employee->eeo_cat_code);
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        $this->setDefault('sub_unit', $employee->work_station);       
        $widgets['sub_unit']->setDefault( $this->employee->work_station);
      $widgets['HR_Status']->setDefault( $this->employee->HR_Status);
        $widgets['Manager_Status']->setDefault( $this->employee->Manager_Status);
        $widgets['emp_resion_of_resignation']->setDefault( $this->employee->emp_resion_of_resignation);
        $widgets['job_title']->setDefault( $this->employee->job_title_code);
        return $widgets;
    }

    private function getPersonalInfoValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        //setting server side validators
        $validators = array(
            'empNumber' => new sfValidatorString(array('required' => false)),
            'txtEmployeeId' => new sfValidatorString(array('required' => false)),
            'txtEmpFirstName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true),
                    array('required' => 'First Name Empty!', 'max_length' => 'First Name Length exceeded 30 characters')),
            'txtEmpMiddleName' => new sfValidatorString(array('required' => false, 'max_length' => 30, 'trim' => true), array('max_length' => 'Middle Name Length exceeded 30 characters')),
            'txtEmpLastName' => new sfValidatorString(array('required' => false, 'max_length' => 30, 'trim' => true),
                    array( 'max_length' => 'Last Name Length exceeded 30 characters')),
                    
                    'job_title' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($jobTitles))),
                   
            'sub_unit' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($subDivisions))),
          'Manager_Status' => new sfValidatorChoice(array('required' => false,'choices' => array_keys($Manager_Status))),
           'HR_Status' => new sfValidatorChoice(array('required' => false,'choices' => array_keys($HR_Status))),
        );
        return $validators;
    }
    private function getSensitiveInfoWidgets() {
        $widgets = array('txtEmployeeId' => new sfWidgetFormInputText(),
            'DOR' => new ohrmWidgetDatePicker(array(), array('id' => 'personal_DOR')),
         
            'emp_manager_id' => new sfWidgetFormInputText(),
            'empNumber' => new sfWidgetFormInputText()
           );
        $widgets['txtEmployeeId']->setAttribute('value', $this->employee->employeeId);
        $widgets['empNumber']->setAttribute('value', $this->employee->empNumber);
        $widgets['DOR']->setAttribute('value', set_datepicker_date_format($this->employee->emp_Date_of_resignation));
     
        $widgets['emp_manager_id']->setAttribute('value', $this->employee->emp_manager_id);
        return $widgets;
    }
    private function getSensitiveInfoValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $validators = array(
            'emp_resion_of_resignation' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
            'emp_manager_id' => new sfValidatorString(array('required' => false, 'max_length' => 150), array('max_length' => 'length exceeded 64 characters')),
            'DOR' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false), array('invalid' => "Date format should be" . $inputDatePattern)));

        return $validators;
    }

    /**
     * Get Employee object with values filled using form values
     */
    public function getEmployee() {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($this->getValue('emp_number'));
        $empStatus = $this->getValue('emp_status');
        if ($empStatus == '') {
            $employee->emp_status = null;
        } else {
            $employee->emp_status = $empStatus;
        }

        $employee = $this->employee;
      
        if ($this->personalInformationPermission->canUpdate()) {

            $employee->firstName = $this->getValue('txtEmpFirstName');
            $employee->empNumber = $this->getValue('empNumber');
            $employee->middleName = $this->getValue('txtEmpMiddleName');
            $employee->lastName = $this->getValue('txtEmpLastName');
            $employee->job_title_code = $this->getValue('job_title');
            $employee->work_station = $this->getValue('sub_unit');
            $employee->emp_resion_of_resignation = $this->getValue('emp_resion_of_resignation');
            $employee->HR_Status = $this->getValue('HR_Status');
          $employee->Manager_Status = $this->getValue('Manager_Status');
           
        }
        if ($this->canEditSensitiveInformation->canUpdate()) {
            $employee->employeeId = $this->getValue('txtEmployeeId');
            $employee->emp_Date_ = $this->getValue('DOR');
           
            $employee->emp_manager_id = $this->getValue('emp_manager_id');
            $employee->Manager_Status = $this->getValue('Manager_Status');
            $employee->HR_Status = $this->getValue('HR_Status');
           

        }
     
        return $employee;
    }
    private function _getEmpStatuses() {
        $empStatusService = new EmploymentStatusService();
        $choices = array('' => '-- ' . __('Select') . ' --');
        $statuses = $empStatusService->getEmploymentStatusList();
        foreach ($statuses as $status) {
            $choices[$status->getId()] = $status->getName();
        }
        return $choices;
    }
    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }
    
    private function _getJobTitles($jobTitleId) 
    {
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

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) 
    {
        $this->companyStructureService = $companyStructureService;
    }
    private function _getSubDivisions() {
        $subUnitList = array('' => '-- ' . __('Select') . ' --');
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
        $tree = $treeObject->fetchTree();
        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        asort($subUnitList);
        return $subUnitList;
    }
    
    private function _getLocations(Employee $employee) {
        $locationList = array('' => '-- ' . __('Select') . ' --');
        $locationService = new LocationService();
        $locations = $locationService->getLocationList();        
        $accessibleLocations = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Location');
        $empLocations = $employee->getLocations();        
        foreach ($empLocations as $location) {
            $accessibleLocations[] = $location->getId();
        }
        foreach ($locations as $location) {
            if (in_array($location->id, $accessibleLocations)) {
                $locationList[$location->id] = $location->name;
            }
        }
        return($locationList);
    }
}

