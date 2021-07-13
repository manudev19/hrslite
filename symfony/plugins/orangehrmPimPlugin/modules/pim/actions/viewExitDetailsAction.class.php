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
/**
 * personalDetailsAction
 *
 */
class viewExitDetailsAction extends basePimAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    
    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        

        $personal = $request->getParameter('personal');
        $empNumber = (isset($personal['txtEmpID']))?$personal['txtEmpID']:$request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        // TODO: Improve            

        $this->personalInformationPermission = $this->getDataGroupPermissions('personal_information', $empNumber);
        $this->canEditSensitiveInformation = ($empNumber == $loggedInEmpNum);


        $param = array('empNumber' => $empNumber, 
            'personalInformationPermission' => $this->personalInformationPermission,
            'canEditSensitiveInformation' => $this->canEditSensitiveInformation);

        if (!$this->IsActionAccessible($empNumber)) {
       
        }

        $this->showDeprecatedFields = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_PIM_SHOW_DEPRECATED);
       

        $this->setForm(new EmployeeExitDetailsForm(array(), $param, true));

        if ($this->personalInformationPermission->canUpdate()){
            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {

                    $this->_checkWhetherEmployeeIdExists($this->form->getValue('txtEmployeeId'), $empNumber);

                    $employee = $this->form->getEmployee();
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('pim/viewExitDetails?empNumber='. $empNumber);

                }
            }
        }
    }

    protected function _checkWhetherEmployeeIdExists($employeeId, $empNumber) {

        if (!empty($employeeId)) {

            $employee = $this->getEmployeeService()->getEmployeeByEmployeeId($employeeId);
          

            if (($employee instanceof Employee) && trim($employee->getEmpNumber()) != trim($empNumber)) {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Employee Id Exists')));
                $this->redirect('pim/viewExitDetails?empNumber='. $empNumber);
            }

        }

    }

}
