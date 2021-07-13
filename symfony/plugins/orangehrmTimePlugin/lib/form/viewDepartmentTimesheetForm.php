<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewDepartmentTimesheetForm
 *
 * @author orangehrm
 */
class viewDepartmentTimesheetForm extends sfFormSymfony {

    private $timesheetService;
    public  $employeeList;
    private $companyStructureService;
    
    public function getTimesheetService() {
        if (is_null($this->timesheetService)) {
            $this->timesheetService = new TimesheetService();
        }
        return $this->timesheetService;
    }

     public function getEmploymentLocationService() {
        if (is_null($this->empLocationService)) {
            $this->empLocationService = new LocationService();
            $this->empLocationService->setLocationDao(new LocationDao());
        }
        return $this->empLocationService;
    }

    public function setSubUnit($subUnit){
        $this->setDefault('sub_unit', $subUnit);
    }

    public function setLocation($locationId){
        $this->setDefault('location', $locationId);
       
    }
    public function configure() {
        $this->_setSubunitWidget(); 
        $this->_setLocationWidget();         
    }

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    private function _setSubunitWidget() {
        $subUnitList = array(0 => __("All"));
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
        $tree = $treeObject->fetchTree();
        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
        $this->setWidget('sub_unit', new sfWidgetFormChoice(array('choices' => $subUnitList)));
        $this->setValidator('sub_unit', new sfValidatorChoice(array('choices' => array_keys($subUnitList))));
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
