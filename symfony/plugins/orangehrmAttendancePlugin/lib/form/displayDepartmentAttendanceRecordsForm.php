<?php

class displayDepartmentAttendanceRecordsForm extends sfFormSymfony {

   
    private $timesheetService;
    public  $employeeList;
    private $companyStructureService;

    public function configure() 
    {
        //$month = $this->getOption('month');
        //$year = $this->getOption('year');
        $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September','October', 'November', 'December'];
        $startYear   = "2006";
        $currentYear = date("Y");

        $yearArray = [];
        for ($i=$currentYear; $i >= $startYear; $i--) {
            $yearArray[$i] = $i;
        }

        $this->setWidgets(array(
            'employeeId' => new sfWidgetFormInputHidden(),
            'month' => new sfWidgetFormSelect(array('choices' => $monthArray), array('id' => 'month')),
            'year' => new sfWidgetFormSelect(array('choices' => $yearArray), array('id' => 'year'))
        ));
        $currentMonth = date("n");
        
        $this->setDefaults(
            array(
                'month' => $currentMonth-1
            )
        );
        $this-> _setSubunitWidget(); // input box for department list

        // Validate that if both from and to date are given, form date is before to date.
    } // End of configure function.

    public function getMonths() 
    {

    }

    public function getYears() 
    { 

    }

    public function getDownloadActionButtons() {
        return array(
            'btnDownload' => new ohrmWidgetButton('btnDownload', 'Download', array()),
        );
    }
    
    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {
            $this->timesheetService = new TimesheetService();
        }
        return $this->timesheetService;
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

        $this->setValidator('sub_unit', new sfValidatorChoice(array('choices' => array_keys($subUnitList),'required'=>true )));
        //$this->setDefault('sub_unit', $this->workStation);
    }

    public function setSubUnit($subUnit){

        $this->setDefault('sub_unit', $subUnit);
    }

    public function setSelectedDate($toDate,$fromDate){

        $this->setDefault('toDate', $toDate);
        $this->setDefault('fromDate', $fromDate);
    }

    public function setMonth($selectedMonth)
    {
        $this->setDefault('month', $selectedMonth);
    }

    public function setYear($selectedYear)
    {
        $this->setDefault('year', $selectedYear);
    }

    public function setStatus($selectedStatus)
    {
        $this->setDefault('status', $selectedStatus);
    }

}


?> 