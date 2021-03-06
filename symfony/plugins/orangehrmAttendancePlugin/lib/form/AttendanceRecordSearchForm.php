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
class AttendanceRecordSearchForm extends sfForm {

    public function configure() {

        $month = $this->getOption('month');
        $year = $this->getOption('year');
        $employeeId = $this->getOption('employeeId');
        $trigger = $this->getOption('trigger');

        $this->setWidgets(array(
            'employeeName' => new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()), array('class' => 'formInputText')),
            'month' => new sfWidgetFormSelect(array('choices' => $this->getMonths()), array('id' => 'month')),
            'year' => new sfWidgetFormSelect(array('choices' => $this->getYears()), array('id' => 'year'))
        ));
        
        if ($trigger) {
            $this->setDefault('employeeName', $this->getEmployeeName($employeeId));
            $this->setDefault('month', $month);
            $this->setDefault('year', $year);
        }

        $this->widgetSchema->setNameFormat('attendance[%s]');

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $this->setValidators(array(
            'employeeName' => new ohrmValidatorEmployeeNameAutoFill()
        ));
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }
    public function getMonths() {
    }

    public function getYears() {        
    }

    public function getDownloadActionButtons() {
        return array(
            'btnDownload' => new ohrmWidgetButton('btnDownload', 'Download', array()),
        );
    }
    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $requiredMarker = ' <em> *</em>';

        $labels = array(
            'employeeName' => __('Employee Name'),
            'month' => __('Month') ,
            'Year' => __('Year')
        );

        return $labels;
    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        
        if($_SESSION['userRole']=="Functional Head")
        {   
            $employeeList = $employeeService->getFunctionalHeadEmployeeList(true);
        }
        else
            {
        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntities('Employee');
            }
        $employeeUnique = array();
        $jsonArray[] = array('name' => __('All'), 'id' => '');
        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name = $employee->getFullNameAndId();
                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function getEmployeeName($employeeId) {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($employeeId);
        if ($employee->getMiddleName() != null) {
            return $employee->getFirstName() . " " . $employee->getMiddleName() . " " . $employee->getLastName();
        } else {
            return $employee->getFirstName() . " " . $employee->getLastName();
        }
    }

}