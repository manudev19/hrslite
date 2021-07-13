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
class viewAttendanceRecordAction extends baseAttendanceAction {

    private $employeeService;
    private $attendanceService;

    public function getEmployeeService() {

        if (is_null($this->employeeService)) {

            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }

    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {

            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
    }

    public function setEmployeeService(EmployeeService $employeeService) {

        $this->employeeService = $employeeService;
    }

    public function execute($request) {

        $loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();
        
        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        $this->parmetersForListCompoment = array();
        $this->showEdit = false;

        $this->attendancePermissions = $this->getDataGroupPermissions('attendance_records');


        if (!$this->attendancePermissions->canRead()) {
            return $this->renderText(__("You are not allowed to view this page") . "!");
        }

        $this->trigger = $request->getParameter('trigger');

        if ($this->trigger) {
            $this->showEdit = true;
        }

        $this->month = $request->getParameter('month');
        $this->year  = $request->getParameter('year');
        $this->employeeId = $request->getParameter('employeeId');
        $this->employeeService = $this->getEmployeeService();
        $values = array('month' => $this->month, 'employeeId' => $this->employeeId) . array('year' => $this->year, 'employeeId' => $this->employeeId);
        $this->form = new AttendanceRecordSearchForm(array(), $values);
        $this->actionRecorder = "viewEmployee";

        $isPaging = $request->getParameter('pageNo');

        $pageNumber = $isPaging;

        $noOfRecords = $noOfRecords = sfConfig::get('app_items_per_page');
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

        $records = array();
        if ($this->attendancePermissions->canRead()) {
            $this->_setListComponent($records, $noOfRecords, $pageNumber, null, $this->showEdit);
        }

        if (!$this->trigger) {

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter('attendance'));
                if ($this->form->isValid()) {
                    $this->allowedToDelete = array();
                    $this->allowedActions = array();

                    $this->allowedActions['Delete'] = false;
                    $this->allowedActions['Edit'] = false;
                    $this->allowedActions['PunchIn'] = false;
                    $this->allowedActions['PunchOut'] = false;

                    $post = $this->form->getValues();
                    if (!$this->employeeId) {
                        $empData = $post['employeeName'];
                        $this->employeeId = $empData['empId'];
                    }
                    if (!$this->month) {
                        $this->month = $post['month'];
                    }
                    if (!$this->year) {
                        $this->year = $post['year'];
                    }

                    if ($this->employeeId) {
                        $this->showEdit = true;
                    }

                    $isPaging = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);

                    $pageNumber = $isPaging;

                    $noOfRecords = sfConfig::get('app_items_per_page');
                    $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

                    $empRecords = array();
                    if (!$this->employeeId) {
                        $empRecords = $this->employeeService->getEmployeeList('firstName', 'ASC', false);
                        $empRecords = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntities('Employee');
                        $count = count($empRecords);
                    } else {
                        $empRecords = $this->employeeService->getEmployee($this->employeeId);
                        $empRecords = array($empRecords);
                        $count = 1;
                    }
                    $records = array();
                    foreach ($empRecords as $employee) {
                        $hasRecords = false;
                        $attendanceRecords = $this->getAttendanceService()->getEmployeeAttendanceRecord($this->employeeId, $this->month, $this->year);
                        $total = 0;

                        if( $attendanceRecords && sizeof($attendanceRecords) > 0 ){
                           $hasRecords = true;
                           $records    = $attendanceRecords;
                        }
                        if ($hasRecords) {
                            $last = end($records);
                        } else {
                            $attendance = new AttendanceRecord();
                            $attendance->setEmployee($employee);
                            $attendance->setTotal('---');
                            $records[] = $attendance;
                        }
                    }
                    
                    $params = array();
                    $this->parmetersForListCompoment = $params;

                    $rolesToExclude = array();
                    $rolesToInclude = array();
                    
                    if ($this->employeeId == $loggedInEmpNumber && $userRoleManager->essRightsToOwnWorkflow()) {
                        $rolesToInclude = array('ESS');
                    }
                    
                    $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
                    $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, $rolesToExclude, $rolesToInclude, array('Employee' => $this->employeeId));
                    $recArray = array();

                    if ($records != null) {
                        if ($actionableStates != null) {
                            foreach ($actionableStates as $state) {
                                foreach ($records as $record) {
                                    if ($state == $record->getState()) {
                                            $this->allowedActions['Edit'] = true;
                                        break;
                                    }
                                }
                            }
                        }

                        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE);
                        $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, $rolesToExclude, $rolesToInclude, array('Employee' => $this->employeeId));

                        if ($actionableStates != null) {
                            foreach ($actionableStates as $state) {
                                foreach ($records as $record) {
                                    if ($state == $record->getState()) {
                                            $this->allowedActions['Delete'] = true;
                                        break;
                                    }
                                }
                            }
                        }

