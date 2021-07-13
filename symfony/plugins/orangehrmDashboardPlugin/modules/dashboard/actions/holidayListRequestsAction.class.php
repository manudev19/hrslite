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
 * Description of holidayListRequestsAction
 */
class holidayListRequestsAction extends BaseDashboardAction {

    protected $holidayService;
    protected $employeeService;

    public function preExecute() {
        $this->setLayout(false);
        parent::preExecute();
    }

    public function execute($request) {
       
        $userDetails = $this->getLoggedInUserDetails();
        $loggedInEmpNumber = $userDetails['loggedUserEmpId'];

        $employee = $this->getemployeeService()->getEmployee($loggedInEmpNumber);
        $subUnit  = $employee->work_station;

        if($subUnit){

            $this->holidayList = array();
            $holidayArray =$this->getholidayService()->getFullHolidayList($employee->work_station);

            foreach ($holidayArray as $holidayObject) {
                
                if ($holidayObject instanceof Holiday) {
                   $currentYear = date("Y");
                   $date = $holidayObject->getDate();
                   $holidayYear = date('Y', strtotime($date));
                   if($currentYear <= $holidayYear) {
                    $desc = $holidayObject->getDescription();
                    $this->holidayList[] = $date . " - " . $desc;
                    }
                }
            }
        }
    }

    /**
     * Get work schedule service
     * @return HolidayService
     */
    public function getholidayService() {
        if (!($this->holidayService instanceof HolidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Get work schedule service
     * @return HolidayService
     */
    public function getemployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }



}
