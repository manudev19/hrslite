<?php

class viewExpenseReportForm extends sfFormSymfony {

   
    private $timesheetService;
    public  $employeeList;
    private $companyStructureService;

    public function configure() 
    {
        $projectList = $this->__getProjectList();

        $this->setWidgets(
            array(
                 'employeeName' => new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()), array('class' => 'formInputText')),
                'employeeId' => new sfWidgetFormInputHidden(),
                //'selected_employee' => new sfWidgetFormInputHidden(),
                //'selected_employee_name' => new sfWidgetFormInputHidden(),
             'projectName' => new sfWidgetFormSelect(array('choices' => $projectList), array('class' => 'pname')),

                'from_date' => new ohrmWidgetDatePicker(
                    array(),
                    array('id' => 'from_date')
                ),
                'to_date' => new ohrmWidgetDatePicker(
                    array(), 
                    array('id' => 'to_date')
                ),
                'status' => new sfWidgetFormChoice(
                    array(
                        'expanded' => true,
                        'choices'  => array(
                            'REJECTED' => 'REJECTED', 
                            'SUBMITTED' => 'SUBMITTED',
                            'APPROVED' => 'APPROVED',
                            'PROCESSED' => 'PROCESSED',
                            'ALL' => 'ALL',
                        ),
                    )
                ),
            )
        );
        
        $this->setDefaults(
            array(
                'status' => 'SUBMITTED',
            )
        );


        $this-> _setSubunitWidget(); // input box for department list
        /*$this->setValidators(array(
                'month'    => new sfValidatorString(),
                'year'   => new sfValidatorString(),
                'status' => new sfValidatorString(),
                'sub_unit' => new sfValidatorString()
        ));*/   

        $this->setDefault('from_date',date('Y-m-01'));
        $this->setDefault('to_date',date('Y-m-t'));
        
        // Validate that if both from and to date are given, form date is before to date.

        $this->getValidatorSchema()->setPostValidator(
            new ohrmValidatorSchemaCompare(
                'from_date', 
                sfValidatorSchemaCompare::LESS_THAN_EQUAL, 
                'to_date',
                array(
                    'throw_global_error' => true,
                    'skip_if_one_empty' => true
                ),
                array(
                    'invalid' => 'The from date ("%left_field%") must be before the to date ("%right_field%")'
                )
            )
        ); 

        $this->setValidator(
            'from_date', 
            new ohrmDateValidator(
                array('date_format' => $inputDatePattern, 'required' => false),
                array('invalid' => 'Date format should be ' . $inputDatePattern)
            )
        );
        
        $this->setValidator(
            'to_date', 
            new ohrmDateValidator(
                array('date_format' => $inputDatePattern, 'required' => false),
                array('invalid' => 'Date format should be ' . $inputDatePattern)
            )
        );

    } // End of configure function.

    public function __getProjectList() {

        $list = array();
        $projectList = $this->getTimesheetService()->getProjectNameList();

        foreach ($projectList as $project) {
             $list[''] = __("All");
             $list[$project['projectId']] = $project['projectName'];

        }
        return $list;

    }

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntities('Employee');
        $employeeUnique = array();
        $jsonArray[] = array('name' => __('All'), 'id' => '');
        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee->getEmpNumber()])) {

                $name =$employee->getFullNameAndId();
                $employeeUnique[$employee->getEmpNumber()] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
            }
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }

   
    public function getDownloadActionButtons() {
        return array(
            'btnDownload' => new ohrmWidgetButton('btnDownload', 'Download', array()),
        );
    }

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {
            $this->timesheetService = new ExpenseService();
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
    }

    public function setSubUnit($subUnit){

        $this->setDefault('sub_unit', $subUnit);
    }

    public function setStatus($selectedStatus)
    {
        $this->setDefault('status', $selectedStatus);
    }

      public function setFromDate($selectedFromDate)
    {
        $this->setDefault('from_date', $selectedFromDate);
    }

    public function setToDate($selectedToDate)
    {
        $this->setDefault('to_date', $selectedToDate);
    }

    public function setSelectedEmployee($selectedEmployeeDetails) 
    {
        $this->setDefault('employeeName', $selectedEmployeeDetails);
    }

    public function setSelectedProject($selectedProject)
    {
        $this->setDefault('projectName', $selectedProject);
    }
}


?> 
