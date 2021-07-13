<?php

class auditLogForm extends sfFormSymfony {

    private $AuditLogService;
    public  $employeeList;
    public $empNumber;
    private $employeeService;
   
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {

        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }
    public function configure() 
    {
        $actions = array('All' => __('All'), 'Insert' => __('Insert'), 'Update' => __('Update'), 
        'Delete' => __('Delete'));
       // $this->getSupervisorAndAdminListAsJson();
        $sections=$this->getSectionListAsJson();
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
    
        $this->setWidgets(
            array(
                'actions' => new sfWidgetFormSelect(array('choices' => $actions), array('class' => 'actions', 'id' => 'actions', 'maxlength' => 50)),
                'action_owner' => new ohrmWidgetEmployeeNameAutoFill(),
                'from_date' => new ohrmWidgetDatePicker(
                    array(),
                    array('id' => 'from_date')
                ),
                'to_date' => new ohrmWidgetDatePicker(
                    array(), 
                    array('id' => 'to_date')
                ),
                'affected_employee' => new ohrmWidgetEmployeeNameAutoFill(array(),array('id'=>'affected_employee')),
                'sections' => new sfWidgetFormSelect(array('choices' => $sections), array('class' => 'sections', 'id' => 'sections', 'maxlength' => 50)),
                'isDownloadable'=> new sfWidgetFormInputHidden(array(), array())
            )
        );
        
        $this-> _setModuleWidget(); 
        $this->setDefault('from_date',date('2021-02-15'));
        $this->setDefault('to_date',date('Y-m-t'));
        $this->setDefault('affected_employee', __('Type for hints').'...');
       

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
                $name = $name.' - '.$employee['employeeId'];
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

    public function getSectionListAsJson() {
        $myInfoScreens=array('Personal Details','Contact Details','Emergency Contacts','Dependents','Immigration','Job','Salary','Report-to','Qualifications','Memberships','Configuration');
        $jsonArrayFirstElement = array('All'=>__('All'));
        $sectionList =$this-> getAuditLogService()->getSectionList();
        foreach ($sectionList as $section) {
                $jsonArray[ $section['menu_title']] =  $section['menu_title'];
            
        }
        foreach ($myInfoScreens as $screens) {
            $jsonArray[ $screens] =  $screens;
       
      }
     /**
     * Sorting logic
     */
      $jsonArrayResult=array_diff($jsonArray,$jsonArrayFirstElement);
      $jsonArraySortResult=[];

      sort($jsonArrayResult);
      foreach($jsonArrayResult as $key=>$data){
      $jsonArraySortResult[$data]=($data);
      }
 
      return $jsonArrayFirstElement+$jsonArraySortResult;
      
   }


   /* public function getSupervisorAndAdminListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
      //  $employeeService->setEmployeeDao(new EmployeeDao());

        $employeeList = $employeeService->getSupervisorList(true);
        foreach ($employeeList as $employee) {

            $name = $employee->getFirstName() . " " . $employee->getMiddleName();
            $name = trim(trim($name) . " " . $employee->getLastName());
            $name= $name .' - '.$employee->getEmployeeId();
            if ($employee->getTerminationId()) {
                $name = $name. ' ('.__('Past Employee') .')';
            }
            $jsonArray[] = array('name' => $name, 'id' => $employee->getEmpNumber());
        }
        $employeeList = $employeeService-> getAdminList(true);
        foreach ($employeeList as $admin) {

            $name = $admin->getFirstName() . " " . $admin->getMiddleName();
            $name = trim(trim($name) . " " . $admin->getLastName());
            $name= $name .' - '.$admin->getEmployeeId();
            if ($admin->getTerminationId()) {
                $name = $name. ' ('.__('Past Employee') .')';
            }
            $jsonArray[] = array('name' => $name, 'id' => $admin->getEmpNumber());
        }
        $jsonString=array_unique( $jsonArray, SORT_REGULAR );
      //  $jsonString = json_encode($jsonArray);

        return $jsonString;
    }*/

   
    private function _setModuleWidget() {
        $treeObject = $this->getAuditLogService()->getAffectedModules();
       
        $moduleArray = array('All'=> __("All"));
        foreach ($treeObject as $tree) {
            $moduleArray[$tree['moduleName']] =$tree['moduleName'];
        }
        $this->setWidget('modules', new sfWidgetFormChoice(array('choices' =>$moduleArray)));

        $this->setValidator('modules', new sfValidatorChoice(array('choices' => $moduleArray,'required'=>true )));
      
    }
    
    public function setModules($selectedModule){

        $this->setDefault('modules', $selectedModule);
    }

    public function setSections($selectedSection)
    {
        $this->setDefault('sections', $selectedSection);
    }

      public function setFromDate($FromDate)
    {
        $this->setDefault('from_date', $FromDate);
    }

    public function setToDate($ToDate)
    {
        $this->setDefault('to_date', $ToDate);
    }

    public function setActions($selectedAction) 
    {
        $this->setDefault('actions', $selectedAction);
    }

    public function setActionOwner($selectedActionOwner) 
    {
        $this->setDefault('action_owner', $selectedActionOwner);
    }

    public function setAffectedEmployee($selectedAffectedEmployee) 
    {
        $this->setDefault('affected_employee', $selectedAffectedEmployee);
    }
 
}


?> 
