<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewEmployeeTimesheetForm
 *
 * @author orangehrm
 */
class viewEmployeeTimesheetForm extends sfFormSymfony {

    private $timesheetService;
    public $employeeList;

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

   public function configure() {

        $this->setWidgets(array(
            'employeeName' => new ohrmWidgetEmployeeNameAutoFill()
        ));
        $this->widgetSchema->setNameFormat('time[%s]');
        $this->setValidators(array(
            'employeeName' => new ohrmValidatorEmployeeNameAutoFill()
        ));

        $this->_setLocationWidget();    

    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());
        $employeeUnique = array();
        
        if($_SESSION['userRole']=="Functional Head")
             {
                 $employeeService = new EmployeeService();
                 $employeeService->setEmployeeDao(new EmployeeDao());
  
             $employeeList = $employeeService->getFunctionalHeadEmployeeList(true);
             foreach ($employeeList as $employee) {
                 $empNumber = $employee['empNumber'];
                 if (!isset($employeeUnique[$empNumber])) {
                     $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
                     $name = $name.' - '.$employee['employeeId'];
                     if ($employee['termination_id']) {
                         $name = $name. ' ('.__('Past Employee') .')';
                     }
                     $employeeUnique[$empNumber] = $name;
                     $jsonArray[] = array('name' => $name, 'id' => $empNumber);
                 }
             }
             }
	             else{
        foreach ($this->employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            if (!isset($employeeUnique[$empNumber])) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
                $name = $name.' - '.$employee['employeeId'];
                if ($employee['termination_id']) {
                    $name = $name. ' ('.__('Past Employee') .')';
                }
                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }
    }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function setLocation($locationId){
        $this->setDefault('location', $locationId);
       
    } 

     public function getEmploymentLocationService() {
        if (is_null($this->empLocationService)) {
            $this->empLocationService = new LocationService();
            $this->empLocationService->setLocationDao(new LocationDao());
        }
        return $this->empLocationService;
    }

      private function _setLocationWidget(){
    
        $empLocationService = $this->getEmploymentLocationService();
        $statusList = $empLocationService->getLocationList();
        $locationList = array('0' => __('All'));

        foreach ($statusList as $status) {
            $locationList[$status->getId()] = $status->getName();
            $this->setWidget('location', new sfWidgetFormChoice(array('choices' => $locationList)));
        $this->setValidator('location', new sfValidatorChoice(array('choices' => array_keys($locationList))));
    
        }

}

}

?>
