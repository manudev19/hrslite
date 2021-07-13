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

class deleteExpenseTypeAction extends sfAction {
    
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
        
        $toDeleteIds = $request->getParameter('chkListRecord');
        
        $this->_checkReasonsInUse($toDeleteIds);
        
        if (!empty($toDeleteIds) && $request->isMethod('post')) {
            $form = new DefaultListForm();
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                $result = $this->getExpenseTypeConfigurationService()->deleteExpenseType($toDeleteIds);
            }
            if ($result) {
                $this->getAuditLogService()->auditLogQuery('PIM','viewExpenseType','Expense Type');
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS)); 
            }            
            $this->redirect('pim/viewExpenseType');
        }       
        
    }
    
    protected function _checkAuthentication() {
        
        $user = $this->getUser()->getAttribute('user');
        
		if (!$user->isAdmin()) {
			$this->redirect('pim/viewPersonalDetails');
		}
        
    }  
    
    protected function _checkReasonsInUse($toDeleteIds) {
        
        if (!empty($toDeleteIds)) {
            
            if ($this->getExpenseTypeConfigurationService()->isReasonInUse($toDeleteIds)) {
                $this->getUser()->setFlash('warning', __('Expense Type(s) in Use'));
                $this->redirect('pim/viewExpenseType');
            }
            
        }
        
    }
    
}
