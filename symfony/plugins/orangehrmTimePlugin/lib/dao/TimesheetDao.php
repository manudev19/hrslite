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
class TimesheetDao {

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
    public function saveTimesheetItem(TimesheetItem $timesheetItem) {

        try {

            if ($timesheetItem->getTimesheetItemId() == '') {
                $idGenService = new IDGeneratorService();

                $idGenService->setEntity($timesheetItem);
                $timesheetItem->setTimesheetItemId($idGenService->getNextID());
            }

            $timesheetItem->save();

            return $timesheetItem;
        } catch (Exception $ex) {

            throw new DaoException($ex->getMessage());
        }
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
       public function getTimesheetListByEmployeeIdAndState($employeeIdList, $stateList, $limit, $offset) {
        $functionalheadEmpNumber=  $_SESSION['empNumber'];

        try {

            if ((!empty($employeeIdList)) && (!empty($stateList))) {

                $employeeListEscapeString = implode(',', array_fill(0, count($employeeIdList), '?'));
                $stateListEscapeString = implode(',', array_fill(0, count($stateList), '?'));
                if($_SESSION['userRole']=="Functional Head")
                {
              $q = " SELECT o.timesheet_id AS timesheetId, o.start_date AS timesheetStartday, o.end_date AS timesheetEndDate,o.employee_id AS employeeId,e.employee_id AS emp_id, e.emp_firstname AS employeeFirstName, e.emp_lastname AS employeeLastName, ol.name AS empLocation, s.name AS department
              FROM ohrm_timesheet o
              LEFT JOIN  hs_hr_employee e ON o.employee_id = e.emp_number INNER JOIN  ohrm_subunit s ON s.id=e.work_station
              JOIN  hs_hr_emp_locations AS el ON e.emp_number = el.emp_number
            JOIN ohrm_location AS ol ON el.location_id = ol.id
              Where s.functional_head= $functionalheadEmpNumber AND
              e.emp_number !=$functionalheadEmpNumber AND
              o.state IN ('SUBMITTED', 'ACCEPTED')";
                }
                else{
                $q = "SELECT  o.timesheet_id AS timesheetId, o.start_date AS timesheetStartday, o.end_date AS timesheetEndDate, o.employee_id AS employeeId,e.employee_id AS emp_id, e.emp_firstname AS employeeFirstName, e.emp_lastname AS employeeLastName, ol.name AS empLocation, os.name AS department
              FROM hs_hr_employee e
              JOIN ohrm_timesheet o ON o.employee_id = e.emp_number
                        JOIN  hs_hr_emp_locations AS el ON e.emp_number = el.emp_number
                        JOIN ohrm_location AS ol ON el.location_id = ol.id
                        JOIN ohrm_subunit AS os ON os.id = e.work_station
              WHERE
              o.employee_id IN ({$employeeListEscapeString}) AND
              o.state IN({$stateListEscapeString})
                        ORDER BY e.emp_lastname ASC";
                           }
        $countQuery = $q;

          if ($limit || $offset) {
             $q .= " LIMIT {$offset}, {$limit}";
         }

                $escapeValueArray = array_merge($employeeIdList, $stateList);

                $pdo = Doctrine_Manager::connection()->getDbh();
                $query = $pdo->prepare($q);
                $query->execute($escapeValueArray);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);

                $pdo = Doctrine_Manager::connection()->getDbh();
                $countQ = $pdo->prepare($countQuery);
                $countQ->execute($escapeValueArray);
                $countResults = count($countQ->fetchAll(PDO::FETCH_ASSOC));
            }
            return [$results, $countResults];

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
       $query2="";
       if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) ";
       }
       else if(date('D',strtotime($toDate)) == 'Sat')
       {
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) ";
       }
       else{
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) ";
       }

            $query3 = "and ohrm_timesheet.state='$selectedStatus' and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, start_date  LIMIT $offset, $limit";


            $query = $query1.$q.$query2.$query3;

        /**
        * The $countQuery was written to support the pagination and the
        * Excel sheet download. Using this query, the total count for selected
        * month and year is displayed above the table. In download-csv file
        * we need to display all the records regardless of the current pagination
        * so this query is used to acheive that condition.
        */
        $countQuery1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager, ohrm_subunit.name as department FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id    where ";
        $countQuery3= "and ohrm_timesheet.state='$selectedStatus' and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, start_date";
        $countQuery4="";
        if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $countQuery4 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) ";
         }
         else if(date('D',strtotime($toDate)) == 'Sat'){
            $countQuery4 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) ";

         }
         else{
            $countQuery4 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) ";
         }

            $countQuery = $countQuery1.$countQuery2.$countQuery4.$countQuery3;

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

       // $query = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, work_station FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and start_date >= '$fromDate' and end_date <= '$toDate' and ohrm_timesheet.state='$selectedStatus' ORDER BY  start_date ASC LIMIT $offset, $limit";

       // $countQuery = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and start_date >= '$fromDate' and end_date <= '$toDate' and ohrm_timesheet.state='$selectedStatus' ORDER BY  start_date ASC";
       $query1="SELECT concat( start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, work_station FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id =$employeeId  and ";
       $query2="";
       if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) ";
       }
       else if(date('D',strtotime($toDate)) == 'Sat')
       {
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) ";

       }
       else{
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) ";
       }

        $query3="and ohrm_timesheet.state='$selectedStatus' ORDER BY  start_date ASC LIMIT $offset, $limit";

        $query=$query1.$query2.$query3;

        $countQuery1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and ";
        $countQuery2="and ohrm_timesheet.state='$selectedStatus' ORDER BY  start_date ASC";
        $countQuery3="";
        if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $countQuery3 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) ";
         }
         else if(date('D',strtotime($toDate)) == 'Sat')
         {
            $countQuery3 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) ";

         }
         else{
            $countQuery3 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) ";
         }

            $countQuery = $countQuery1.$countQuery3.$countQuery2;


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
        } else{
            return null;
        }


        $query1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ";
        if ($selectedDepartment != 0) {
            $q = "work_station=$selectedDepartment and ";
            $countQuery2 = "work_station=$selectedDepartment and ";
        }
        $query2="";
        if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) and ";
        }
        else if(date('D',strtotime($toDate)) == 'Sat')
        {
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) and ";

        }
        else{
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) and ";
        }
            $query3 = " and hs_hr_employee.termination_id IS NULL and hs_hr_employee.role_in_department IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, emp_lastname,start_date LIMIT $offset, $limit";

            $query = $query1.$q.$query2.$statusQuery.$query3;



        /**
        * The $countQuery was written to support the pagination and the
        * Excel sheet download. Using this query, the total count for selected
        * month and year is displayed above the table. In download-csv file
        * we need to display all the records regardless of the current pagination
        * so this query is used to acheive that condition.
        */

        $countQuery1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, GROUP_CONCAT(erep_sup_emp_number) as reporting_manager, ohrm_subunit.name as department FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number LEFT join hs_hr_emp_reportto on hs_hr_employee.emp_number=hs_hr_emp_reportto.erep_sub_emp_number join ohrm_subunit on hs_hr_employee.work_station = ohrm_subunit.id where ";
        $countQuery3="";
        if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $countQuery3 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) and ";
         }
         else if(date('D',strtotime($toDate)) == 'Sat')
         {
            $countQuery3 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) and ";
         }
         else{
            $countQuery3 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) and ";
         }
            $countQuery4= " and hs_hr_emp_reportto.erep_reporting_mode=1 and hs_hr_employee.termination_id IS NULL and hs_hr_employee.role_in_department IS NULL GROUP BY start_date,emp_firstname,emp_lastname ORDER BY emp_firstname ASC, start_date";

            $countQuery = $countQuery1.$countQuery2.$countQuery3.$statusQuery.$countQuery4;

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
        } else{
            return null;
        }

        // Here IN can be used avoiding all these else if's.

        $query1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state, work_station FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and ";
        $query2="";
        if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
           $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) and ";
        }
        else if(date('D',strtotime($toDate)) == 'Sat')
        {
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) and ";

        }
        else{
            $query2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) and ";
        }
            $query3=" ORDER BY  start_date ASC LIMIT $offset, $limit";

            $query=$query1.$query2.$statusQuery.$query3;

        $countQuery1 = "SELECT concat(start_date, ' to ', end_date) as time_slot, start_date, end_date, emp_firstname,emp_lastname, emp_number, state FROM ohrm_timesheet INNER JOIN hs_hr_employee on ohrm_timesheet.employee_id = hs_hr_employee.emp_number where ohrm_timesheet.employee_id = $employeeId and ";
        $countQuery2="";
        if((date('D',strtotime($toDate)) == 'Sun')||date('D',strtotime($toDate)) == 'Mon'){
            $countQuery2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(5+DAYOFWEEK('$toDate')) DAY) and ";
         }
         else if(date('D',strtotime($toDate)) == 'Sat')
         {
             $countQuery2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(-1+DAYOFWEEK('$toDate')) DAY) and ";
         }
         else{
            $countQuery2 = "start_date>=DATE_ADD('$fromDate', INTERVAL(1-DAYOFWEEK('$fromDate')) DAY) and end_date <= DATE_ADD('$toDate', INTERVAL(1+DAYOFWEEK('$toDate')) DAY) and ";
         }
        $countQuery3=" ORDER BY  start_date ASC";
        $countQuery=$countQuery1.$countQuery2.$statusQuery.$countQuery3;
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
    public function getdepartmentonemployeeid($employeeid){
        $sql="SELECT `s`.`name` as department from `ohrm_subunit` as s, `hs_hr_employee` as h where `h`.`work_station` = `s`.`id` and `h`.`emp_number` =$employeeid";
        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($sql);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $res) {
            return $res['department'];


            # code...
        }
    }

    public function getemployeeid($empid){

        $sql="SELECT `employee_id`as id FROM `hs_hr_employee` WHERE `emp_number`= $empid";
        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($sql);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $res) {
            return $res['id'];


            # code...
        }
    }
/**
 * Getting Joined Date for gray out
 */
    public function getJoinedDate($empId){
        try {
            $timesheet = Doctrine::getTable('Employee')
                    ->find($empId);
            return $timesheet;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
 * Getting Termination Date for gray out
 */
    public function getEmployeeTerminationDate($empid){

        $sql="SELECT `termination_date`as ter_date from `ohrm_emp_termination` where `emp_number`='$empid'";

        $pdo = Doctrine_Manager::connection()->getDbh();
        $prepareQuery = $pdo->prepare($sql);
        $prepareQuery->execute();
        $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $res) {
            return $res['ter_date'];
            # code...
        }
   }

 /**
 * Getting Termination Id for gray out
 */
   public function getEmployeeTerminationId($empId){
    try {
        $timesheet = Doctrine::getTable('Employee')
                ->find($empId);
        return $timesheet;
    } catch (Exception $ex) {
        throw new DaoException($ex->getMessage());
    }
}
}
