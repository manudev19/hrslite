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
class ExpenseService {

    // Timesheet Data Access Object
    private $ExpenseDao;
    private $employeeDao;
    private $timesheetPeriodService;
    
    // Cache timesheet time format for better performance.
    private static $timesheetTimeFormat = null;

    /**
     * Get the Timesheet Data Access Object
     * @return TimesheetDao
     */
    public function getExpenseDao() {

        if (is_null($this->ExpenseDao)) {
            $this->ExpenseDao = new ExpenseDao();
        }
        return $this->ExpenseDao;
    }


    public function getExpenseAttachmentById($attachmentId) {

        return $this->getExpenseDao()->getExpenseAttachment($attachmentId);
    }
    /**
     * Set TimesheetData Access Object
     * @param TimesheetDao $TimesheetDao
     * @return void
     */
    public function setTimesheetDao(TimesheetDao $timesheetDao) {

        $this->timesheetDao = $timesheetDao;
    }

    /**
     * Set EmployeeData Access Object
     * @param EmployeeDao $employeeDao
     * @return void
     */
    public function setEmployeeDao(EmployeeDao $employeeDao) {

        $this->employeeDao = $employeeDao;
    }

    /**
     * Get the Employee Data Access Object
     * @return EmployeeDao
     */
    public function getEmployeeDao() {

        if (is_null($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {
            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    public function setTimesheetPeriodDao(TimesheetPeriodService $timesheetPeriodService) {

        $this->timesheetPeriodService = $timesheetPeriodService;
    }

    /**
     * Add, Update Timesheet
     * @param Timesheet $timesheet
     * @return boolean
     */
    public function saveTimesheet(Timesheet $timesheet) {

        return $this->getExpenseDao()->saveTimesheet($timesheet);
    }

    /**
     * Get Timesheet by given timesheetId
     * @param int $timesheetId
     * @return Timesheet $timesheet
     */
    public function getTimesheetById($timesheetId) {

        $timesheet = $this->getExpenseDao()->getTimesheetById($timesheetId);

        if (!$timesheet instanceof Timesheet) {
            $timesheet = new Timesheet();
        }

        return $timesheet;
    }

    /**
     * Get Timesheet by given Start Date
     * @param int $startDate
     * @return Timesheet $timesheet
     */
    public function getTimesheetByStartDate($startDate) {

        $timesheet = $this->getExpenseDao()->getTimesheetByStartDate($startDate);

        return $timesheet;
    }

    /**
     * Get TimesheetItem by given Id
     * @param int $timesheetItemId
     * @return TimesheetItem $timesheetItem
     */
    public function getTimesheetItemById($timesheetItemId) {

        $timesheetItem = $this->getExpenseDao()->getTimesheetItemById($timesheetItemId);

        return $timesheetItem;
    }

    /**
     * Get Timesheet by given Start Date and Employee Id
     * @param $startDate , int $employeeId
     * @return Timesheet $timesheet
     */
    public function getTimesheetByStartDateAndEmployeeId($startDate, $employeeId) {

        $timesheet = $this->getExpenseDao()->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);

        return $timesheet;
    }

    public function getTimesheetByEmployeeId($employeeId) {

        return $this->getExpenseDao()->getTimesheetByEmployeeId($employeeId);
    }

    /**
     * Get Timesheet by given Employee Id and state list
     * @param $employeeId, $stateList
     * @return Timesheet $timesheet
     */
    public function getTimesheetByEmployeeIdAndState($employeeId, $stateList) {

        return $this->getExpenseDao()->getTimesheetByEmployeeIdAndState($employeeId, $stateList);
    }

    /**
     * Return an Array of Timesheets for given Employee Ids and States
     * 
     * <pre>
     * Ex: $employeeIdList = array('1', '2')
     *     $stateList = array('SUBMITTED', 'ACCEPTED');
     * 
     * For above $employeeIdList and $stateList parameters there will be an array like below as the response.
     *
     * array(
     *          0 => array('timesheetId' => 2, 'timesheetStartday' => '2011-04-22', 'timesheetEndDate' => '2011-04-19', 'employeeId' => 2, 'employeeFirstName' => 'Kayla', 'employeeLastName' => 'Abay'),
     *          1 => array('timesheetId' => 8, 'timesheetStartday' => '2011-04-22', 'timesheetEndDate' => '2011-04-28', 'employeeId' => 1, 'employeeFirstName' => 'John', 'employeeLastName' => 'Dunion')
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Array $employeeIdList Array of Employee Ids
     * @param Array $stateList Array of States
     * @param $limit Number of Timesheets return
     * @return Array of Timesheets
     */
    public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit) {
        return $this->getExpenseDao()->getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit);
    }
    
    public function getStartAndEndDatesList($employeeId) {

        $resultArray = $this->getExpenseDao()->getStartAndEndDatesList($employeeId);

        return $resultArray;
    }

    /**
     * Add or Save TimesheetActionLog
     * @param Timesheet $timesheet
     * @return boolean
     */
    public function saveTimesheetActionLog(TimesheetActionLog $timesheetActionLog) {

        return $this->getExpenseDao()->saveTimesheetActionLog($timesheetActionLog);
    }

    /**
     * Get TimesheetActionLog
     * @param TimesheetId $timesheetId
     * @return
     */
    public function getTimesheetActionLogByTimesheetId($timesheetId) {

        return $this->getExpenseDao()->getTimesheetActionLogByTimesheetId($timesheetId);
    }

    public function saveExpenseItems($expenseId,$inputExpenseItem, $employeeId, $initialRows) {
        
        $dateOfExpense = $inputExpenseItem['Date']; 
        $expenseType = $inputExpenseItem['expense_type'];
        $message = $inputExpenseItem['message']; 
        $paidByCompany = $inputExpenseItem['paid_by_company']; 
        $amount = $inputExpenseItem['amount']; 
        if($inputExpenseItem['noAttachment']== 1){
            $noAttachment = 1;
        }else
        {
            $noAttachment = null; 
        }
        $currency = $inputExpenseItem['currency'];
        // var_dump($noAttachment. ' '. $currency);
        $newExpenseItem = new ExpenseItem();
        $newExpenseItem->setExpenseId($expenseId);
        $newExpenseItem->setExpenseType($expenseType);
        $newExpenseItem->setDateOfExpense($dateOfExpense);
        $newExpenseItem->setMessage($message);
        $newExpenseItem->setPaidByCompany($paidByCompany);
        $newExpenseItem->setAmount($amount);
        $newExpenseItem->setCurrency($currency);
        $newExpenseItem->setEmployeeId($employeeId);
        $newExpenseItem->setNoAttachment($noAttachment);


        $x = $this->getExpenseDao()->saveExpenseItem($newExpenseItem);
        return $x;
    }                 


    public function saveExpenseAttachments($expenseId, $fileName, $fileType, $fileSize, $fileContent, $itemId)
    {
        $newExpenseAttachment = new ExpenseAttachment();
        $newExpenseAttachment->setExpenseId($expenseId);
        $newExpenseAttachment->setFileName($fileName);
        $newExpenseAttachment->setFileType($fileType);
        $newExpenseAttachment->setFileSize($fileSize);
        $newExpenseAttachment->setFileContent($fileContent);
        $newExpenseAttachment->setExpenseItemId($itemId);
        $this->getExpenseDao()->saveExpenseAttachment($newExpenseAttachment);


    }

    public function deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId) {


        return $this->getExpenseDao()->deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId);
    }

    /**
     * get pending approvel timesheets
     * @param
     * @return supervispr approved timesheets array
     */
    public function getPendingApprovelTimesheetsForAdmin() {

        return $this->getExpenseDao()->getPendingApprovelTimesheetsForAdmin();
    }

    public function getTimesheetTimeFormat() {
        if (is_null(self::$timesheetTimeFormat)) {
            self::$timesheetTimeFormat = $this->getExpenseDao()->getTimesheetTimeFormat();
        }
        return self::$timesheetTimeFormat;
    }

    public function convertDurationToHours($durationInSecs) {

        $timesheetTimeFormat = $this->getTimesheetTimeFormat();

        if ($timesheetTimeFormat == '1') {

            $padHours = false;
            $hms = "";
            $hours = intval(intval($durationInSecs) / 3600);
            $hms .= ( $padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' : $hours . ':';
            $minutes = intval(($durationInSecs / 60) % 60);
            $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
            return $hms;
        } elseif ($timesheetTimeFormat == '2') {

            $durationInHours = number_format($durationInSecs / (60 * 60), 2, '.', '');
            return $durationInHours;
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

    public function getActivityByActivityId($activityId) {

        $activity = $this->getExpenseDao()->getActivityByActivityId($activityId);

        return $activity;
    }

    function addConvertTime($initialTime, $timeToAdd) {

        $old = explode(":", $initialTime);
        $play = explode(":", $timeToAdd);


        $hours = $old[0] + $play[0];

        $minutes = $old[1] + $play[1];

        if ($minutes > 59) {
            $minutes = $minutes - 60;
            $hours++;
        }
        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }
        if ($minutes == 0) {
            $minutes = "00";
        }
        $sum = $hours . ":" . $minutes;
        return $sum;
    }

    function dateDiff($start, $end) {

        $start_ts = strtotime($start);
        $end_ts = strtotime($end);
        $diff = $end_ts - $start_ts;
        return round($diff / 86400) + 1;
    }

    public function getProjectList() {

        return $this->getExpenseDao()->getProjectList();
    }

    public function getProjectListForValidation() {

        return $this->getExpenseDao()->getProjectListForValidation();
    }
    
    /**
     * Return an Array of Project Names
     * 
     * <pre>
     * This will return an array like below as the response.
     *
     * array(
     *          0 => array('projectId' => 1, 'projectName' => 'UB', 'customerName' => 'University of Belize')
     *          1 => array('projectId' => 2, 'projectName' => 'KM2', 'customerName' => 'KM2 Solutions')
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Boolean $excludeDeletedProjects Exclude deleted projects or not
     * @param String $orderField Sort order field
     * @param String $orderBy Sort order
     * @return Array of Project Names
     */
    public function getProjectNameList($excludeDeletedProjects = true, $orderField = 'project_id', $orderBy = 'ASC') {
        return $this->getExpenseDao()->getProjectNameList($excludeDeletedProjects, $orderField, $orderBy);
    }

    /**
     * Return an Array of Project Activities by Project Id
     * 
     * <pre>
     * Ex: $projectId = 1
     *     $excludeDeletedActivities = true;
     * 
     * For above $projectId and $excludeDeletedActivities parameters there will be an array like below as the response.
     *
     * array(
     *          0 => array('activityId' => 1, 'projectId' => 1, 'is_deleted' => 0, 'name' => 'Development')
     * )
     * </pre>
     * 
     * @version 2.7.1
     * @param Integer $projectId Project Id
     * @param Boolean $excludeDeletedActivities Exclude Deleted Project Activities or not
     * @return Array of Project Activities
     */
    public function getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities = true) {
        return $this->getExpenseDao()->getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities);
    }
    
    public function getLatestTimesheetEndDate($employeeId) {

        return $this->getExpenseDao()->getLatestTimesheetEndDate($employeeId);
    }

    public function checkForOverlappingTimesheets($startDate, $endDate, $employeeId) {

        return $this->getExpenseDao()->checkForOverlappingTimesheets($startDate, $endDate, $employeeId);
    }

    public function checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate) {

        return $this->getExpenseDao()->checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate);
    }

    public function createPreviousTimesheets($currentTimesheetStartDate, $employeeId) {

        // this method is for creating past timesheets.This would get conflicted if the user changes the timesheet period and does not loging to the system for couple of weeks



        $previousTimesheetEndDate = mktime(0, 0, 0, date("m", strtotime($currentTimesheetStartDate)), date("d", strtotime($currentTimesheetStartDate)) - 1, date("Y", strtotime($currentTimesheetStartDate)));
        $datesInTheCurrentTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod(date("Y-m-d", $previousTimesheetEndDate));

        $timesheetStartingDate = $datesInTheCurrentTimesheetPeriod[0];
        $endDate = end($datesInTheCurrentTimesheetPeriod);




        if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 1) {


            $accessFlowStateMachineService = new AccessFlowStateMachineService();
            $tempNextState = $accessFlowStateMachineService->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, Timesheet::STATE_INITIAL, "SYSTEM", WorkflowStateMachine::TIMESHEET_ACTION_CREATE);
            $timesheet = new Timesheet();
            $timesheet->setState($tempNextState);
            $timesheet->setStartDate($timesheetStartingDate);
            $timesheet->setEndDate($endDate);
            $timesheet->setEmployeeId($employeeId);
            $timesheet = $this->saveTimesheet($timesheet);
            //create Timesheet

            $this->createPreviousTimesheets($timesheetStartingDate, $employeeId);
        }
    }

    public function createTimesheet($employeeId, $currentDate) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($currentDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        $timesheet = $this->getTimesheetByStartDateAndEmployeeId($timesheetStartingDate, $employeeId);
        if ($timesheet == null) {


            if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 0) {
                if ($this->checkForMatchingTimesheetForCurrentDate($employeeId, date('Y-m-d')) == null) {
                    //state 1 is given when timehseet is overlapping + mathcing timesheet is not found
                    $statusValuesArray['state'] = 1;
                } else {
                    $currentDatesTimesheet = $this->checkForMatchingTimesheetForCurrentDate($employeeId, date('Y-m-d'));
                    $timesheetStartingDate = $currentDatesTimesheet->getStartDate();
                    //state 2 is given when the matching timesheet is found
                    $statusValuesArray['state'] = 2;
                    $statusValuesArray['message'] = $timesheetStartingDate;
                }
            } else {


                $accessFlowStateMachineService = new AccessFlowStateMachineService();
                $tempNextState = $accessFlowStateMachineService->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, Timesheet::STATE_INITIAL, "SYSTEM", WorkflowStateMachine::TIMESHEET_ACTION_CREATE);
                $timesheet = new Timesheet();
                $timesheet->setState($tempNextState);
                $timesheet->setStartDate($timesheetStartingDate);
                $timesheet->setEndDate($endDate);
                $timesheet->setEmployeeId($employeeId);
                $timesheet = $this->saveTimesheet($timesheet);
                //state 3 is given when the new timesheet is created
                $statusValuesArray['state'] = 3;
                $statusValuesArray['message'] = $timesheetStartingDate;
            }
        } else {

            //state 4 is given for when there is no timesheets to access
            $statusValuesArray['state'] = 4;
            $statusValuesArray['message'] = $timesheetStartingDate;
        }
        return $statusValuesArray;
    }

    public function createTimesheets($startDate, $employeeId) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        $timesheet = $this->getTimesheetByStartDateAndEmployeeId($timesheetStartingDate, $employeeId);
        if ($timesheet == null) {
            if ($this->checkForOverlappingTimesheets($timesheetStartingDate, $endDate, $employeeId) == 0) {

                $statusValuesArray['state'] = 1;
            } else {


                $accessFlowStateMachineService = new AccessFlowStateMachineService();
                $tempNextState = $accessFlowStateMachineService->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, Timesheet::STATE_INITIAL, "SYSTEM", WorkflowStateMachine::TIMESHEET_ACTION_CREATE);
                $timesheet = new Timesheet();
                $timesheet->setState($tempNextState);
                $timesheet->setStartDate($timesheetStartingDate);
                $timesheet->setEndDate($endDate);
                $timesheet->setEmployeeId($employeeId);
                $timesheet = $this->saveTimesheet($timesheet);
                $statusValuesArray['state'] = 2;
                $statusValuesArray['startDate'] = $timesheetStartingDate;
            }
        } else {
            $statusValuesArray['state'] = 3;
        }
        return $statusValuesArray;
    }

    public function validateStartDate($startDate) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];


        if ($timesheetStartingDate == $startDate) {

            return true;
        } else {

            return false;
        }
    }

    public function returnEndDate($startDate) {

        $datesInTheCurrenTimesheetPeriod = $this->getTimesheetPeriodService()->getDefinedTimesheetPeriod($startDate);
        $timesheetStartingDate = $datesInTheCurrenTimesheetPeriod[0];
        $endDate = end($datesInTheCurrenTimesheetPeriod);
        
        return $endDate;
    }
    
    /**
     *
     * @param array/Integer $employeeIds
     * @param date $dateFrom
     * @param date $dateTo
     * @param int $subDivision
     * @param String $employeementStatus 
     * @return array
     */
    public function searchTimesheetItems($employeeIds = null, $employeementStatus = null, $subDivision = null,$supervisorId = null, $dateFrom = null , $dateTo = null ){

        if(!is_array($employeeIds) && $employeeIds != null ){
            $employeeIds = array($employeeIds);
        }
        
        $employeeService = new EmployeeService();
        $subordinates = $employeeService->getSubordinateListForEmployee($supervisorId);
        
        $supervisorIds = array();
        foreach($subordinates as $subordinate){           
            $supervisorIds [] = $subordinate->getSubordinateId();
        }
        
        return $this->getExpenseDao()->searchTimesheetItems($employeeIds, $employeementStatus, $supervisorIds,  $subDivision, $dateFrom, $dateTo );
    }

     /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimeSheetsByDepartmentAndStatus
    *
    */
     public function getTimeSheetsByDepartmentAndStatus($selectedDepartment, $selectedMonth, $selectedYear, $selectedStatus, $limit, $offset)
     {

        return $this->getExpenseDao()->getTimeSheetsByDepartmentAndStatus($selectedDepartment, $selectedMonth, $selectedYear, $selectedStatus, $offset, $limit);
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimeSheetsByDepartmentAndStatusDateRange
    *
    */
    public function getTimeSheetsByDepartmentAndStatusDateRange($selectedDepartment, $fromDate, $toDate, $selectedStatus, $limit, $offset)
    {
        return $this->getExpenseDao()->getTimeSheetsByDepartmentAndStatusDateRange($selectedDepartment, $fromDate, $toDate, $selectedStatus, $offset, $limit);
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimeSheetOfEmployee
    * Purpose : To fetch all the time sheets of the selected Employee
    */
    public function getTimeSheetOfEmployee($employeeId, $fromDate, $toDate, $selectedStatus, $limit, $offset)
    {
        return $this->getExpenseDao()->getTimeSheetOfEmployee($employeeId, $fromDate, $toDate, $selectedStatus, $limit, $offset);
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimesheetForDeparmtmentLead
    *
    */
    public function getTimesheetForDeparmtmentLead($selectedDepartment, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $limit, $offset)
    {

        return $this->getExpenseDao()->getTimesheetForDeparmtmentLead($selectedDepartment, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $offset, $limit);
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimeSheetOfEmployeeForDepartmentLead
    * Purpose : To fetch all the time sheets of the selected Employee
    */
    public function getTimeSheetOfEmployeeForDepartmentLead($employeeId, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $limit, $offset)
    {

        return $this->getExpenseDao()->getTimeSheetOfEmployeeForDepartmentLead($employeeId, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $limit, $offset);
    }

    public function getExpense($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$selectedDepartment,$limit,$offset)
    {
        return $this->getExpenseDao()->getExpense($employeeId, $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$selectedDepartment,$limit,$offset);
    }
    public function viewAllForSup($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$selectedDepartment,$limit,$offset)
    {
        return $this->getExpenseDao()->viewAllForSup($employeeId, $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$selectedDepartment,$limit,$offset);
    }
    

    public function getEmpNameForEmail($expenseId){
        return $this->getExpenseDao()->getEmpNameForEmail($expenseId);
    }
    
    public function getCCAdminList($expenseReporterLocationId){
        return $this->getExpenseDao()->getCCAdminList($expenseReporterLocationId);
    }

     public function getSupDetailForFinaceApproval($expenseId){
        return $this->getExpenseDao()->getSupDetailForFinaceApproval($expenseId);
    }

     public function getSubmitedExpenseDate($expenseId)
    {
        return $this->getExpenseDao()->getSubmitedExpenseDate($expenseId);
    }
    
    public function getExpenseItem($expenseId)
    {
        return $this->getExpenseDao()->getExpenseItem($expenseId);
    }

    public function getExpenseForEmp($empId,$limit,$offset)
    {
        
        return $this->getExpenseDao()->getExpenseForEmp($empId,$limit,$offset);
    }

    public function managerApprovedExpenseForFinance($employeeId, $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$empHeadLocId, $selectedDepartment,$limit,$offset)
    {

        return $this->getExpenseDao()->managerApprovedExpenseForFinance($employeeId, $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$empHeadLocId, $selectedDepartment,$limit,$offset);
    }
    public function viewAllForFinanceTeam($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$empHeadLocId, $selectedDepartment,$limit,$offset)
    {

        return $this->getExpenseDao()->viewAllForFinanceTeam($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$empHeadLocId, $selectedDepartment,$limit,$offset);
    }

    public function empHeadLocation($empNo){
        return $this->getExpenseDao()->empHeadLocation($empNo);
    }

    public function managerApprovedExpenseForFinance1($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup, $empHeadLocId, $selectedDepartment,$limit,$offset)
    {
        // var_dump($selectedDepartment);
        // exit;
        return $this->getExpenseDao()->managerApprovedExpenseForFinance1($employeeId, $fromDate, $toDate, $selectedProject, $selectedStatus, $subList, $isSup, $empHeadLocId, $selectedDepartment,$limit,$offset);
    }

    public function saveExpense($employeeId, $state, $customerId, $projectId, $date, $tripName, $expenseNumber = '1')
    {
        // var_dump($date);
        $newExpense = new Expense();
        
        // var_dump($customerId);
        $newExpense->setCustomerId($customerId);
        $newExpense->setProjectId($projectId);
        $newExpense->setEmployeeId($employeeId);
        $newExpense->setState($state);
        $newExpense->setDate($date);
        $newExpense->setExpenseNumber($expenseNumber);
        $newExpense->setExpenseName($tripName);
        // var_dump($newExpense);exit;
        $x = $this->getExpenseDao()->saveExpense($newExpense);
        // var_dump($x['expenseId']);exit;
        return $x;

    }


    public function saveExpenseActionLog($comment, $state, $currentDate, $loggedInEmpNumber,$expenseId)
    {
        // var_dump($comment." ".$state." ".$currentDate." ".$loggedInEmpNumber);exit;
        $newExpenseActionLog = new ExpenseActionLog();
        $newExpenseActionLog->setComment($comment);
        $newExpenseActionLog->setState($state);
        $newExpenseActionLog->setDateTime($currentDate);
        $newExpenseActionLog->setPerformedBy($loggedInEmpNumber);
        $newExpenseActionLog->setExpenseId($expenseId);
        $x = $this->getExpenseDao()->saveExpenseActionLog($newExpenseActionLog);
        return $x;

    }
    public function updateExpenseState($state,$expenseId)
    {
        return $this->getExpenseDao()->updateExpenseState($state,$expenseId);
    }

    /**
    * To save the status of the Finance team on the expense submit
    */
    public function accountTeamStatusUpdate($state,$expenseId)
    {
        return $this->getExpenseDao()->accountTeamStatusUpdate($state,$expenseId);
    }

    public function accountTeamRejectedStatusUpdate($state,$expenseId)
    {
        return $this->getExpenseDao()->accountTeamRejectedStatusUpdate($state,$expenseId);
    }

    public function getActionDetails($expenseId)
    {
        return $this->getExpenseDao()->getActionDetails($expenseId);
    }
    public function updateExpense($employeeId, $state, $customerId, $projectId, $date,$expenseId, $tripName)
    {
        return $this->getExpenseDao()->updateExpense($employeeId, $state, $customerId, $projectId, $date,$expenseId, $tripName) ;

    }
    public function updateExpenseActionLog($comment, $state, $currentDate, $loggedInEmpNumber,$expenseId)
    {
        return $this->getExpenseDao()->updateExpenseActionLog($comment, $state, $currentDate, $loggedInEmpNumber,$expenseId);
    }
    /*updating the expense items*/
    public function updateExpenseItems($date,$expenseType,$message,$paidByCompany,$amount,$currency,$noAttachment,$itemId) 
    {

        if($noAttachment == 1){
            $noAttachment = 1;
        }

        return $this->getExpenseDao()->updateExpenseItems($date,$expenseType,$message,$paidByCompany,$amount,$currency,$noAttachment,$itemId);
    }
    /*called by ajax to remove row along with attachment*/
    public function deleteExpenseItems($expenseItemId) {

        $this->getExpenseDao()->deleteExpenseAttachment($expenseItemId);
        $this->getExpenseDao()->deleteExpenseItems($expenseItemId);
        return $this;
    }
     /*called by ajax to remove attachment*/
    public function removeExpenseItemsAttachment($expenseItemId) {

        return $this->getExpenseDao()->removeExpenseItemsAttachment($expenseItemId);
         
    }
    public function deleteExpenseAttachment($item_id)
    {
        return $this->getExpenseDao()->deleteExpenseAttachment($item_id);
    }
    public function getProjectListForCLient($clientId)
    {
        return $this->getExpenseDao()->getProjectListForCLient($clientId);
    }
    public function getStatus1($expId)
    {
       return $this->getExpenseDao()->getStatus1($expId); 
    }

    public function updateExpenseNum($id, $expId)
    {   
    // var_dump($id." ".$expId);
        return $this->getExpenseDao()->updateExpenseNum($id, $expId);
    }
    public function getActionDetailsFordownload($expenseId)
    {
        return $this->getExpenseDao()->getActionDetailsFordownload($expenseId);
    }

}
