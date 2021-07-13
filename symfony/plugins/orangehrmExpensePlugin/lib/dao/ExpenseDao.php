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
class ExpenseDao {

    /**
     * Get Timesheet by given Timehseet Id
     * @param $timesheetId
     * @return Timesheet
     */
    protected $configDao;

    public function setConfigDao($configDao) {
        $this->configDao = $configDao;
    }

    public function getConfigDao() {

        if (is_null($this->configDao)) {
            $this->configDao = new ConfigDao();
        }

        return $this->configDao;
    }

    public function getTimesheetById($timesheetId) {

        try {
            $timesheet = Doctrine::getTable('Timesheet')
            ->find($timesheetId);

            return $timesheet;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet by given Start Date
     * @param $starDate
     * @return Timesheet
     */
    public function getTimesheetByStartDate($startDate) {

        try {

            $query = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("start_date = ?", $startDate);
            $results = $query->execute();
            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save Timesheet
     * @param Timesheet $timesheet
     * @return Timesheet
     */
    public function saveTimesheet(Timesheet $timesheet) {

        try {

            if ($timesheet->getTimesheetId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($timesheet);
                $timesheet->setTimesheetId($idGenService->getNextID());
            }
            $timesheet->save();

            return $timesheet;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    
    /**
     * Get Timesheet Item by given Id
     * @param $timesheetItemId
     * @return TimesheetItem
     */
    public function getTimesheetItemById($timesheetItemId) {

        try {

            $timesheetItem = Doctrine::getTable("TimesheetItem")
            ->find($timesheetItemId);

            return $timesheetItem;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet Item by given timesheetId and employeeId
     * @param $timesheetId , $employeeId
     * @return TimesheetItem
     */
    public function getTimesheetItem($timesheetId, $employeeId) {

        try {

            $query = Doctrine_Query::create()
            ->from("TimesheetItem ti")
            ->leftJoin("ti.Project p")
            ->leftJoin("ti.ProjectActivity a")
            ->where("ti.timesheetId = ?", $timesheetId)
            ->andWhere("ti.employeeId = ?", $employeeId)
            ->orderBy('ti.projectId ASC, ti.activityId ASC, ti.date ASC');


            return $query->execute()->getData();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet Item by given timesheetId and employeeId
     * @param $timesheetId , $employeeId
     * @return TimesheetItem
     */
    public function getTimesheetItemByDateProjectId($timesheetId, $employeeId, $projectId, $activityId, $date) {

        try {

            $timesheetItem = Doctrine_Query::create()
            ->from("TimesheetItem")
            ->where("timesheetId = ?", $timesheetId)
            ->andWhere("employeeId = ?", $employeeId)
            ->andWhere("projectId = ?", $projectId)
            ->andWhere("activityId = ?", $activityId)
            ->andWhere("date = ?", $date);

            return $timesheetItem->execute();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save TimesheetItem
     * @param $timesheetItem
     * @return $timesheetItem
     */
    public function saveExpenseItem(ExpenseItem $expenseItem)
    {
        try {
            $expenseItem->save();
            return $expenseItem->getData();            
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function saveExpenseAttachment(ExpenseAttachment $ExpenseAttachment)
    {

        $ExpenseAttachment->save();
        return $ExpenseAttachment;
    }

    /**
     * Add or Save TimesheetItem
     * @param $timesheetItem
     * @return $timesheetItem
     */
    public function deleteTimesheetItems($employeeId, $timesheetId, $projectId, $activityId) {
        try {

            $query = Doctrine_Query::create()
            ->delete()
            ->from("TimesheetItem")
            ->where("timesheetId = ?", $timesheetId)
            ->andWhere("employeeId = ?", $employeeId)
            ->andWhere("projectId = ?", $projectId)
            ->andWhere("activityId = ?", $activityId);

            $timesheetItemDeleted = $query->execute();
            if ($timesheetItemDeleted > 0) {
                return true;
            }

            return false;
        } catch (Exception $ex) {

            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Add or Save TimesheetActionLog
     * @param TimesheetActionLog $timesheetActionLog
     * @return $timesheetActionLog
     */
    public function saveTimesheetActionLog(TimesheetActionLog $timesheetActionLog) {

        try {

            if ($timesheetActionLog->getTimesheetActionLogId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($timesheetActionLog);
                $timesheetActionLog->setTimesheetActionLogId($idGenService->getNextID());
            }

            $timesheetActionLog->save();
            return $timesheetActionLog;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get TimesheetActionLog by given TimesheetActionLog Id
     * @param $timesheetActionLogId
     * @return TimesheetActionLog
     */
    public function getTimesheetActionLogById($timesheetActionLogId) {

        try {

            $timesheetActionLog = Doctrine::getTable("TimesheetActionLog")
            ->find($timesheetActionLogId);

            return $timesheetActionLog;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet by given Start Date And Employee Id
     * @param $starDate , $employeeId
     * @return Timesheet
     */
    public function getTimesheetByStartDateAndEmployeeId($startDate, $employeeId) {

        try {

            $query = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("start_date = ?", $startDate)
            ->andWhere("employee_id = ?", $employeeId);

            $results = $query->execute();
            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get TimesheetActionLog by given Timesheet Id
     * @param $timesheetActionLogId
     * @return TimesheetActionLog
     */
    public function getTimesheetActionLogByTimesheetId($timesheetId) {

        try {


            $query = Doctrine_Query::create()
            ->from("TimesheetActionLog")
            ->where("timesheetId = ?", $timesheetId)
            ->orderBy('timesheetActionLogId');

            $results = $query->execute();
            if ($results[0]->getTimesheetActionLogId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get start and end days of each timesheet
     * @param none
     * @return Start and end dates Array
     */
    public function getStartAndEndDatesList($employeeId) {

        $query = Doctrine_Query::create()
        ->select('a.start_date')
        ->from('Timesheet a')
        ->where("employeeId = ?", $employeeId)
        ->orderBy('a.start_date ASC');
        $results = $query->fetchArray();
        $query1 = Doctrine_Query::create()
        ->select('a.end_date')
        ->from('Timesheet a')
        ->where("employeeId = ?", $employeeId)
        ->orderBy('a.end_date ASC');

        $results1 = $query1->fetchArray();
        $resultArray = array($results, $results1);
        return $resultArray;
    }

    /**
     * Get Timesheet by given Employee Id
     * @param $employeeId
     * @return Timesheets
     */
    public function getTimesheetByEmployeeId($employeeId) {

        try {

            $query = Doctrine_Query::create()
            ->from('Timesheet a')
            ->where('employee_id = ?', $employeeId)
            ->orderBy('a.start_date ASC');

            $results = $query->execute();

            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Timesheet by given Employee Id and State
     * @param $employeeId
     * @return Timesheets
     */
    public function getTimesheetByEmployeeIdAndState($employeeId, $stateList) {

        try {

            $query = Doctrine_Query::create()
            ->from('Timesheet')
            ->where('employee_id = ?', $employeeId)
            ->andWhereIn('state', $stateList);

            $results = $query->execute();

            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }
    
    /**
     * Return an Array of Timesheets for given Employee Ids and States
     * 
     * @version 2.7.1
     * @param Array $employeeIdList Array of Employee Ids
     * @param Array $stateList Array of States
     * @param Integer $limit
     * @return Array of Timesheets
     */
    public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit = 100) {

        try {

            if ((!empty($employeeIdList)) && (!empty($stateList))) {

                $employeeListEscapeString = implode(',', array_fill(0, count($employeeIdList), '?'));
                $stateListEscapeString = implode(',', array_fill(0, count($stateList), '?'));

                $q = "SELECT o.timesheet_id AS timesheetId, o.start_date AS timesheetStartday, o.end_date AS timesheetEndDate, o.employee_id AS employeeId, e.emp_firstname AS employeeFirstName, e.emp_lastname AS employeeLastName
                FROM ohrm_timesheet o
                LEFT JOIN  hs_hr_employee e ON o.employee_id = e.emp_number
                WHERE 
                o.employee_id IN ({$employeeListEscapeString}) AND
                o.state IN({$stateListEscapeString})
                ORDER BY e.emp_lastname ASC";

                if ($limit) {
                    $q .= " LIMIT 0, {$limit}";
                }
                
                $escapeValueArray = array_merge($employeeIdList, $stateList);
                
                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($q);
                $query->execute($escapeValueArray);
                
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get Customer by customer name
     * @param $customerName
     * @return Customer
     */
    public function getCustomerByName($customerName) {

        try {

            $query = Doctrine_Query::create()
            ->from("Customer")
            ->where("name = ?", $customerName);

            $results = $query->execute();

            if ($results[0]->getCustomerId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }


    public function getExpenseAttachment($attachmentId) {
        try {
            $q = Doctrine_Query:: create()
            ->from('ExpenseAttachment a')
            ->where('a.id = ?', $attachmentId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * get Project By ProjectName And CustomerId
     * @param $projectName, $customerId
     * @return Project
     */
    public function getProjectByProjectNameAndCustomerId($projectName, $customerId) {

        try {

            $query = Doctrine_Query::create()
            ->from('Project')
            ->where('name = ?', $projectName)
            ->andWhere('customer_id = ?', $customerId)
            ->andWhere('is_deleted = ?', 0);

            $results = $query->execute();

            if ($results[0]->getProjectId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project Activities By PorjectId
     * @param $projectId, $deleted
     * @return Project Activities
     */
    public function getProjectActivitiesByPorjectId($projectId, $deleted = false) {

        try {

            $query = Doctrine_Query::create()
            ->from('ProjectActivity')
            ->where('project_id = ?', $projectId);

            if (!$deleted) {
                // Only fetch active projects
                $query->andWhere('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY);
            }

            $query->orderBy('name ASC');
            $results = $query->execute();

            if ($results[0]->getActivityId() == null) {
                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }    
    
    /**
     * Return an Array of Project Names
     * 
     * @version 2.7.1
     * @param Boolean $excludeDeletedProjects Exclude deleted projects or not
     * @param String $orderField Sort order field
     * @param String $orderBy Sort order
     * @return Array of Project Names
     */
    public function getProjectNameList($excludeDeletedProjects = true, $orderField='project_id', $orderBy='ASC') {
        try {

            $q = "SELECT p.project_id AS projectId, p.name AS projectName, c.name AS customerName
            FROM ohrm_project p
            LEFT JOIN ohrm_customer c ON p.customer_id = c.customer_id";
            
            if($excludeDeletedProjects) {
                $q .= " WHERE p.is_deleted = 0";
            }
            
            if ($orderField) {
                $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
                $q .= " ORDER BY {$orderField} {$orderBy}";
            }
            
            $pdo = Doctrine_Manager::connection()->getDbh();
            $projectList = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);

            return $projectList;
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    
    /**
     * Return an Array of Project Activities by Project Id
     * 
     * @version 2.7.1
     * @param Integer $projectId Project Id
     * @param Boolean $excludeDeletedActivities Exclude Deleted Project Activities or not
     * @return Array of Project Activities
     */
    public function getProjectActivityListByPorjectId($projectId, $excludeDeletedActivities = true) {

        try {

            $query = Doctrine_Query::create()
            ->from('ProjectActivity')
            ->where('project_id = ?', $projectId);

            if ($excludeDeletedActivities) {
                $query->andWhere('is_deleted = ?', ProjectActivity::ACTIVE_PROJECT_ACTIVITY);
            }
            $query->orderBy('name ASC');
            $results = $query->fetchArray();
            
            return $results;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * get Project Activity By Project Id And ActivityName
     * @param $projectId, $activityName
     * @return Project Activities
     */
    public function getProjectActivityByProjectIdAndActivityName($projectId, $activityName) {

        try {

            $query = Doctrine_Query::create()
            ->from('ProjectActivity')
            ->where('project_id = ?', $projectId)
            ->andWhere('name = ?', $activityName);

            $results = $query->execute();

            if ($results[0]->getActivityId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project Activity By Activity Id
     * @param $activityId
     * @return Project Activities
     */
    public function getProjectActivityByActivityId($activityId) {

        try {

            $query = Doctrine_Query::create()
            ->from('ProjectActivity')
            ->where('activity_id = ?', $activityId);

            $results = $query->execute();

            if ($results[0]->getActivityId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * retrieve suppervisor approved timesheets
     * @param
     * @return $timesheets doctrine collection
     */
    public function getPendingApprovelTimesheetsForAdmin() {

        try {
            $query = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("state = ?", "SUPERVISOR APPROVED");
            $results = $query->execute();
            if ($results[0]->getTimesheetId() == null) {

                return null;
            } else {

                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Activity by given Activity Id
     * @param $activityId
     * @return ProjectActivity
     */
    public function getActivityByActivityId($activityId) {

        try {
            $activity = Doctrine::getTable('ProjectActivity')
            ->find($activityId);

            return $activity;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Timesheet Time Format
     * @param 
     * @return Time Format
     */
    public function getTimesheetTimeFormat() {

        try {
            return $this->getConfigDao()->getValue(ConfigService::KEY_TIMESHEET_TIME_FORMAT);
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get Project List
     * @param $orderField, $orderBy, $deleted
     * @return Projects
     */
    public function getProjectList($orderField='project_id', $orderBy='ASC', $deleted =0) {
        try {
            $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
            $q = Doctrine_Query::create()
            ->from('Project')
            ->andWhere('is_deleted = ?', $deleted)
            ->orderBy($orderField . ' ' . $orderBy);

            $projectList = $q->execute();

            return $projectList;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    

    /**
     * get Project List For Validation
     * @param $orderField, $orderBy,
     * @return Projects
     */
    public function getProjectListForValidation($orderField='project_id', $orderBy='ASC') {
        try {
            $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
            $q = Doctrine_Query::create()
            ->from('Project')
            ->orderBy($orderField . ' ' . $orderBy);

            $projectList = $q->execute();

            return $projectList;
        } catch (Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

    /**
     * get Latest Timesheet EndDate
     * @param $employeeId
     * @return EndDate
     */
    public function getLatestTimesheetEndDate($employeeId) {

        try {

            $query = Doctrine_Query::create()
            ->select('MAX(end_date)')
            ->from("Timesheet")
            ->where('employee_id = ?', $employeeId);

            $results = $query->execute();

            if ($results[0]['MAX'] != null) {

                return $results[0]['MAX'];
            } else {
                return null;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * check For Overlapping Timesheets
     * @param $$startDate, $endDate, $employeeId
     * @return string 1,0
     */
    public function checkForOverlappingTimesheets($startDate, $endDate, $employeeId) {


        $isValid = "1";

        try {
            //case1=where the startDate is ok but the endDate comes in between some other timesheets startDate and endDate
            $query1 = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("employee_id = ?", $employeeId)
            ->andWhere("start_date >= ?", $startDate)
            ->andWhere("end_date <= ?", $endDate);
            $records1 = $query1->execute();


            if ((count($records1) > 0)) {
                $isValid = "0";
            }

            //case2=this checks wether the timesheets startDate falls between some other timesheets startDate and enddate
            $query2 = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("employeeId = ?", $employeeId)
            ->andWhere("start_date <= ?", $startDate)
            ->andWhere("end_date >= ?", $startDate);
            $records2 = $query2->execute();


            if ((count($records2) > 0)) {

                $isValid = "0";
            }

            //case3=this checks the case where new timesheet about to create totaly ovelapps a existing timesheet
            $query3 = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("employeeId = ?", $employeeId)
            ->andWhere("start_date >= ?", $startDate)
            ->andWhere("start_date <= ?", $endDate);
            $records3 = $query3->execute();


            if ((count($records3) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }

        return $isValid;
    }

    public function checkForMatchingTimesheetForCurrentDate($employeeId, $currentDate) {


        try {
            $query = Doctrine_Query::create()
            ->from("Timesheet")
            ->where("employee_id = ?", $employeeId)
            ->andWhere("start_date <= ?", $currentDate)
            ->andWhere("end_date >= ?", $currentDate);
            $record = $query->execute();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }

        if ((count($record) > 0)) {

            return $record[0];
        } else {
            return null;
        }
    }

    /**
     *
     * @param type $employeeIds
     * @param type $dateFrom
     * @param type $dateTo
     * @param type $subDivision
     * @param type $employeementStatus
     * @return type array
     */
    public function searchTimesheetItems($employeeIds = null, $employeementStatus = null, $supervisorIds = null, $subDivision = null, $dateFrom = null, $dateTo = null) {

        $q = Doctrine_Query::create()
        ->select("e.emp_middle_name, e.termination_id , e.emp_lastname, e.emp_firstname, i.date, cust.name, prj.name, act.name, i.comment, SUM(i.duration) AS total_duration ")
        ->from("ProjectActivity act")
        ->leftJoin("act.Project prj")
        ->leftJoin("prj.Customer cust")
        ->leftJoin("act.TimesheetItem i")
        ->leftJoin("i.Employee e");
        
        $q->where("act.activity_id = i.activity_id ");
        
        if ($employeeIds != null) {
            if (is_array($employeeIds)) {
                $q->whereIn("e.emp_number", $employeeIds);
            } else {
                $q->andWhere(" e.emp_number = ?", $employeeIds);
            }
        }
        
        if (is_array($supervisorIds) && sizeof($supervisorIds)>0) {
            $q->whereIn("e.emp_number", $supervisorIds);
        }

        if( $employeementStatus > 0 ){
            $q->andWhere("e.emp_status = ?", $employeementStatus);
        } else {
            if($employeeIds <= 0){
                $q->andWhere("(e.termination_id IS NULL)");
            }            
        }        
        
        if( $subDivision > 0){

            $companyService = new CompanyStructureService();
            $subDivisions = $companyService->getCompanyStructureDao()->getSubunitById($subDivision);

            $subUnitIds = array($subDivision);
            if (!empty($subDivisions)) {
                $descendents = $subDivisions->getNode()->getDescendants();
                
                foreach($descendents as $descendent) {                
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn("e.work_station", $subUnitIds);            
        }

        if ($dateFrom != null) {
            $q->andWhere("i.date >=?", $dateFrom);
        }

        if ($dateTo != null) {
            $q->andWhere("i.date <=?", $dateTo);
        }

        $q->groupBy("e.emp_number, i.date, act.activity_id");
        $q->orderBy("e.lastName ASC, i.date DESC, cust.name, act.name ASC ");

        $result = $q->execute(array(), Doctrine::HYDRATE_SCALAR);

        return $result;
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimeSheetsByDepartmentAndStatus
    * Purpose : To show the timesheets of the employees of the selected 
    * department to the admin. We are getting month, year and department
    * from the form. 
    */
    public function getTimeSheetsByDepartmentAndStatus($departmentId, $month, $year, $status, $offSet, $limit)
    {
        /**
        * $query is written to acheive the pagination. Both $query and $countQuery will
        * respond with the same output.
        */
        $query = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number where work_station=$departmentId and MONTH(start_date) = $month and YEAR(start_date) = $year and ohrm_timesheet.state='$status' and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL GROUP BY start_date,emp_firstname ORDER BY emp_firstname ASC, start_date LIMIT $offSet, $limit";
        /**
        * The $countQuery was written to support the pagination and the 
        * download-as-CSV-sheet. Using this query, the total count for selected
        * month and year is displayed above the table. In download-csv file
        * we need to display all the records regardless of the current pagination
        * so this query is used to acheive that condition.
        */
        $countQuery = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager, ohrm_subunit.name as department FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id where work_station=$departmentId and MONTH(start_date) = $month and YEAR(start_date) = $year and ohrm_timesheet.state='$status' and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL GROUP BY start_date,emp_firstname ORDER BY emp_firstname ASC, start_date";

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        // Getting the counts.
        $prepareCountQuery = $pdo->prepare($countQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $countResult];
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimeSheetsByDepartmentAndStatusDateRange
    * Purpose : To show the timesheets of the employees of the selected 
    * department to the admin. We are getting month, year and department
    * from the form. 
    */
    public function getTimeSheetsByDepartmentAndStatusDateRange($selectedDepartment, $fromDate, $toDate, $selectedStatus, $offset, $limit)
    {
        /**
        * $query is written to acheive the pagination. Both $query and $countQuery will
        * respond with the same output.
        */

        $query1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number where ";
        if ($selectedDepartment != 0) {
            $q = "work_station=$selectedDepartment and ";
            $countQuery2 = "work_station=$selectedDepartment and ";
        }
        $query2 = "start_date >= '$fromDate' and end_date <= '$toDate' and ohrm_timesheet.state='$selectedStatus' and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, start_date LIMIT $offset, $limit";

        $query = $query1.$q.$query2;

        /**
        * The $countQuery was written to support the pagination and the 
        * Excel sheet download. Using this query, the total count for selected
        * month and year is displayed above the table. In download-csv file
        * we need to display all the records regardless of the current pagination
        * so this query is used to acheive that condition.
        */

        $countQuery1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager, ohrm_subunit.name as department FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id where ";
        $countQuery3= "start_date >= '$fromDate' and end_date <= '$toDate' and ohrm_timesheet.state='$selectedStatus' and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, start_date";
        $countQuery = $countQuery1.$countQuery2.$countQuery3;

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        // Getting the counts.
        $prepareCountQuery = $pdo->prepare($countQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $countResult];
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Purpose : Query to fetch all the timesheets of the selected employee
    */
    public function getTimeSheetOfEmployee($employeeId, $fromDate, $toDate, $selectedStatus, $limit, $offset)
    {
        $query = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, work_station FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and start_date >= '$fromDate' and end_date <= '$toDate' and ohrm_timesheet.state='$selectedStatus' ORDER BY  start_date ASC LIMIT $offset, $limit";

        $countQuery = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and start_date >= '$fromDate' and end_date <= '$toDate' and ohrm_timesheet.state='$selectedStatus' ORDER BY  start_date ASC";
        
        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        // Getting the counts.
        $prepareCountQuery = $pdo->prepare($countQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $countResult];
    }

    /**
    * Author : Pravinraj Venkatachalam
    * Name : getTimesheetForDeparmtmentLead
    * Purpose : To show the timesheets of the employees of the selected 
    * department to the department Lead. We are getting start date, end date 
    * and department from the form. 
    */
    public function getTimesheetForDeparmtmentLead($selectedDepartment, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $offset, $limit)
    {
        /**
        * $query is written to acheive the pagination. Both $query and $countQuery will
        * respond with the same output.
        */
        
        
        if ($notSubmitted != null && $submitted != null && $approved != null) {

            $statusQuery = "(ohrm_timesheet.state='NOT SUBMITTED' OR ohrm_timesheet.state='SUBMITTED' OR ohrm_timesheet.state='APPROVED')";
        }
        else if ($notSubmitted != null && $submitted != null) {
            $statusQuery = "( ohrm_timesheet.state='NOT SUBMITTED' OR ohrm_timesheet.state='SUBMITTED')";
        }
        else if ($notSubmitted != null && $approved != null) {
            $statusQuery = "( ohrm_timesheet.state='NOT SUBMITTED' OR ohrm_timesheet.state='APPROVED')";
        }
        else if ($submitted != null && $approved != null) {
            $statusQuery = "( ohrm_timesheet.state='APPROVED' OR ohrm_timesheet.state='SUBMITTED')";
        } else if ($submitted != null) {
            $statusQuery = "(ohrm_timesheet.state = 'SUBMITTED')";
        } else if ($notSubmitted != null) {
            $statusQuery = "(ohrm_timesheet.state = 'NOT SUBMITTED')";
        } else if ($approved != null) {
            $statusQuery = "(ohrm_timesheet.state = 'APPROVED')";
        }

        
        $query1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ";
        if ($selectedDepartment != 0) {
            $q = "work_station=$selectedDepartment and ";
            $countQuery2 = "work_station=$selectedDepartment and ";
        }
        $query2 = "start_date >= '$fromDate' and end_date <= '$toDate' and $statusQuery and hs_hr_employee.termination_id IS NULL and hs_hr_employee.role_in_department IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, emp_lastname,start_date LIMIT $offset, $limit";

        $query = $query1.$q.$query2;
        
        

        /**
        * The $countQuery was written to support the pagination and the 
        * Excel sheet download. Using this query, the total count for selected
        * month and year is displayed above the table. In download-csv file
        * we need to display all the records regardless of the current pagination
        * so this query is used to acheive that condition.
        */

        $countQuery1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager, ohrm_subunit.name as department FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id where ";
        $countQuery3= "start_date >= '$fromDate' and end_date <= '$toDate' and $statusQuery and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL and hs_hr_employee.role_in_department IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, start_date";

        $countQuery = $countQuery1.$countQuery2.$countQuery3;

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        // Getting the counts.
        $prepareCountQuery = $pdo->prepare($countQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $countResult];
    }

     /**
    * Author : Pravinraj Venkatachalam
    * Purpose : Query to fetch all the timesheets of the selected employee
    */
     public function getTimeSheetOfEmployeeForDepartmentLead($employeeId, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $limit, $offset)
     {
      if ($notSubmitted != null && $submitted != null && $approved != null) {

        $statusQuery = "(ohrm_timesheet.state='NOT SUBMITTED' OR ohrm_timesheet.state='SUBMITTED' OR ohrm_timesheet.state='APPROVED')";
    }
    else if ($notSubmitted != null && $submitted != null) {
        $statusQuery = "( ohrm_timesheet.state='NOT SUBMITTED' OR ohrm_timesheet.state='SUBMITTED')";
    }
    else if ($notSubmitted != null && $approved != null) {
        $statusQuery = "( ohrm_timesheet.state='NOT SUBMITTED' OR ohrm_timesheet.state='APPROVED')";
    }
    else if ($submitted != null && $approved != null) {
        $statusQuery = "( ohrm_timesheet.state='APPROVED' OR ohrm_timesheet.state='SUBMITTED')";
    } else if ($submitted != null) {
        $statusQuery = "(ohrm_timesheet.state = 'SUBMITTED')";
    } else if ($notSubmitted != null) {
        $statusQuery = "(ohrm_timesheet.state = 'NOT SUBMITTED')";
    } else if ($approved != null) {
        $statusQuery = "(ohrm_timesheet.state = 'APPROVED')";
    }

        // Here IN can be used avoiding all these else if's. 

    $query = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, work_station FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and start_date >= '$fromDate' and end_date <= '$toDate' and $statusQuery ORDER BY  start_date ASC LIMIT $offset, $limit";

    $countQuery = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and start_date >= '$fromDate' and end_date <= '$toDate' and $statusQuery ORDER BY  start_date ASC";
        //print_r($query);exit;
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($query);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        // Getting the counts.
    $prepareCountQuery = $pdo->prepare($countQuery);
    $prepareCountQuery->execute();
    $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);
    return [$result, $countResult];
}
public function saveExpense(Expense $expense) {
    $expense->save();
    return $expense->getData();
}

public function getEmpNameForEmail($expenseId) {
   $querys = "SELECT ohrm_expense.expense_id,hs_hr_employee.emp_number, emp_firstname, emp_lastname, emp_work_email, location_id from ohrm_expense 
       INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number INNER JOIN hs_hr_emp_locations ON ohrm_expense.employee_id = hs_hr_emp_locations.emp_number where expense_id = '$expenseId' ";

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($querys);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

public function getCCAdminList($expenseReporterLocationId) {
    $querys = "SELECT  empId, hs_hr_employee.emp_firstname, hs_hr_employee.emp_lastname, hs_hr_employee.emp_work_email, location from `cost_center_mapping` INNER JOIN hs_hr_employee ON cost_center_mapping.empId = hs_hr_employee.emp_number WHERE location = '$expenseReporterLocationId' ";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($querys);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

public function getSupDetailForFinaceApproval($expenseId) {
   $querys = "SELECT ohrm_expense_action_log.performed_by, hs_hr_employee.emp_firstname, hs_hr_employee.emp_lastname, hs_hr_employee.emp_work_email FROM `ohrm_expense_action_log`Left Join hs_hr_employee ON hs_hr_employee.emp_number = ohrm_expense_action_log.performed_by  WHERE expense_id = '72' and action ='APPROVED'";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($querys);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

public function viewAllForSup($employeeId,  $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$selectedDepartment, $limit, $offset) {

    $ids = join("','",$subList);

   $query1 = "SELECT sum(amount) as amount,ohrm_expense.expense_id,emp_firstname, emp_lastname,hs_hr_employee.employee_id as emp_id, date, state, ohrm_project.name,ohrm_expense.expense_number as expenseNumber, ohrm_subunit.name as deptName
    FROM ohrm_expense_item INNER JOIN ohrm_expense ON ohrm_expense.expense_id = ohrm_expense_item.expense_id INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number JOIN ohrm_project ON ohrm_expense.project_id = ohrm_project.project_id join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id ";

    $queryLast = " GROUP BY ohrm_expense_item.expense_id ORDER by ohrm_expense.expense_id DESC";

    if ($limit || $offset) {
        $queryLimit = " LIMIT {$offset}, {$limit}";
    }

    if (!empty($employeeId && $selectedProject && $selectedDepartment)) { 
        $q = "WHERE work_station='$selectedDepartment' and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate'";
    }
    else if ($selectedProject!=NULL && $selectedDepartment == 0 && $employeeId == NULL ){
        $q = "WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'NOT SUBMITTED' and ohrm_expense.project_id = '$selectedProject'";
    }
    else if ($selectedProject == NULL && $selectedDepartment == 0 && $employeeId!=NULL) {
        $q = "WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'NOT SUBMITTED' and ohrm_expense.employee_id = '$employeeId'";
    }
    else if($selectedProject!=NULL && $selectedDepartment == 0 && $employeeId!=NULL ){
        $q ="WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.employee_id = '$employeeId' and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject'";
    }
    else if($selectedProject == NULL && $selectedDepartment!= 0 && $employeeId!=NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.employee_id = '$employeeId' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' ";
    }
    else if($selectedProject != NULL && $selectedDepartment!= 0 &&  $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' ";
    }
    else if ($selectedProject == NULL && $selectedDepartment!=0 && $employeeId == NULL) {
        $q = "WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'NOT SUBMITTED'";
    }
    else if($selectedProject == NULL && $selectedDepartment== 0 && $employeeId == NULL ){
        $q = " WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'NOT SUBMITTED'";
    }
     if ($isSup == true){
        $q1 = "and  hs_hr_employee.emp_number IN ('$ids')";
        $query = $query1 . $q . $q1 . $queryLast . $queryLimit;
        $countQuery = $query1 . $q . $q1 . $queryLast;

    }
    else{
        $query = $query1 . $q . $queryLast . $queryLimit;
        $countQuery = $query1 . $q . $queryLast;
    }

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($query);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareCountQuery = $pdo->prepare($countQuery);
    $prepareCountQuery->execute();
    $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);

    return [$result, $countResult];
}

public function getExpense($employeeId,  $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$selectedDepartment, $limit, $offset )
{   
    $ids = join("','",$subList);
    
    $query1 =  " SELECT sum(amount) as amount,ohrm_expense.expense_id,emp_firstname, emp_lastname,hs_hr_employee.employee_id as emp_id, date, state, ohrm_project.name,ohrm_expense.expense_number as expenseNumber, ohrm_subunit.name as deptName 
    FROM ohrm_expense_item INNER JOIN ohrm_expense ON ohrm_expense.expense_id = ohrm_expense_item.expense_id INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number JOIN ohrm_project ON ohrm_expense.project_id = ohrm_project.project_id join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id ";

    $queryLast = " GROUP BY ohrm_expense_item.expense_id ORDER by ohrm_expense.expense_id DESC";

    if ($limit || $offset) {
        $queryLimit = " LIMIT {$offset}, {$limit}";
    }

    if (!empty($employeeId && $selectedProject && $selectedDepartment)) { 
        $q = " WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus' and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject' ";
    }
    else if ($employeeId == NULL && $selectedDepartment == 0 && $selectedProject!=NULL){
        $q = "WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus' and ohrm_expense.project_id = '$selectedProject'";
    }
    else if ($selectedProject == NULL && $selectedDepartment == 0 && $employeeId!=NULL) {
        $q = "WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus' and ohrm_expense.employee_id = '$employeeId'";
    }
    else if($selectedDepartment == 0 && $selectedProject!=NULL && $employeeId!=NULL ){
        $q ="WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus' and ohrm_expense.employee_id = '$employeeId' and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject'";
    }
    else if($selectedDepartment!= 0 && $selectedProject == NULL && $employeeId!=NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.employee_id = '$employeeId' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus'";
    }
    else if( $selectedDepartment!= 0 && $selectedProject != NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus'";
    }
    else if( $selectedDepartment!= 0 && $selectedProject == NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus'";
    }
    else if( $selectedDepartment == 0 && $selectedProject == null && $employeeId == null ){
        $q = " WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.state = '$selectedStatus'";
    }
    if ($isSup == true){
        $q1 = "and  hs_hr_employee.emp_number IN ('$ids')";
        $query = $query1 . $q . $q1 . $queryLast . $queryLimit;
        $countQuery = $query1 . $q . $q1 . $queryLast;
    }
    else{
        $query = $query1 . $q . $queryLast . $queryLimit;
        $countQuery = $query1 . $q . $queryLast;
    }
        // if(empty($employeeId && $selectedProject)){
        //     $query = $query1 . $queryLast;
        // }
        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareCountQuery = $pdo->prepare($countQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);

        return [$result, $countResult];
}

/*
This is to fetch the date on which the expense was submited
*/
public function getSubmitedExpenseDate($expenseId){
    $q = "Select date FROM ohrm_expense where expense_id = '$expenseId'";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return $result;

}

public function getExpenseItem($expenseId)
{
    /*query to get the project & client and other info of employee*/
    $query1 = "SELECT hs_hr_employee.emp_firstname, hs_hr_employee.emp_lastname, ohrm_customer.name as clientName, ohrm_project.name, ohrm_expense.date, ohrm_expense.state , ohrm_expense.finance_status,  hs_hr_employee.employee_id as empNumber, ohrm_expense.expense_id as expenseId,ohrm_expense.expense_name as tripName, hs_hr_employee.emp_number as empId,ohrm_expense.customer_id as customerId, ohrm_expense.project_id as projectId,ohrm_expense.expense_number as expenseNumber
    FROM ohrm_expense INNER JOIN hs_hr_employee on ohrm_expense.employee_id = hs_hr_employee.emp_number
    JOIN ohrm_customer on ohrm_expense.customer_id = ohrm_customer.customer_id
    JOIN ohrm_project on ohrm_expense.project_id = ohrm_project.project_id
    JOIN ohrm_expense_item on ohrm_expense.expense_id = ohrm_expense_item.expense_id
    where ohrm_expense.expense_id = '$expenseId'";
    /*query to get the expense items of the expenseId*/
    $query= "SELECT  ohrm_expense_item.item_id,ohrm_expense_item.date_of_expense, ohrm_emp_expense_type.name as expense_type, ohrm_expense_item.message, ohrm_expense_item.paid_by_company, ohrm_expense_item.amount, ohrm_expense_attachment.file_content, ohrm_expense_attachment.file_name,ohrm_expense_attachment.id, ohrm_expense_attachment.id,ohrm_expense_item.expense_type as expenseTypeId,ohrm_expense_item.currency as currency, ohrm_expense_item.no_attachment as noAttachment
    FROM ohrm_expense_item 
    left join ohrm_expense_attachment on ohrm_expense_item.item_id = ohrm_expense_attachment.expense_item_id 
    join ohrm_expense on ohrm_expense.expense_id = ohrm_expense_item.expense_id 
    join ohrm_emp_expense_type on ohrm_expense_item.expense_type = ohrm_emp_expense_type.id
    where ohrm_expense.expense_id = $expenseId";
    /*query to get the sum of ammount in diffrent currency*/
    $query2 = "SELECT currency,SUM(amount) as amount
    FROM ohrm_expense_item
    WHERE expense_id='$expenseId' AND paid_by_company = '1'
    GROUP BY currency";
    $query3="SELECT currency,SUM(amount) as amount
    FROM ohrm_expense_item
    WHERE expense_id='$expenseId'
    GROUP BY currency";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($query);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

    $pdo1 = Doctrine_Manager::connection()->getDbh();
    $prepareQuery1 = $pdo->prepare($query1);
    $prepareQuery1->execute();
    $result1 = $prepareQuery1->fetchAll(PDO::FETCH_ASSOC);
    
    $pdo2 = Doctrine_Manager::connection()->getDbh();
    $prepareQuery2 = $pdo->prepare($query2);
    $prepareQuery2->execute();
    $due= $prepareQuery2->fetchAll(PDO::FETCH_ASSOC);
    
    $pdo3 = Doctrine_Manager::connection()->getDbh();
    $prepareQuery3 = $pdo->prepare($query3);
    $prepareQuery3->execute();
    $total= $prepareQuery3->fetchAll(PDO::FETCH_ASSOC);
    return [$result,$result1,$due,$total];
}
public function getProjectAjax($clientId)
{
    $q = "SELECT project_id,name
    FROM ohrm_project
    WHERE customer_id = $clientId";
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

public function getExpenseForEmp($employeeId, $limit, $offset)
{
    $q = "SELECT sum(amount) as amount,ohrm_expense.expense_id, date, state, ohrm_project.name as project_id, ohrm_expense.expense_number as expenseNumber
    FROM ohrm_expense_item INNER JOIN ohrm_expense ON ohrm_expense.expense_id = ohrm_expense_item.expense_id INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number JOIN ohrm_project ON ohrm_expense.project_id = ohrm_project.project_id WHERE ohrm_expense.employee_id = $employeeId GROUP BY ohrm_expense_item.expense_id ORDER by ohrm_expense.expense_id DESC ";

   if ($limit || $offset) {
    $queryLimit = " LIMIT {$offset}, {$limit}";
   }
    $query = $q . $queryLimit;
    $CountQuery = $q;

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($query);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareCountQuery = $pdo->prepare($CountQuery);
    $prepareCountQuery->execute();
    $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);

    return [$result, $countResult];
}

public function viewAllForFinanceTeam($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$empHeadLocId,$selectedDepartment, $limit, $offset)
{
    $ids = join("','",$subList);

    $query1 = "SELECT sum(amount) as amount,ohrm_expense.expense_id,emp_firstname, emp_lastname,hs_hr_employee.employee_id as employee_id, concat(emp_firstname, ' ', emp_lastname) AS employeeFullName, hs_hr_employee.employee_id as emp_id, date, state, ohrm_project.name,ohrm_expense.expense_number as expenseNumber,ohrm_subunit.name as deptName FROM ohrm_expense_item INNER JOIN ohrm_expense ON ohrm_expense.expense_id = ohrm_expense_item.expense_id INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number JOIN ohrm_project ON ohrm_expense.project_id = ohrm_project.project_id JOIN hs_hr_emp_locations ON hs_hr_emp_locations.emp_number = hs_hr_employee.emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id ";


    $queryLast = "GROUP BY ohrm_expense_item.expense_id ORDER by ohrm_expense.expense_id DESC";
     
    if ($limit || $offset) {
        $queryLimit = " LIMIT {$offset}, {$limit}";
    }

    if (!empty($employeeId && $selectedProject && $selectedDepartment)) { 
        $q = "Join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id WHERE work_station='$selectedDepartment' and ohrm_expense.employee_id= '$employeeId' and ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if ($employeeId == NULL && $selectedDepartment == 0 && $selectedProject!=NULL){
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.project_id = '$selectedProject'";
    }
    else if ($selectedProject == NULL && $selectedDepartment == 0 && $employeeId!=NULL) {
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId'";
    }
    else if($selectedDepartment == 0 && $selectedProject!=NULL && $employeeId!=NULL ){
        $q ="Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject'";
    }
    else if($selectedDepartment!= 0 && $selectedProject == NULL && $employeeId!=NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.employee_id = '$employeeId' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment!= 0 && $selectedProject != NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment!= 0 && $selectedProject == NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment== 0 && $selectedProject == NULL && $employeeId == NULL ){
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and state != 'SUBMITTED' and state != 'NOT SUBMITTED' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    if ($isSup == true){
        $q1 = "and  hs_hr_employee.emp_number IN ('$ids')";
         $query = $query1 . $q . $q1 . $queryLast . $queryLimit;
         $CountQuery = $query1 . $q . $q1 . $queryLast;
    }
    else{
        $query = $query1 . $q . $queryLast . $queryLimit;
        $CountQuery = $query1 . $q . $queryLast;
    }

       $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareCountQuery = $pdo->prepare($CountQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);

        return [$result, $countResult];
}



public function managerApprovedExpenseForFinance($employeeId, $fromDate, $toDate, $selectedProject, $selectedStatus,$subList,$isSup,$empHeadLocId, $selectedDepartment, $limit, $offset)
{
    $ids = join("','",$subList);   
    $query1 =  " SELECT sum(amount) as amount,ohrm_expense.expense_id,emp_firstname, emp_lastname,hs_hr_employee.employee_id as employee_id, concat(emp_firstname, ' ', emp_lastname) AS employeeFullName, hs_hr_employee.employee_id as emp_id, date, state, ohrm_project.name,ohrm_expense.expense_number as expenseNumber,ohrm_subunit.name as deptName FROM ohrm_expense_item INNER JOIN ohrm_expense ON ohrm_expense.expense_id = ohrm_expense_item.expense_id INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number JOIN ohrm_project ON ohrm_expense.project_id = ohrm_project.project_id JOIN hs_hr_emp_locations ON hs_hr_emp_locations.emp_number = hs_hr_employee.emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id ";

    $queryLast = " GROUP BY ohrm_expense_item.expense_id ORDER by ohrm_expense.expense_id DESC ";

    if ($limit || $offset) {
        $queryLimit = " LIMIT {$offset}, {$limit}";
    }


   if (!empty($employeeId && $selectedProject && $selectedDepartment)) { 
        $q = "Join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject' ";
    }
    else if ($employeeId == NULL && $selectedDepartment == 0 && $selectedProject!=NULL){
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.project_id = '$selectedProject'";
    }
    else if ($selectedProject == NULL && $selectedDepartment == 0 && $employeeId!=NULL) {
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId'";
    }
    else if($selectedDepartment == 0 && $selectedProject!=NULL && $employeeId!=NULL ){
        $q ="Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject'";
    }
    else if($selectedDepartment!= 0 && $selectedProject == NULL && $employeeId!=NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.employee_id = '$employeeId' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment!= 0 && $selectedProject != NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment!= 0 && $selectedProject == NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment== 0 && $selectedProject == NULL && $employeeId == NULL ){
        $q = " WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    if ($isSup == true){
        $q1 = "and  hs_hr_employee.emp_number IN ('$ids')";
        $query = $query1 . $q . $q1 . $queryLast . $queryLimit;
        $CountQuery = $query1 . $q . $q1 . $queryLast;
    }
    else{
        $query = $query1 . $q . $queryLast . $queryLimit;
        $CountQuery = $query1 . $q . $queryLast;
    }
        // if(empty($employeeId && $selectedProject)){
        //     $query = $query1 . $queryLast;
        // }
        // print_r($query);exit;
    
        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareCountQuery = $pdo->prepare($CountQuery);
        $prepareCountQuery->execute();
        $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);

        return [$result, $countResult];
}

public function empHeadLocation($empNo){
    try{
        $query = "SELECT location from cost_center_mapping WHERE empId = '$empNo'";
        
        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($query);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        return $result; 
    }catch (Exception $ex) {

        throw new DaoException($ex->getMessage());
    }

}

public function managerApprovedExpenseForFinance1($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$empHeadLocId,$selectedDepartment, $limit, $offset)
{

    $ids = join("','",$subList);   
    $query1 =  " SELECT sum(amount) as amount,ohrm_expense.expense_id,emp_firstname, emp_lastname,hs_hr_employee.employee_id as employee_id , concat(emp_firstname, ' ', emp_lastname) AS employeeFullName, hs_hr_employee.employee_id as emp_id, date, state, ohrm_project.name,ohrm_expense.expense_number as expenseNumber,ohrm_subunit.name as deptName 
    FROM ohrm_expense_item INNER JOIN ohrm_expense ON ohrm_expense.expense_id = ohrm_expense_item.expense_id INNER JOIN hs_hr_employee ON ohrm_expense.employee_id = hs_hr_employee.emp_number JOIN ohrm_project ON ohrm_expense.project_id = ohrm_project.project_id JOIN hs_hr_emp_locations ON hs_hr_emp_locations.emp_number = hs_hr_employee.emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id ";

    $queryLast = "GROUP BY ohrm_expense_item.expense_id ORDER by ohrm_expense.expense_id DESC";

    if ($limit || $offset) {
        $queryLimit = " LIMIT {$offset}, {$limit}";
    }

    if (!empty($employeeId && $selectedProject && $selectedDepartment)) { 
        $q = "WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject' ";
    }
    else if ($employeeId == NULL && $selectedDepartment == 0 && $selectedProject!=NULL){
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.project_id = '$selectedProject'";
    }
    else if ($selectedProject == NULL && $selectedDepartment == 0 && $employeeId!=NULL) {
        $q = "Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId'";
    }
    else if($selectedDepartment == 0 && $selectedProject!=NULL && $employeeId!=NULL ){
        $q ="Where DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId) and ohrm_expense.employee_id = '$employeeId' AND ohrm_expense.project_id = '$selectedProject'";
    }
    else if($selectedDepartment!= 0 && $selectedProject == NULL && $employeeId!=NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.employee_id = '$employeeId' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment!= 0 && $selectedProject != NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' AND ohrm_expense.project_id = '$selectedProject' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment!= 0 && $selectedProject == NULL && $employeeId == NULL){
        $q = "WHERE work_station='$selectedDepartment' and DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    else if( $selectedDepartment== 0 && $selectedProject == NULL && $employeeId == NULL ){
        $q = "WHERE DATE(ohrm_expense.date) between '$fromDate' and '$toDate' and ohrm_expense.finance_status = '$selectedStatus' and state = 'Approved' and hs_hr_emp_locations.location_id IN ($empHeadLocId)";
    }
    if ($isSup == true){
        $q1 = "and  hs_hr_employee.emp_number IN ('$ids')";
        $query = $query1 . $q . $q1 . $queryLast . $queryLimit;
        $CountQuery = $query1 . $q . $q1 . $queryLast;
    }
    else{
        $query = $query1 . $q . $queryLast . $queryLimit;
        $CountQuery = $query1 . $q . $queryLast;
    }

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($query);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareCountQuery = $pdo->prepare($CountQuery);
    $prepareCountQuery->execute();
    $countResult = $prepareCountQuery->fetchAll(PDO::FETCH_ASSOC);

    return [$result, $countResult];
}

public function saveExpenseActionLog(ExpenseActionLog $expenseActionLog)
{
    $expenseActionLog->save();
    return $expenseActionLog->getData();
}

public function updateExpenseState($state,$expenseId)
{
    $q ="UPDATE ohrm_expense
    set state = '$state', finance_status = 1
    WHERE expense_id = '$expenseId'"; 
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    return $result;
}

public function accountTeamStatusUpdate($state,$expenseId)
{
    $q ="UPDATE ohrm_expense
    set finance_status = 2, state = '$state'
    WHERE expense_id = '$expenseId'"; 
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    return $result;
}
public function accountTeamRejectedStatusUpdate($state,$expenseId)
{
    $q ="UPDATE ohrm_expense
    set finance_status = 3, state = '$state'
    WHERE expense_id = '$expenseId'"; 
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    return $result;
}

public function getActionDetails($expenseId)
{
    $q = "SELECT action,hs_hr_employee.emp_firstname, hs_hr_employee.emp_lastname,date_time,comment 
    FROM ohrm_expense_action_log
    JOIN hs_hr_employee ON ohrm_expense_action_log.performed_by = hs_hr_employee.emp_number
    WHERE ohrm_expense_action_log.expense_id = $expenseId
    ORDER BY date_time";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
public function updateExpense($employeeId, $state, $customerId, $projectId, $date,$expenseId, $tripName) {

 $q = "UPDATE ohrm_expense SET customer_id= '$customerId', project_id ='$projectId',state ='$state',expense_name = '$tripName', date = '$date' WHERE expense_id ='$expenseId'";
 $pdo = Doctrine_Manager::connection()->getDbh();
 $prepareQuery = $pdo->prepare($q);
 $result=$prepareQuery->execute();
 return  $result;
}
public function updateExpenseActionLog($comment, $state, $currentDate, $loggedInEmpNumber,$expenseId)
{
    $q="UPDATE ohrm_expense_action_log SET comment='$comment',action= '$state',date_time='$currentDate',performed_by='$loggedInEmpNumber' WHERE expense_id='$expenseId' ";
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    return  $result;
}
public function updateExpenseItems($date,$expenseType,$message,$paidByCompany,$amount,$currency,$noAttachment,$itemId)
{
    $q = " UPDATE ohrm_expense_item SET date_of_expense='$date',expense_type='$expenseType',message='$message',paid_by_company='$paidByCompany',amount='$amount', currency = '$currency', no_attachment= '$noAttachment' WHERE item_id='$itemId'";
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    return  $result;
}
public function deleteExpenseItems($expenseItemId) {
    try {

        $query = Doctrine_Query::create()
        ->delete()
        ->from("ExpenseItem")
        ->where("expensetItemId = ?", $expenseItemId);
        $expenseItemDeleted = $query->execute();

        if ($expenseItemDeleted > 0) {
            return true;
        }

        return false;
    } catch (Exception $ex) {

        throw new DaoException($ex->getMessage());
    }
}
public function deleteExpenseAttachment($item_id)
{
    try {
        $query = Doctrine_Query::create()
        ->delete()
        ->from("ExpenseAttachment")
        ->where("expenseItemId = ?", $item_id);
        $expenseItemDeleted = $query->execute();

        if ($expenseItemDeleted > 0) {
            return true;
        }

        return false;
    } catch (Exception $ex) {

        throw new DaoException($ex->getMessage());
    }
}

public function removeExpenseItemsAttachment($item_id)
{
    try {
        $query = Doctrine_Query::create()
        ->delete()
        ->from("ExpenseAttachment")
        ->where("attachmentId = ?", $item_id);
        $expenseItemDeleted = $query->execute();
  
        if ($expenseItemDeleted > 0) {
            return true;
        }

        return false;
    } catch (Exception $ex) {

        throw new DaoException($ex->getMessage());
    }
}
public function getProjectListForCLient($clientId,$deleted=0)
{
    try {
            // $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
        $q = Doctrine_Query::create()
        ->from('Project')
        ->Where('customerId = ?', $clientId)
        ->andWhere('is_deleted = ?',$deleted);

        $projectList = $q->execute();

        return $projectList;
    } catch (Exception $e) {
        throw new AdminServiceException($e->getMessage());
    }
}

public function getStatus1($expId){
    $q = "SELECT state FROM `ohrm_expense` WHERE `expense_id` = '$expId'";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return  $result;
}
public function updateExpenseNum($id, $expId)
{
 
   $q="UPDATE ohrm_expense SET expense_number = '$expId' WHERE expense_id='$id' ";
   $pdo = Doctrine_Manager::connection()->getDbh();
   $prepareQuery = $pdo->prepare($q);
   $result=$prepareQuery->execute();
   return  $result;
}
public function getActionDetailsFordownload($expenseId)
{
    $q = "SELECT action,hs_hr_employee.emp_firstname,hs_hr_employee.emp_lastname,hs_hr_employee.emp_number,date_time,comment,performed_by 
    FROM ohrm_expense_action_log
    JOIN hs_hr_employee ON ohrm_expense_action_log.performed_by = hs_hr_employee.emp_number
    WHERE ohrm_expense_action_log.expense_id = $expenseId and ohrm_expense_action_log.action = 'APPROVED'
    ORDER BY date_time";

    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q);
    $result=$prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

}