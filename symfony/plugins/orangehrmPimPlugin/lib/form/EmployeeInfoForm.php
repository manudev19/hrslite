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

class EmployeeInfoForm extends BaseForm {
    
    public $employeeList;

    public function configure() {

        $this->setWidgets(array(
            'employeeName' => new ohrmWidgetEmployeeNameAutoFill (),
            'employeeId' => new sfWidgetFormInputHidden(),
        ));

        $this->setValidators(array(
            'employeeName' => new ohrmValidatorEmployeeNameAutoFill(),
            'employeeId' => new sfValidatorString(),
        ));
    }

    public function getEmployeeListAsJson() {
     
        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        $employeeUnique = array();
        foreach ($this->employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            if (!isset($employeeUnique[$empNumber])) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
                $name=$name.' - '.$employee['employeeId'];
                if ($employee['termination_id']) {
                    $name = $name. ' ('.__('Past Employee') .')';
                }
                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }

        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }
}

?>
