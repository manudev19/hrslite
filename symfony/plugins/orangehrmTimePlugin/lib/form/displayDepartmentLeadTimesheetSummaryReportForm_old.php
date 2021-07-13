<?php

class displayDepartmentLeadTimesheetSummaryReportForm extends sfFormSymfony {

   
    private $timesheetService;
    public  $employeeList;
    private $companyStructureService;
    private $workStation;

    public function configure() 
    {

        $sfUser = sfContext::getInstance()->getUser();
        $userId = $sfUser->getAttribute('auth.empNumber');
        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($userId);
        if ($employee->role_in_department == 1) {
            $this->workStation = $employee->work_station;
        }

        $this->setWidgets(
            array(
                 'employeeName' => new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()), array('class' => 'formInputText')),
                'employeeId' => new sfWidgetFormInputHidden(),
                
                
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
                            'NOT SUBMITTED' => 'NOT SUBMITTED', 
                            'SUBMITTED' => 'NOT APPROVED',
                            'APPROVED' => 'APPROVED',
                            
                        ),
                    )
                ),

                'not_submitted' => new sfWidgetFormInputCheckbox(
                    array('value_attribute_value' => 'not_submitted')
                ),
                
                'submitted' => new sfWidgetFormInputCheckbox(
                    array('value_attribute_value' => 'submitted', 'default' => false)
                ),

                'approved' => new sfWidgetFormInputCheckbox(
                    array('value_attribute_value' => 'approved', 'default' => false)
                )
            )
        );
        /*$this->setDefaults(
            array(
                'status' => 'NOT SUBMITTED',
            )
        );*/


        $this-> _setSubunitWidget(); // input box for department list
        /*$this->setValidators(array(
                'month'    => new sfValidatorString(),
                'year'   => new sfValidatorString(),
                'status' => new sfValidatorString(),
                'sub_unit' => new sfValidatorString()
        ));*/   

        $this->setDefault('from_date',date('Y-m-01'));
        $this->setDefault('to_date',date('Y-m-t'));
        $this->setDefault('not_submitted',true);
        
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

    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        //$employeeService->setEmployeeDao(new EmployeeDao());
        //$employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntities('Employee');
        $employeeList = $employeeService->getDepartmentEmployees($this->workStation);
        $employeeUnique = array();
        $jsonArray[] = array('name' => __('All'), 'id' => '');

        foreach ($employeeList as $employee) {

            if (!isset($employeeUnique[$employee['empNumber']])) {

                $name = $employee['firstName']." ".$employee['middleName']." ".$employee['lastName'] ;
                $employeeUnique[$employee['empNumber']] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $employee['empNumber']);
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
        $this->setWidget(
            'sub_unit', 
            new sfWidgetFormChoice(
                array('choices' => $subUnitList)
            )
        );
        $this->getWidget('sub_unit')->setAttribute('disabled', 'disabled');
        $this->setValidator('sub_unit', new sfValidatorChoice(array('choices' => array_keys($subUnitList),'required'=>true )));
        $this->setDefault('sub_unit', $this->workStation);
    }

    public function setSubUnit($subUnit){

        $this->setDefault('sub_unit', $this->workStation);
    }

    /*public function setStatus($selectedStatus)
    {
        $this->setDefault('status', $selectedStatus);
    }*/

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

    public function setNotSubmitted($selectedNotSubmitted)
    {
        $this->setDefault('not_submitted', $selectedNotSubmitted);
    }

    public function setSubmitted($selectedSubmitted)
    {
        $this->setDefault('submitted', $selectedSubmitted);
    }

    public function setApproved($selectedApproved)
    {
        $this->setDefault('approved', $selectedApproved);
    }
    
}


?> 
