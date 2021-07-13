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
class viewEmployeeTimesheetAction extends baseTimeAction {

    const NUM_PENDING_TIMESHEETS = 100;
    private $employeeNumber;
    private $timesheetService;

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }


     public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

public function execute($request) {
        
        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets');
    
        $this->form = new viewEmployeeTimesheetForm();
        $userRoleManager = $this->getContext()->getUserRoleManager();
                
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $employeeList = UserRoleManagerFactory::getUserRoleManager()
                ->getAccessibleEntityProperties('Employee', $properties);
    
        if ($request->isMethod("post")) {
    
    
            $this->form->bind($request->getParameter('time'));
    
            if ($this->form->isValid()) {
                $emp=$this->form->getValue('employeeName');
                $locationId=$this->form->getValue('location');
               
               if($emp['empName']!='Type for hints...'){
                   if(empty($emp['empId'])){
                    $this->getContext()->getUser()->setFlash('emptimesheet.warning', __('Please enter valid Employee Name/Id'));
                    $this->redirect('time/viewEmployeeTimesheet');
                   }else{
                 $this->employeeId =$emp['empId'];
                 $startDaysListForm = new startDaysListForm(array(), array('employeeId' => $this->employeeId));
                 $dateOptions = $startDaysListForm->getDateOptions();
    
                if ($dateOptions == null) {
                    $this->getContext()->getUser()->setFlash('warning.nofade', __('No Timesheets Found'));
                    $this->redirect('time/createTimesheetForSubourdinate?' . http_build_query(array('employeeId' => $this->employeeId)));
                }
    
                $this->redirect('time/viewTimesheet?' . http_build_query(array('employeeId' => $this->employeeId)));
            }
            }
            else{
                if($locationId !=0){
                foreach( $employeeList as $emp){
                $employee[]= $this->getEmployeeService()->getEmpByLocationIdBasedOnUserRole($locationId, $emp['empNumber']);  
                }
                $employeeList= $employee;
                $this->pendingApprovelTimesheets = $this->getActionableTimesheetsBasedOnUserRole($employeeList);
            }else{
                $this->form->employeeList = $employeeList;
                $this->pendingApprovelTimesheets = $this->getActionableTimesheets($employeeList);
                }
           
            }
        }
    
        }else{
        $this->form->employeeList = $employeeList;
        $this->pendingApprovelTimesheets = $this->getActionableTimesheets($employeeList);
        }
    
    }
    
    public function getActionableTimesheetsBasedOnUserRole($employeeList) {
        $timesheetList = null;
        
        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $action = array(PluginWorkflowStateMachine::TIMESHEET_ACTION_APPROVE, PluginWorkflowStateMachine::TIMESHEET_ACTION_REJECT);
        $actionableStatesList = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, AdminUserRoleDecorator::ADMIN_USER, $action);
        
        $empNumbers = array();
        if ($actionableStatesList != null) {
            $timesheetList = $this->getTimesheetService()->getTimesheetListByEmployeeIdAndState($employeeList, $actionableStatesList,  self::NUM_PENDING_TIMESHEETS);
        }
        return $timesheetList;
    } 

    public function getActionableTimesheets($employeeList) {
        $timesheetList = null;
        
        $accessFlowStateMachinService = new AccessFlowStateMachineService();
        $action = array(PluginWorkflowStateMachine::TIMESHEET_ACTION_APPROVE, PluginWorkflowStateMachine::TIMESHEET_ACTION_REJECT);
        $actionableStatesList = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, AdminUserRoleDecorator::ADMIN_USER, $action);
        
        $empNumbers = array();
        foreach ($employeeList as $employee) {
            $empNumbers[] = $employee['empNumber'];
        }
        if ($actionableStatesList != null) {
            $timesheetList = $this->getTimesheetService()->getTimesheetListByEmployeeIdAndState($empNumbers, $actionableStatesList,  self::NUM_PENDING_TIMESHEETS);
        }
        
        return $timesheetList;
        }  

}

