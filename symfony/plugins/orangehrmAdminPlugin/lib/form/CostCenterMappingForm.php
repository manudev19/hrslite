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
 *
 */
class CostCenterMappingForm extends BaseForm {

    private $customerService;
    public $id;
    public $numberOfProjectAdmins = 5;
    public $edited = false;
    protected $projectService;

    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
            $this->projectService->setProjectDao(new ProjectDao());
        }
        return $this->projectService;
    }

    public function getCustomerService() {
        if (is_null($this->customerService)) {
            $this->customerService = new CustomerService();
            $this->customerService->setCustomerDao(new CustomerDao());
        }
        return $this->customerService;
    }

    public function configure() {
        $this->id = $this->getOption('id');
        
        $statusList = $this->getCostCenterMappingListAsJson();
        $location = $this->_getLocations();
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'costCenterId' => new sfWidgetFormSelect(array('choices' => $statusList), array("class" => "formSelect")),
            'location' => new sfWidgetFormSelect(array('choices' => $location),array("class" => "formSelect")),
            'empId' => new ohrmWidgetEmployeeNameAutoFill()
        )); 
        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false)),
            'costCenterId' => new sfValidatorString(),
            'location' => new sfValidatorString(),
            'empId' => new ohrmValidatorEmployeeNameAutoFill()
        ));

        $this->widgetSchema->setNameFormat('addProject[%s]');

        if($this->id != null){
            $this->setDefaultValues($this->id);
        }
      
    }

    private function _getLocations() {
        $locationList = array('' => '-- ' . __('Select') . ' --');
        $locationService = new LocationService();
        $locations = $locationService->getLocationList();        
        foreach ($locations as $location) {
          // if (in_array($location->id, $accessibleLocations)) {
                $locationList[$location->id] = $location->name;
           // }
        }

        return($locationList);
    }
     
    public function save() {
        $id = $this->getValue('id');
        $empData = $this->getValue('empId');
            //$locationId = $this->getValue('location');
        if(empty($id)){
            $costCenterMapping = new CostCenterMapping();     
        } else{
            $this->edited = true;
            $costCenterMapping = $this->getProjectService()->getEditCostCenterById($id);
        } 

            $costCenterMapping->setLocation($this->getValue('location'));
            $costCenterMapping->setCostCenterId($this->getValue('costCenterId'));
            $costCenterMapping->setEmpId($empData['empId']);
            //$project->setIsDeleted(CostCenterMapping::ACTIVE_PROJECT);
            $costCenterMapping->save();
    }

    private function setDefaultValues($id) {
        $costCenterMapping = $this->getProjectService()->getSavedCostCenterById($this->id);
        $this->setDefault('id', $id);
        $this->setDefault('location', $costCenterMapping[0]['location']);
        $this->setDefault('costCenterId', $costCenterMapping[0]['costcenter_id']);
        $this->setDefault('empId', array('empName' => $costCenterMapping[0]['empName'], 'empId' => $costCenterMapping[0]['empId']));
 }

       protected function saveProjectAdmins1($projectAdmins) {

        if ($projectAdmins[0] != null) {
            for ($i = 0; $i < count($projectAdmins); $i++) {
                $projectAdmin = new ProjectAdmin();
                //$projectAdmin->setProjectId($projectId);
                $projectAdmin->setEmpNumber($projectAdmins[$i]);
                //var_dump('here');
                //echo "<pre>";
                //var_dump($projectAdmin); exit;
                $projectAdmin->save();
            }
        }
    }

    protected function saveProject($project) {

        $project->setCustomerId($this->getValue('costCenterId'));
        $project->setName(trim($this->getValue('location')));
        $project->save();
        return $project->getProjectId();
    }

    // protected function getCustomerList() {

    //     $list = array("" => "-- " . __('Select') . " --");
    //     $customerList = $this->getCustomerService()->getAllCustomers();
    //     foreach ($customerList as $customer) {

    //         $list[$customer->getCustomerId()] = $customer->getName();
    //     }
    //     return $list;
    // }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $properties = array("empNumber", "firstName", "middleName", "lastName");
        $employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);

            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }
        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

    public function getCustomerListAsJson() {
        $jsonArray = array();

        $customerList = $this->getCustomerService()->getAllCustomers(true);


        foreach ($customerList as $customer) {

            $jsonArray[] = array('name' => $customer->getName(), 'id' => $customer->getCustomerId());
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }
//this will be used for namvigating to next page


    public function getCostCenterMappingListAsJson() {
        $nationalityService = new NationalityService();
        $costCenterArray = array('' => '-- ' . __('Select') . ' --');
        $costCenterList = $nationalityService->getCostCenterList();
        foreach ($costCenterList as $costCenter) {
                $costCenterArray[$costCenter->id] =  $costCenter['name'] ;
           
        }
        return $costCenterArray;
    }

 }

?>
