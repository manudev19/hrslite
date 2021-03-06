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
class editTimesheetAction extends baseTimeAction {

    private $timesheetService;
    private $timesheetPeriodService;
    private $totalRows = 0;
    private $employeeService;

    public function getEmployeeService() {

        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {

        if ($employeeService instanceof EmployeeService) {
            $this->employeeService = $employeeService;
        }
    }

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    public function execute($request) {
       
        $this->listForm = new DefaultListForm();

        $this->backAction = $request->getParameter('actionName');
        $this->timesheetId = $request->getParameter('timesheetId');
        $this->employeeId = $request->getParameter('employeeId');
        $this->empId = $request->getParameter('employeeId');
        $this->employeeName = $this->getEmployeeName( $this->empId );
       // $this->id = $request->getParameter('employeeId');
       $this->timesheetActon=  $request->getParameter("timeSheetAction");
        $loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();
        $selectedTimesheetStartDate = $request->getParameter('timesheetStartDateFromDropDown');
        /* For highlighting corresponding menu item */
        if ($this->employeeId == $loggedInEmpNumber) {
            $request->setParameter('initialActionName', 'viewMyTimesheet');
        } else {
            $request->setParameter('initialActionName', 'viewEmployeeTimesheet');            
        } 
        
        $timesheet = $this->getTimesheetService()->getTimesheetById($this->timesheetId);

        $this->date = $timesheet->getStartDate();
        $this->endDate = $timesheet->getEndDate();
        $this->startDate = $this->date;
        $this->noOfDays = $this->timesheetService->dateDiff($this->startDate, $this->endDate);
        $values = array('date' => $this->startDate, 'employeeId' => $this->employeeId, 'timesheetId' => $this->timesheetId, 'noOfDays' => $this->noOfDays);
        $this->timesheetForm = new TimesheetForm(array(), $values);
        $this->currentWeekDates = $this->timesheetForm->getDatesOfTheTimesheetPeriod($this->startDate, $this->endDate);
        $this->timesheetItemValuesArray = $this->timesheetForm->getTimesheet($this->startDate, $this->employeeId, $this->timesheetId);

        $this->messageData = array($request->getParameter('message[0]'), $request->getParameter('message[1]'));

        if ($this->timesheetItemValuesArray == null) {

            $this->totalRows = 0;
            $this->timesheetForm = new TimesheetForm(array(), $values);
        } else {

            $this->totalRows = sizeOf($this->timesheetItemValuesArray);
            $this->timesheetForm = new TimesheetForm(array(), $values);
        }
        $startDateSelectedFromDropDown = $request->getParameter('timesheetStartDateFromDropDown');


        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets', $this->employeeId);

        $this->_checkAuthentication($this->employeeId);

        if ($this->employeeId == $loggedInEmpNumber) {
            $this->employeeName = $this->getEmployeeName( $_SESSION['empNumber']);
            // $this->employeeName = null;
        } else {
            $this->employeeName = $this->getEmployeeName($this->employeeId);
        }

        $startDateOfTheTimesheetForUpdates = $request->getParameter('timesheetStartDate');
       
        $this->dateForm = new startDaysListForm(array(), array('employeeId' => $this->employeeId));
        $dateOptions = $this->dateForm->getDateOptions();

      

       if (!isset($selectedTimesheetStartDate)) {                                      // admin or the supervisor enters the name of the employee and clicks on the view button
            $startDate = $this->getStartDate($dateOptions);
        } else {

            $startDate = $selectedTimesheetStartDate;

            // this sets the start day as the start date set by the search drop down or the coming back from the edit action
        }

        /* This action is checks whether the start date set. If not the current date is set. */
        if (isset($startDate)) {
            $this->toggleDate = $startDate;
        }

        $this->timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($startDate, $this->employeeId);

        $this->currentState = $this->timesheet->getState();

        if (isset($startDate)) {
            $selectedIndex = $this->dateForm->returnSelectedIndex($this->startDate, $this->employeeId);
        }

        if (isset($selectedIndex)) {
          
            $this->dateForm->setDefault('startDates', $selectedIndex);
        }
      
       
        $workingDays = 6;
        $this->leaveDetails = array();
        for ($count=0 ; $count<= $workingDays ; $count++) {
            $leaveDate = date('Y-m-d',strtotime($this->startDate . "+$count days"));
           
            $leaveInfo = $this->getLeaveRequestService()->searchLeaveForTimesheet($this->employeeId, $leaveDate);
            foreach ($leaveInfo as $leaveRequest) {
                if($leaveRequest){
                   $leaveData['dateApplied']    = $leaveRequest->getFormattedLeaveDateToViewForTimesheet();
                   $leaveData['leaveType']      = $leaveRequest->getLeaveType();
                   $leaveData['status']         = $leaveRequest->getTextLeaveStatus();
                   $leaveData['leavesDuration'] = $leaveRequest->getLengthHours();
                   $this->leaveDetails[]        = $leaveData;
                }
            }
        }

        $workingDays = 6;
        $this->attendanceDetails = array();
        for ($count=0 ; $count<= $workingDays ; $count++) {
            $attendanceDate = date('Y-m-d',strtotime($this->startDate . "+$count days"));

            $attendanceRecord = $this->getAttendanceDao()->getAttendanceRecord($this->employeeId, $attendanceDate);

            foreach ($attendanceRecord as  $attendance) {
                if($attendance){
                    $attendanceData['date']             = $attendance->getLoginDate();
                    $attendanceData['inTime']           = $attendance->getPunchInUserTime();
                    $attendanceData['outTime']          = $attendance->getPunchOutUserTime();
                    $attendanceData['WorkingHrs']       = $attendance->getWorkingHours();
                    $attendanceData['actualWorkingHrs'] = $attendance->getActualWorkingHours();
                    $attendanceData['breakTime']        = $attendance->getBreakTime();
                    $attendanceData['overTime']         = $attendance->getOverTime(); 
                    $attendanceData['status']           = $attendance->getStatus();       
                    $this->attendanceDetails[]          = $attendanceData;

                    $this->breakTimeTotal    = $this->breakTimeTotal  + $this->getTimeStrToMins($attendanceData['breakTime'] )  ;
                    $this->wrkHrsTotal       = $this->wrkHrsTotal  + $this->getTimeStrToMins($attendanceData['WorkingHrs'] )  ;
                    $this->actualWrkHrsTotal = $this->actualWrkHrsTotal  + $this->getTimeStrToMins($attendanceData['actualWorkingHrs'] )  ;
                    $this->overTimeTotal     = $this->overTimeTotal  + $this->getTimeStrToMins($attendanceData['overTime'] )  ;
                } 
            }
        }
        $this->formToImplementCsrfToken = new TimesheetFormToImplementCsrfTokens();


        $userRoleManagers = $this->getContext()->getUserRoleManager();
        $user = $userRoleManagers->getUser();
        // var_dump($userRoleManager);exit;
        $userId = $user->getId();
        $this->createTimesheetForm = new CreateTimesheetForm();

        $excludeRoles = array();
        $includeRoles = array();
       
        if ($loggedInEmpNumber == $this->employeeId && $userRoleManagers->essRightsToOwnWorkflow()) {
            $includeRoles = array('ESS');
        }
                
        $entities = array('Employee' => $this->employeeId);

        $initialStateActions = $userRoleManagers->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, PluginTimesheet::STATE_INITIAL, $excludeRoles, $includeRoles, $entities);
        $this->allowedToCreateTimesheets = isset($initialStateActions[WorkflowStateMachine::TIMESHEET_ACTION_CREATE]);
        

        if ($request->isMethod('post')) {

            if ($request->getParameter('btnSave')) {
                
                if( $this->timesheetForm->getCSRFtoken() == $request->getParameter('_csrf_token')){
                    $backAction = $this->backAction;
                   
                        foreach ($request->getParameter('initialRows') as $inputTimesheetItem) {
                            $activityId = $inputTimesheetItem['projectActivityName'];
                         
                            if ($activityId != null) {
                            
                                $tempArray = array_slice($inputTimesheetItem, 3);
                                for ($i = 0; $i < sizeof( $this->currentWeekDates); $i++) {
                                  
                                   
                                    $date = $keysArray[$i];
                                    $timesheetItemDuration = $inputTimesheetItem[$i];
                                 
                                        if ($timesheetItemDuration == null) {
                                            $timesheetItemDuration = 0;
                                        }
                
                                      $this->convertDurationToSeconds($timesheetItemDuration);
                                    
                              
                                       if( $this->convertDurationToSeconds($timesheetItemDuration)>=86400)
                                       {
                                        $this->getUser()->setFlash('edittimesheet.warning', __('Should Be Less Than 24 and in HH:MM'));
                                         $this->redirect( 'time/editTimesheet?'. http_build_query(array('timesheetId'=> $this->timesheetId, 'employeeId'=> $this->employeeId , 'actionName' =>$backAction)));   
                                       
                                    }  
                                 
                                }
                            }
                        } 
                    $this->getTimesheetService()->saveTimesheetItems($request->getParameter('initialRows'), $this->employeeId, $this->timesheetId, $this->currentWeekDates, $this->totalRows);
                    $this->messageData = array('success', __(TopLevelMessages::SAVE_SUCCESS));
                    
                    $timeSheet = $this->getTimesheetService()->getTimesheetById($this->timesheetId);
                    
                    $resultingState = $this->getResultingState($timeSheet, 
                            PluginWorkflowStateMachine::TIMESHEET_ACTION_MODIFY,
                            $loggedInEmpNumber == $timesheet->getEmployeeId());
                    
                    if ($resultingState != $timeSheet->getState()) {
                        $timesheet->setState($resultingState);
                        $this->getTimesheetService()->saveTimesheet($timesheet);
                    }
                    
                    $startingDate = $timeSheet->getStartDate();
                    $this->redirect('time/' . $backAction . '?' . http_build_query(array('message' => $this->messageData, 'timesheetStartDate' => $startingDate, 'employeeId' => $this->employeeId)));
                 }

            }

            if ($request->getParameter('buttonRemoveRows')) {
                $this->messageData = array('success', __('Successfully Removed'));
            }
        }
    }
    
