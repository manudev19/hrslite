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
 *
 */
class SystemUserForm extends BaseForm {

    private $userId = null;
    private $systemUserService;
    public $edited = false;

    public function getSystemUserService() {
        $this->systemUserService = new SystemUserService();
        return $this->systemUserService;
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

        $subUnitList = array();
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
        $tree = $treeObject->fetchTree();
        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        return $subUnitList;
    }
    public function configure() {

        $this->userId = $this->getOption('userId');
        $empNameStyle = array("class" => "formInputText inputFormatHint", "maxlength" => 200, "value" => __("Type for hints") . "...");
        if (!empty($this->userId)) {
            $this->edited = true;
            $empNameStyle = array("class" => "formInputText", "maxlength" => 200);
        }
        $userRoleList = $this->getAssignableUserRoleList();
        $statusList = $this->getStatusList();
        $this->subunit = $this->_setSubunitWidget();

        $this->setWidgets(array(
            'userId' => new sfWidgetFormInputHidden(),
            'userType' => new sfWidgetFormSelect(array('choices' => $userRoleList), array("class" => "formSelect", "maxlength" => 3)),
            'employeeName' => new ohrmWidgetEmployeeNameAutoFill(array(), $empNameStyle),
            'userName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
            'status' => new sfWidgetFormSelect(array('choices' => $statusList), array("class" => "formSelect", "maxlength" => 3)),
            'chkChangePassword' => new sfWidgetFormInputCheckbox(array(), array('class' => 'chkChangePassword', 'value' => 'on')),
            'password' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText password", "maxlength" => 20)),
            'confirmPassword' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText password", "maxlength" => 20)),
            'FunctionalHeadDept' => new ohrmWidgetCheckboxGroup(array('choices' => $this->subunit,'item_container'=> 'table','separator'=>"<br>",'show_all_option'=> true), array("class" => "formInputText" )),
        ));

        $this->setValidators(array(
            'userId' => new sfValidatorNumber(array('required' => false)),
            'userType' => new sfValidatorChoice(array('required' => true, 
                                                      'choices' => array_keys($userRoleList))),            
            'employeeName' => new ohrmValidatorEmployeeNameAutoFill(),
            'userName' => new sfValidatorString(array('required' => true, 'max_length' => 20)),
            'password' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
            'confirmPassword' => new sfValidatorString(array('required' => false, 'max_length' => 20)),
            'status' => new sfValidatorString(array('required' => true, 'max_length' => 1)),
            'chkChangePassword' => new sfValidatorString(array('required' => false)),
            'FunctionalHeadDept' => new sfValidatorChoice(array('choices' => array_keys($this->subunit),'multiple' => true,'required' => false)),
        ));


        $this->widgetSchema->setNameFormat('systemUser[%s]');

        if ($this->userId != null) {
            $this->setDefaultValues($this->userId);
        } else {
            $this->setDefault('userType', 2);
        }

        $this->getWidgetSchema()->setLabels($this->getFormLabels());

        //merge secondary password
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'saveSystemUser', 'SystemUserForm');

    }

    private function setDefaultValues($locationId) {

        $systemUser = $this->getSystemUserService()->getSystemUser($this->userId);
        $functionalHeadDept =$this->getSystemUserService()->getFunctionalHead($systemUser->getEmpNumber());

        $this->setDefault('FunctionalHeadDept', $functionalHeadDept);
        $this->setDefault('userId', $systemUser->getId());
        $this->setDefault('userType', $systemUser->getUserRoleId());
        $this->setDefault('employeeName', array('empName' => $systemUser->getEmployee()->getFullNameAndId(), 'empId' => $systemUser->getEmployee()->getEmpNumber()));
        $this->setDefault('userName', $systemUser->getUserName());
        $this->setDefault('status', $systemUser->getStatus());
    }

    /**
     * Get Pre Defined User Role List
     * 
     * @return array
     */
    private function getAssignableUserRoleList() {
        $list = array();
        $userRoles = $this->getSystemUserService()->getAssignableUserRoles();
        
        $accessibleRoleIds = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('UserRole');
        
        foreach ($userRoles as $userRole) {
            if (in_array($userRole->getId(), $accessibleRoleIds)) {
                $list[$userRole->getId()] = $userRole->getDisplayName();
            }
        }
        return $list;
    }

    private function getStatusList() {
        $list = array();
        $list[1] = __("Enabled");
        $list[0] = __("Disabled");

        return $list;
    }

    public function save() {

        $userId = $this->getValue('userId');
        $functionalHeadDept= $this->getValue('FunctionalHeadDept');
    
     
        $empNumber=$this->getValue('employeeName');
       
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
   
        if(in_array("17", $functionalHeadDept) )
        {
            $data=array("18","19","20","21");
            $functionalHeadDept = array_merge($functionalHeadDept,$data);
        }
        if(in_array("8", $functionalHeadDept))
        {
           
            $functionalHeadDept = array_merge($functionalHeadDept,array("26"));
        }
        
        
      if($this->getValue('userType')==8)
      {
        $employee =$employeeService->saveFunctionalHead($empNumber['empId'],$functionalHeadDept);
      }
      else{
        $employee =$employeeService->saveFunctionalHead($empNumber['empId'],0);
      }
        $password = $this->getValue('password');
        $changePasswordCheck = $this->getValue('chkChangePassword');
        $changePasword = false;
        if (empty($userId)) {
            $user = new SystemUser();
            $user->setDateEntered(date('Y-m-d H:i:s'));
            $user->setCreatedBy($this->getOption('sessionUser')->getUserId());
            $user->setUserPassword($this->getValue('password'));
            $changePasword = true;
        } else {
            $this->edited = true;
            $user = $this->getSystemUserService()->getSystemUser($userId);
            $user->setDateModified(date('Y-m-d H:i:s'));
            $user->setModifiedUserId($this->getOption('sessionUser')->getUserId());
            if (!empty($changePasswordCheck)) {
                $user->setUserPassword($this->getValue('password'));
                $changePasword = true;
            }
        }

        $user->setUserRoleId($this->getValue('userType'));
        $empData = $this->getValue('employeeName');
        $user->setEmpNumber($empData['empId']);
        $user->setUserName($this->getValue('userName'));

        $user->setStatus($this->getValue('status'));

        $savedUser = $this->getSystemUserService()->saveSystemUser($user, $changePasword);
        
        if ($savedUser instanceof SystemUser) {
            $this->setDefault('userId', $savedUser->getId());
        }
        
        //save secondary password
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->saveMergeForms($this, 'saveSystemUser', 'SystemUserForm');

        return $savedUser;
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = $employeeService->getEmployeeList();

        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $workShiftLength = 0;

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFullName();

                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $required = '<em> *</em>';
        $labels = array(
            'userType' => __('User Role') . $required,
            'employeeName' => __('Employee Name/Id') . $required,
            'userName' => __('Username') . $required,
            'password' => __('Password') . '<em class="passwordRequired"> *</em>',
            'confirmPassword' => __('Confirm Password') . '<em class="passwordRequired"> *</em>',
            'status' => __('Status') . $required,
            'chkChangePassword' => __('Change Password'),
            'FunctionalHeadDept' => __('Assign Department(s)') .'<em class="passwordRequired"> *</em>',
        );

        return $labels;
    }

}