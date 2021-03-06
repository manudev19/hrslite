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
class deleteSubunitAction extends sfAction {

    private $companyStructureService;
    private $AuditLogService;
    
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }

    public function execute($request) {
        $id = trim($request->getParameter('subunitId'));

        try {
            $form = new DefaultListForm();
            $form->bind($request->getParameter($form->getName()));
            
            $object = new stdClass();
            
            if ($form->isValid()) {
                $subunit = $this->getCompanyStructureService()->getSubunitById($id);
                $result = $this->getCompanyStructureService()->deleteSubunit($subunit);
            
                if ($result) {
                    $this->getAuditLogService()->auditLogQuery('ADMIN','viewCompanyStructure','Structure');
                    $object->messageType = 'success';
                    $object->message = __(TopLevelMessages::DELETE_SUCCESS);
                } else {
                    $object->messageType = 'error';
                    $object->message = __(TopLevelMessages::DELETE_FAILURE);
                }
            } else {
                $object->messageType = 'error';
                $object->message = __(TopLevelMessages::VALIDATION_FAILED);
            }
        } catch (Exception $e) {
            $logger = Logger::getLogger('admin.subunit');
            $logger->error('Error deleting subunut: ' . $e);
            $object->messageType = 'error';
            $object->message = __(TopLevelMessages::DELETE_FAILURE);
        }

        @ob_clean();
        return $this->renderText(json_encode($object));
    }

}

