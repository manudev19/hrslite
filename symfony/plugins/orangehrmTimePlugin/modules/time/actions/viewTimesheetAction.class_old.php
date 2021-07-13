<?php
include_once 'SunHRM/symfony/apps/orangehrm/lib/model/core/Service/EmailService.php';

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
class viewTimesheetAction extends baseTimeAction {

    private $timesheetService;
    private $timesheetPeriodService;
    private $timesheetActionLog;
    private $employeeService;

    public function getEmployeeService() {

        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }
    
    public function getEmailService() {
        if (empty($this->emailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }
   
    public function setEmployeeService($employeeService) {

        if ($employeeService instanceof EmployeeService) {
            $this->employeeService = $employeeService;
        }
    }

    public function getAttendanceDao() {

        if (is_null($this->attendanceDao)) {

            $this->attendanceDao = new AttendanceDao();
        }

        return $this->attendanceDao;
    }

    public function execute($request) {
        /* For highlighting corresponding menu item */  
        $request->setParameter('initialActionName', 'viewEmployeeTimesheet');  

        $employeeId = $request->getParameter('employeeId');
        $this->empId = $request->getParameter('employeeId');
        $this->timesheetActon=  $request->getParameter("timeSheetAction");
        
        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();
                    
        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets', $employeeId);

        $this->_checkAuthentication($employeeId);

        $userRoleManager = $this->getContext()->getUserRoleManager();
        $user = $userRoleManager->getUser();
        $userId = $user->getId();
        
        $this->employeeName = $this->getEmployeeName($employeeId);
        
        $this->createTimesheetForm = new CreateTimesheetForm();
        $this->currentDate = date('Y-m-d');

        $this->headingText = $this->getTimesheetPeriodService()->getTimesheetHeading();
        $this->successMessage = array($request->getParameter('message[0]'), $request->getParameter('message[1]'));
        $this->timeService = $this->getTimesheetService();

        /* This action is called from viewTimesheetAction, when the user serches a previous timesheet, if not finds a start date from
         * back btn from editTimesheet. */

        $selectedTimesheetStartDate = $request->getParameter('timesheetStartDateFromDropDown');
        if (!isset($selectedTimesheetStartDate)) {
            $selectedTimesheetStartDate = $request->getParameter('timesheetStartDate');
        }

        $this->actionName = $this->getActionName();
        $this->format = $this->getTimesheetService()->getTimesheetTimeFormat();


        /* Error message when there is no timesheet to view */
        if ($this->getContext()->getUser()->hasFlash('errorMessage')) {

            $this->messageData = array('warning', __($this->getContext()->getUser()->getFlash('errorMessage')));
        } else {


            $this->dateForm = new startDaysListForm(array(), array('employeeId' => $employeeId));
            $dateOptions = $this->dateForm->getDateOptions();

            if ($dateOptions == null) {

                $this->messageData = array('warning', __("No Accessible Timesheets"));
            }

            if ($this->getContext()->getUser()->hasFlash('TimesheetStartDate')) {                 //this is admin or supervisor accessing the viewTimesheet from by clicking the "view" button
                $startDate = $this->getContext()->getUser()->getFlash('TimesheetStartDate');
            } elseif (!isset($selectedTimesheetStartDate)) {                                      // admin or the supervisor enters the name of the employee and clicks on the view button
                $startDate = $this->getStartDate($dateOptions);
            } else {

                $startDate = $selectedTimesheetStartDate;

                // this sets the start day as the start date set by the search drop down or the coming back from the edit action
            }

            /* This action is checks whether the start date set. If not the current date is set. */
            if (isset($startDate)) {
                $this->toggleDate = $startDate;
            }
        
            $this->timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);

            $this->currentState = $this->timesheet->getState();

            if (isset($startDate)) {
                $selectedIndex = $this->dateForm->returnSelectedIndex($startDate, $employeeId);
            }

            if (isset($selectedIndex)) {
                $this->dateForm->setDefault('startDates', $selectedIndex);
            }

            $noOfDays = $this->timesheetService->dateDiff($this->timesheet->getStartDate(), $this->timesheet->getEndDate());
            $values = array('date' => $startDate, 'employeeId' => $employeeId, 'timesheetId' => $this->timesheet->getTimesheetId(), 'noOfDays' => $noOfDays);
            $form = new TimesheetForm(array(), $values);
            $this->timesheetRows = $form->getTimesheet($startDate, $employeeId, $this->timesheet->getTimesheetId());
          
            $workingDays = 6;
            $this->leaveDetails = array();
            for ($count=0 ; $count<= $workingDays ; $count++) {
                $leaveDate = date('Y-m-d',strtotime($startDate . "+$count days"));
               
                $leaveInfo = $this->getLeaveRequestService()->searchLeaveForTimesheet($employeeId, $leaveDate);
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
               
                $attendanceDate = date('Y-m-d',strtotime($startDate . "+$count days"));
             
                $attendanceRecord = $this->getAttendanceDao()->getAttendanceRecord($employeeId, $attendanceDate);
               
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
            if ($request->isMethod('post')) {
                $this->formToImplementCsrfToken->bind($request->getParameter('time'));

                if ($this->formToImplementCsrfToken->isValid()) {

                    $action = $request->getParameter('act');

                    // check if action allowed and get next state
                    $excludeRoles = array();
                    $includeRoles = array();
                    
                    if ($loggedInEmpNumber == $employeeId && $userRoleManager->essRightsToOwnWorkflow()) {
                        $includeRoles = array('ESS');
                    }
             
                    $entities = array('Employee' => $employeeId);
                                           
                    $allowedActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, $excludeRoles, $includeRoles, $entities);
                    
                    if (isset($allowedActions[$action])) {
                        
                        $state = $allowedActions[$action]->getResultingState();
                        
                        $this->successMessage = array('success', __("Timesheet " . ucwords(strtolower($state))));
                        
                        $comment = $request->getParameter('Comment');
                        $this->timesheet->setState($state);
                        $this->timesheet = $this->getTimesheetService()->saveTimesheet($this->timesheet);
                        if($state=="REJECTED"){
                            $headName = $user->getName();
                            $comment = $request->getParameter('Comment');
                            
                            $employeeService = new EmployeeService();
                            $employee = $employeeService->getEmployee($employeeId);
                            $employeeName = $employee->firstName;
                            $empWorkEmail = $employee->emp_work_email;
                             
                            $endDate = date('Y-m-d',strtotime($startDate.' + 6 days'));
                            $emailData =  array('recipientFirstName' => $employeeName, 'StartDate' => $startDate, 'status'=> $state,
                                        'headName'=>$headName, 'enddate'=>$endDate, 'comment'=> $comment);
                            $this->getEmailService()->sendEmailUsingTemplate('rejectedTimesheet' , $emailData , $empWorkEmail);
                        }

                        if ($request->getParameter('updateActionLog')) {
                             $comment = $request->getParameter('Comment');

                            if ($action == WorkflowStateMachine::TIMESHEET_ACTION_RESET) {

                                $this->setTimesheetActionLog(Timesheet::RESET_ACTION, $comment, $this->timesheet->getTimesheetId(), $userId);
                                
                            } else {
                                $this->setTimesheetActionLog($state, $comment, $this->timesheet->getTimesheetId(), $userId);
                            }
                            
                            if ($action == WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT) {
                                $this->successMessage = array('success', __("Timesheet Submitted"));
                            }
                            
                        }
                    }                    
                }
            }

            $this->currentState = $this->timesheet->getState();
            
            $excludeRoles = array();
            $includeRoles = array();
            if ($loggedInEmpNumber == $employeeId && $userRoleManager->essRightsToOwnWorkflow()) {
                $includeRoles = array('ESS');
            }
                    
            $entities = array('Employee' => $employeeId);
                                           
            $initialStateActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, PluginTimesheet::STATE_INITIAL, $excludeRoles, $includeRoles, $entities);
            $this->allowedToCreateTimesheets = isset($initialStateActions[WorkflowStateMachine::TIMESHEET_ACTION_CREATE]);
            
            $this->allowedActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, $excludeRoles, $includeRoles, $entities);
            
            $this->rowDates = $form->getDatesOfTheTimesheetPeriod($this->timesheet->getStartDate(), $this->timesheet->getEndDate());
            $this->actionLogRecords = $this->getTimesheetService()->getTimesheetActionLogByTimesheetId($this->timesheet->getTimesheetId());
           
         
            if($this->timesheet->getState() == "NOT SUBMITTED" &&  $this->timesheetRows ==null){
              
                $this->redirect( 'time/editTimesheet?'. http_build_query(array('timesheetId'=>$this->timesheet->getTimesheetId(), 'employeeId'=> $employeeId , 'actionName' => $this->actionName,'timeSheetAction'=>  $this->timesheetActon,'startdate'=>$startDate)));
                }

        }
    }

    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }

    public function getTimeStrToMins($timeStr){
         $time = explode(':', $timeStr);
           
            $hours  = $time[0];
            $min =$time[1];
            
            $totalMinutes = ($hours * 60 + $min ) * 60; 
       // var_dump($totalMinutes);
            return $totalMinutes ;

    }
    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function getTimesheetActionLog() {

        if (is_null($this->timesheetActionLog)) {

            $this->timesheetActionLog = new TimesheetActionLog();
          
        }

        return $this->timesheetActionLog;
    }

    public function setTimesheetActionLog($state, $comment, $timesheetId, $employeeId) {

        $timesheetActionLog = $this->getTimesheetActionLog();
        $timesheetActionLog->setAction($state);
        $timesheetActionLog->setComment($comment);
        $timesheetActionLog->setTimesheetId($timesheetId);
        $timesheetActionLog->setDateTime(date("Y-m-d"));
        $timesheetActionLog->setPerformedBy($employeeId);

               
        $this->getTimesheetService()->saveTimesheetActionLog($timesheetActionLog);
    }

    public function getStartDate($dateOptions) {

        $temp = $dateOptions[0];
        $tempArray = explode(" ", $temp);
        return $tempArray[0];
    }

    public function getEmployeeName($employeeId) {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($employeeId);

        $name = $employee->getFirstName() . " " . $employee->getLastName();

        if ($employee->getTerminationId()) {
            $name = $name . ' ('. __('Past Employee') . ')';
        }

        return $name;
    }

    protected function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    protected function _checkAuthentication($empNumber) {

        $userRoleManager = $this->getContext()->getUserRoleManager();

        if (!$userRoleManager->isEntityAccessible('Employee', $empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

    }

}