    protected function _checkAuthentication($empNumber) {

        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();

        if ($loggedInEmpNumber == $empNumber) {
            return;
        }

        $userRoleManager = $this->getContext()->getUserRoleManager();
        if (!$userRoleManager->isEntityAccessible('Employee', $empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

    }
    public function convertDurationToSeconds($duration) {

        $find = ':';
        $pos = strpos($duration, $find);
    
        if ($pos !== false) {
    
            $str_time = $duration;
            sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
            $durationInSeconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 3600 + $minutes * 60;
            return $durationInSeconds;
        } else {
            $durationInSeconds = $duration * 60 * 60;
            return $durationInSeconds;
        }
    }

    private function getEmployeeName($employeeId) {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($employeeId);

        $name = $employee->getFirstName() . " " . $employee->getLastName();

        if ($employee->getTerminationId()) {
            $name = $name . ' (' . __('Past Employee') . ')';
        }

        return $name;
    }
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }
    public function getAttendanceDao() {

        if (is_null($this->attendanceDao)) {

            $this->attendanceDao = new AttendanceDao();
        }

        return $this->attendanceDao;
    }
    public function getTimeStrToMins($timeStr){
        $time = explode(':', $timeStr);
          
           $hours  = $time[0];
           $min =$time[1];
           
           $totalMinutes = ($hours * 60 + $min ) * 60; 
      // var_dump($totalMinutes);
           return $totalMinutes ;

   }
   public function getStartDate($dateOptions) {

    $temp = $dateOptions[0];
    $tempArray = explode(" ", $temp);
    return $tempArray[0];
}

}

