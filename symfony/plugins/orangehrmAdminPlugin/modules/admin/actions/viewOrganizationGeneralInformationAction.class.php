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
class viewOrganizationGeneralInformationAction extends sfAction {
    private $AuditLogService;
    
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

        $usrObj = $this->getUser()->getAttribute('user');
        if (!($usrObj->isAdmin())) {
            $this->redirect('pim/viewPersonalDetails');
        }

        $this->setForm(new OrganizationGeneralInformationForm());
        $employeeService = new EmployeeService();
        $this->employeeCount = $employeeService->getEmployeeCount();

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $result = $this->form->save();
                $name = $this->form->getValue('name');
                $organizationName = (!empty($name)) ? $name : __("Organization");
                $companyStructureService = new CompanyStructureService();
                $companyStructureService->setOrganizationName($organizationName);
                $this->getAuditLogService()->auditLogQuery('ADMIN','viewOrganizationGeneralInformation','General Information');
                $this->getUser()->setFlash('generalinformation.success', __(TopLevelMessages::SAVE_SUCCESS));
            }
        }
    }

}

