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
 * Actions class for PIM module deleteAttachmentAction
 */
class deleteAttachmentsAction extends basePimAction {
    private $AuditLogService;
    
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    
    /**
     * Delete employee attachments
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully deleted, false otherwise
     */
    public function execute($request) {
        $this->form = new EmployeeAttachmentDeleteForm(array(), array(), true);

        $this->form->bind($request->getParameter($this->form->getName()));
        if ($this->form->isValid()) {
            $empId = $request->getParameter('EmpID', false);
            if (!$empId) {
                throw new PIMServiceException("No Employee ID given");
            }
            
            if (!$this->IsActionAccessible($empId)) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
            }
            
            $attachmentsToDelete = $request->getParameter('chkattdel', array());
            if ($attachmentsToDelete) {
                $service = new EmployeeService();
                $var = $this->getAuditLogService()->getAttachmentScreen($empId, $attachmentsToDelete);
                $service->deleteEmployeeAttachments($empId, $attachmentsToDelete);
                $this->getScreen($var);
                $this->getUser()->setFlash('listAttachmentPane.success', __(TopLevelMessages::DELETE_SUCCESS));
            }
        }

        $this->redirect($this->getRequest()->getReferer(). '#attachments');
    }

    function getScreen($screen)
    {
     
        switch(true)
        {	
            case ($screen === 'personal'):
            {
               $this->getAuditLogService()->auditLogQuery('PIM','viewEmployee','Personal Details'); 
               break; 
            }
            case ($screen === 'dependents'):
            {
               $this->getAuditLogService()->auditLogQuery('PIM','viewDependents','Dependents'); 
              break;
            }	
            case ($screen === 'immigration'):
                {
                   $this->getAuditLogService()->auditLogQuery('PIM','viewImmigration','Immigration'); 
                    break;
            }
            case ($screen === 'job'):
                {
                   $this->getAuditLogService()->auditLogQuery('PIM','viewJobDetails','Job'); 
                    break;
                }   
            case ($screen === 'salary'):
                {
                   $this->getAuditLogService()->auditLogQuery('PIM','viewSalaryList','Salary'); 
                    break;
                }	
            case ($screen === 'report-to'):
                {
                   $this->getAuditLogService()->auditLogQuery('PIM','viewReportToDetails','Report-to'); 
                    break;
                }
               case ($screen === 'qualifications'):
                {
                   $this->getAuditLogService()->auditLogQuery('PIM','viewQualifications','Qualifications'); 
                     break;
                }
               case ($screen === 'membership'):
                {
                   $this->getAuditLogService()->auditLogQuery('PIM','viewMemberships','Membership'); 
                         break;
                }
              
         }	 
    }
}
