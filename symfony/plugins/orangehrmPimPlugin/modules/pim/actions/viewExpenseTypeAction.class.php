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

class viewExpenseTypeAction extends sfAction {
    
    private $expenseTypeConfigurationService;
    private $AuditLogService;
    
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    public function getExpenseTypeConfigurationService() {
        
        if (!($this->expenseTypeConfigurationService instanceof ExpenseTypeConfigurationService)) {
            $this->expenseTypeConfigurationService = new ExpenseTypeConfigurationService();
        }        
        
        return $this->expenseTypeConfigurationService;
    }

    public function setExpenseTypeConfigurationService($expenseTypeConfigurationService) {
        $this->expenseTypeConfigurationService = $expenseTypeConfigurationService;
    }
    
    public function execute($request) {
        
        $this->_checkAuthentication();
        
        $this->form = new ExpenseTypeForm();
        $this->records = $this->getExpenseTypeConfigurationService()->getExpenseTypeList();
        
        if ($request->isMethod('post')) {
            
			$this->form->bind($request->getParameter($this->form->getName()));
            
			if ($this->form->isValid()) {

                $this->_checkDuplicateEntry();
                
                $templateMessage = $this->form->save();
                $this->getAuditLogService()->auditLogQuery('ADMIN','viewExpenseType','Expense Type');
				$this->getUser()->setFlash($templateMessage['messageType'], $templateMessage['message']);                
                $this->redirect('pim/viewExpenseType');
                
            }
            
        }
        $this->listForm = new DefaultListForm();
    }
    
    protected function _checkAuthentication() {
        
        $user = $this->getUser()->getAttribute('user');
        
		if (!$user->isAdmin()) {
			$this->redirect('pim/viewPersonalDetails');
		}
        
    }

    protected function _checkDuplicateEntry() {

        $id = $this->form->getValue('id');
        $object = $this->getExpenseTypeConfigurationService()->getExpenseTypeByName($this->form->getValue('name'));
        
        if ($object instanceof ExpenseType) {
            
            if (!empty($id) && $id == $object->getId()) {
                return false;
            }
            
            $this->getUser()->setFlash('warning', __('Name Already Exists'));
            $this->redirect('pim/viewExpenseType');            
            
        }
        
        return false;

    }    
    
}