                        foreach ($records as $record) {
                            $this->allowedToDelete[] = $userRoleManager->isActionAllowed(WorkflowStateMachine::FLOW_ATTENDANCE, $record->getState(), PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE, array(), array(), array('Employee' => $this->employeeId));
                            $recArray[] = $record;
                        }
                    } else {
                        $attendanceRecord = null;
                    }

                    /** 
                     * TODO: Following code looks overly complicated. Simplify
                     */
                    $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
                    $allowedActionsList = array();
                    $actionableStates = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, $rolesToExclude, $rolesToInclude, array('Employee' => $this->employeeId));

                    if ($actionableStates != null) {
                        if (!empty($recArray)) {
                            $lastRecordPunchOutTime = $recArray[count($records) - 1]->getPunchOutUserTime();
                            if (empty($lastRecordPunchOutTime)) {
                                $attendanceRecord = "";
                            } else {
                                $attendanceRecord = null;
                            }
                        }

                        foreach ($actionableStates as $actionableState) {
  
                            $allowedActionsArray = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, 
                                $actionableState, array(), array(), array('Employee' => $this->employeeId));
                            
                            if (!is_null($allowedActionsArray)) {

                                $allowedActionsList = array_unique(array_merge(array_keys($allowedActionsArray), $allowedActionsList));
                            }
                        }

                        if ((is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, $allowedActionsList))) {
                                $this->allowedActions['PunchIn'] = true;
                        }
                        if ((!is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT, $allowedActionsList))) {
                                $this->allowedActions['PunchOut'] = true;
                        }
                    }
                    if ($this->employeeId == '') {
                        $this->showEdit = FALSE;
                    }
                    
                    $this->_setListComponent($records, $noOfRecords, $pageNumber, $count, $this->showEdit, $this->allowedActions);

                    if( $request->getParameter('hdnAction') == 'download') {
                        $this->downloadReport();         
                        return sfView::NONE;
                    }
                }
            }
        }
    }


    protected function employeeAttendanceDetails($employeeId, $month, $year) {
        $attendanceService = new AttendanceService();
        $result = $attendanceService->getEmployeeAttendanceRecord($this->employeeId, $this->month, $this->year);
        return $result;
    }

    protected function downloadReport() {
        $list = $this->employeeAttendanceDetails($this->employeeId, $this->month, $this->year);
       
        $response = $this->getResponse();
        $response->setHttpHeader('Pragma', 'public');
        $response->setHttpHeader("Content-type", "application/csv");
        $response->setHttpHeader("Content-Disposition", "attachment; filename=AttendanceReport.csv");
        $response->setHttpHeader('Expires', '0');
        $content = "Date,Shift,In Time,Out Time,Working Hours,Over Time,Break Time,Actual Working Hours,Status\n";
           
        foreach ($list as $attendanceRequest) {
            $loginDate             = $attendanceRequest->getLoginDate();
            $getShift              = $attendanceRequest->getShift();
            $getPunchInUserTime    = $attendanceRequest->getPunchInUserTime();
            $getPunchOutUserTime   = $attendanceRequest->getPunchOutUserTime();
            $getWorkingHours       = $attendanceRequest->getWorkingHours();
            $getOverTime           = $attendanceRequest->getOverTime();
            $getBreakTime          = $attendanceRequest->getBreakTime();
            $getActualWorkingHours = $attendanceRequest->getActualWorkingHours();
            $getStatus             = $attendanceRequest->getStatus();
            $content .= "$loginDate, $getShift, $getPunchInUserTime, $getPunchOutUserTime, $getWorkingHours, $getOverTime, $getBreakTime, $getActualWorkingHours, $getStatus";
        }

        $response->setHttpHeader("Content-Length", strlen($content));
        $response->setContent($content);
    } 

    private function _setListComponent($records, $noOfRecords, $pageNumber, $count = null, $showEdit = null, $allowedActions = null) {

        $configurationFactory = new AttendanceRecordHeaderFactory();
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();

        $notSelectable = array();
        foreach ($records as $record) {
            if (!$userRoleManager->isActionAllowed(WorkflowStateMachine::FLOW_ATTENDANCE, 
                    $record->getState(), WorkflowStateMachine::ATTENDANCE_ACTION_DELETE, 
                    array(), array(), array('Employee' => $this->employeeId))) {          
                $notSelectable[] = $record->getId();
            }
        }

        $buttons = array();
        $canSelect = false;
        if (isset($allowedActions)) {
            if (isset($showEdit) && $showEdit) {
                if ($allowedActions['Edit']) :
                    $buttons['Edit'] = array('label' => __('Edit'), 'type' => 'button',);
                endif;
                if ($allowedActions['PunchIn']) :
                endif;
                if ($allowedActions['PunchOut']) :
                endif;
            }
            if ($allowedActions['Delete']) :
                $canSelect = true;
                $buttons['Delete'] = array('label' => __('Delete'),
                    'type' => 'submit',
                    'data-toggle' => 'modal',
                    'data-target' => '#dialogBox',
                    'class' => 'delete');
            endif;
        }
        $configurationFactory->setRuntimeDefinitions(array(
            'buttons' => $buttons,
            'unselectableRowIds' => $notSelectable,
            'hasSelectableRows' => $canSelect
        ));

        ohrmListComponent::setActivePlugin('orangehrmAttendancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($records);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($count);
    }
    
}

