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
class viewCostCenterMappingAction extends baseAdminAction {

    private $projectService;

    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
            $this->projectService->setProjectDao(new ProjectDao());
        }
        return $this->projectService;
    }

    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {

        //$usrObj = $this->getUser()->getAttribute('user');
        $this->projectPermissions = $this->getDataGroupPermissions('time_projects');

        $userRoleManager = sfContext::getInstance()->getUserRoleManager();
        $allowedProjectList = $userRoleManager->getAccessibleEntityIds('Project'); 
        
        $isPaging = $request->getParameter('pageNo');
        $sortField = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $id = $request->getParameter('id');

        if ($this->projectPermissions->canRead()) {
            $pageNumber = $isPaging;  
            if ($id > 0 && $this->getUser()->hasAttribute('pageNumber')) {
                $pageNumber = $this->getUser()->getAttribute('pageNumber');
            }

            $limit = CostCenterMapping::NO_OF_RECORDS_PER_PAGE;
            $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;
            
        //To get the list of center along with their admin and location
            $adminList = $this->getProjectService()->getCostCenterByAdmin();
            //echo "<pre>"; var_dump($adminList); exit;
            $projectListCount = $this->getProjectService()->getSearchProjectListCount($searchClues, $allowedProjectList);
            //var_dump("here"); exit;
            $this->_setListComponent($adminList, $limit, $pageNumber, $projectListCount);
            $this->getUser()->setAttribute('pageNumber', $pageNumber);
            $params = array();
            $this->parmetersForListCompoment = $params;

            if ($this->getUser()->hasFlash('templateMessage')) {
                list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
            }

            if ($request->isMethod('post')) {
                //var_dump("here"); exit;
                $offset = 0;
                $pageNumber = 1;
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                $searchClues = $this->_setSearchClues($sortField, $sortOrder, $offset, $limit);
                    $this->getUser()->setAttribute('searchClues', $searchClues);
                    $searchedProjectList = $this->getProjectService()->searchProjects($searchClues, $allowedProjectList);
                    $projectListCount = $this->getProjectService()->getSearchProjectListCount($searchClues, $allowedProjectList);
                $this->_setListComponent($adminList, $limit, $pageNumber, $projectListCount);
                }
            }
            
        }
    }

    /**
     *
     * @param <type> $projectList
     * @param <type> $noOfRecords
     * @param <type> $pageNumber
     */
    private function _setListComponent($adminList, $limit, $pageNumber, $recordCount) {
        //var_dump("here1"); exit;
        $configurationFactory = new CostCenterMappingHeaderFactory();
        $runtimeDefinitions = array();
        $buttons = array();

        //if ($permissions->canCreate()) {
            $buttons['Add'] = array('label' => 'Add');
        //}

        //if (!$permissions->canDelete()) {
            $runtimeDefinitions['hasSelectableRows'] = false;
       // } else if ($permissions->canDelete()) {
            /*Comment the DELETE COST CENTER MAPPING BUTTON*/
            // $buttons['Delete'] = array('label' => 'Delete',
            //     'type' => 'submit',
            //     'data-toggle' => 'modal',
            //     'data-target' => '#deleteConfModal',
            //     'class' => 'delete');
       // }
        
        $runtimeDefinitions['buttons'] = $buttons;

        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($adminList);
        // ohrmListComponent::setItemsPerPage($limit);
        ohrmListComponent::setNumberOfRecords($recordCount);
    }

    private function _setSearchClues($sortField, $sortOrder, $offset, $limit) {
        return array(
            //'customer' => $this->form->getValue('customer'),
           // 'project' => $this->form->getValue('project'),
            //'projectAdmin' => $this->form->getValue('projectAdmin'),
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'offset' => $offset,
            'limit' => $limit
        );
    }
}

