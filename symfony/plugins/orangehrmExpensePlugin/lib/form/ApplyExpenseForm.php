<?php

class ApplyExpenseForm extends sfForm {

    public function getCustomerService() {
        if (is_null($this->customerService)) {
            $this->customerService = new CustomerService();
            $this->customerService->setCustomerDao(new CustomerDao());
        }
        return $this->customerService;
    }

    public function getExpenseService() {
        if (is_null($this->timesheetService)) {
            $this->timesheetService = new ExpenseService();
        }
        return $this->timesheetService;
    }

    public function __getProjectList() {
        $list = array();
        $projectList = $this->getExpenseService()->getProjectNameList();
        foreach ($projectList as $project) {
            $list[''] = __("Select Your Project");
            $list[$project['projectId']] = $project['projectName'];
        }
    return $list;
    }

    public function __getCustomerList() {
        $list = array();
        $customerList = $this->getCustomerService()->getAllCustomers();
        foreach ($customerList as $customer) {
            $list[''] = __("Select Your Client");
            $list[$customer->customerId] = $customer->name;
        }
        return $list;
    }

    public function getExpenseTypeConfigurationService(){
        if(is_null($this->expenseTypeConfigurationService)) {
            $this->expenseTypeConfigurationService = new ExpenseTypeConfigurationService();
        }
        return $this->expenseTypeConfigurationService;
    }
    public function __getExpenseType() {
        $list = array();
        $expenseType = $this->getExpenseTypeConfigurationService()->getExpenseTypeList();
        foreach ($expenseType as $expenseType) {
            $list[''] = __("Select");
            $list[$expenseType->getId()] = $expenseType->getName();
        }
        return $list;
    }

    public function configure() {

        $customerList = $this->__getCustomerList();
        $projectList = $this->__getProjectList();
        $this->expenseType = $this->__getExpenseType();
        $expenseId = $this->getoption('expenseId');
       /*var_dump($expenseId);
       echo "<pre>"; var_dump($this); exit;*/
       /* $this->setWidgets([
            'customerName' => new sfWidgetFormSelect(array('choices' => $customerList),array('class' => 'cname')),
            'projectName' => new sfWidgetFormSelect(array('choices' => ''), array('class' => 'pname'))
        ]);*/

        if($expenseId != null) {
            $expenseItems = $this->getExpenseItem($expenseId);
            $numberofRows = $expenseItems[0];
            $clientAndProject = $expenseItems[1];
             /*echo "<pre>";
             var_dump($expenseItems); exit;*/
            // echo "<pre>";
            // var_dump("zdsf---",$clientAndProject);
            //  exit;
                         
            $projectListForClient = $this->getProjectListForCLient($clientAndProject[0]['customerId']);
            $this->setWidgets([
                'customerName' => new sfWidgetFormSelect(array('choices' => $customerList),array('class' => 'cname')),
                'projectName' => new sfWidgetFormSelect(array('choices' =>  $projectListForClient), array('class' => 'pname')),
                'tripName' => new sfWidgetFormInputText()
            ]);
            $this->setDefault('customerName', $clientAndProject[0]['customerId']);
            $this->setDefault('projectName', $clientAndProject[0]['projectId']);
            $this->setDefault('tripName', $clientAndProject[0]['tripName']);
            // echo "<pre>";
            // var_dump($clientAndProject[0]); exit;
            $totalRows = count($numberofRows);
            $expenseRows = new sfForm();
            $count=0;
            for ($i=0; $i < $totalRows; $i++) { 
                $rowForm = new ExpenseRowForm();
                $x = $rowForm->getWidgets();
                $y = array('Date' => new ohrmWidgetDatePicker(array(), array('id' => 'expense_date_'.$i, 'class' => 'tdate')),
                    'expense_type' => new sfWidgetFormSelect(array('choices' => $this->expenseType), array('class' => 'expenseType')));
                $z = array('attachment' => new sfWidgetFormInputFileEditable(
                    array('edit_mode' => false,
                        'with_delete' => false,
                        'file_src' => ''),
                    array('class' => 'attachment')));
                  

                $widgets = array_merge($y,$x,$z);
                $rowForm->setWidgets($widgets);
                $rowForm->setDefault('Date',$numberofRows[$i]['date_of_expense']);
                $rowForm->setDefault('expense_type',$numberofRows[$i]['expenseTypeId']);
                $rowForm->setDefault('message',$numberofRows[$i]['message']);
                $rowForm->setDefault('paid_by_company',$numberofRows[$i]['paid_by_company']);
                $rowForm->setDefault('currency',$numberofRows[$i]['currency']);
                $rowForm->setDefault('amount',$numberofRows[$i]['amount']);
                $rowForm->setDefault('attachment',$numberofRows[$i]['file_name']);
                $expenseRows->embedForm($count, $rowForm);

                $count++;
            }
          /*  echo"<pre>";
                var_dump($expenseRows); exit;*/
            $this->embedForm('initialRows', $expenseRows);
        } else {
            $this->setWidgets([
                'customerName' => new sfWidgetFormSelect(array('choices' => $customerList),array('class' => 'cname')),
                'projectName' => new sfWidgetFormSelect(array('choices' => ''), array('class' => 'pname')),
                'tripName' => new sfWidgetFormInputText()
            ]);
              $this->setValidators([
                'tripName' => new sfValidatorString(
                    array('required' => true),
                    array('required' => 'This value is required.')
                )
              ]);
            $timesheetRows = new sfForm();
            $emptyRowForm = new ExpenseRowForm();
            $x = $emptyRowForm->getWidgets();
            $y = array(
                'Date' => new ohrmWidgetDatePicker(array(), array('id' => 'expense_date_0', 'class' => 'tdate')),
                'expense_type' => new sfWidgetFormSelect(array('choices' => $this->expenseType), array('class' => 'expenseType'))
            );
            $z = array('attachment' => new sfWidgetFormInputFileEditable(
                array('edit_mode' => false,
                    'with_delete' => false,
                    'file_src' => ''),
                array('class' => 'attachment')));
             //var_dump($y); exit;
            $widgets = array_merge($y,$x,$z);
                // var_dump($widgets);
            $emptyRowForm->setWidgets($widgets);

            $timesheetRows->embedForm(0, $emptyRowForm);

            $this->embedForm('initialRows', $timesheetRows);
        }
    }

    public function getExpenseItem($expenseId='')
    {
        // var_dump($expenseId);exit;
        $expenseItems = $this->getExpenseService()->getExpenseItem($expenseId);
        //aisha-1 echo "<pre>";var_dump($expenseItems);exit;
        if($expenseItems[1]!=null){
            return $expenseItems;
        }
        else{
            return NULL;
        }
    }
    public function addRow($num, $values) {
            // var_dump($num)
        $rowForm = new ExpenseRowForm();
        $x = $rowForm->getWidgets();
        $y = array(
            'Date' => new ohrmWidgetDatePicker(array(), array('id' => 'expense_date_'.$num, 'class' => 'tdate')),
            'expense_type' => new sfWidgetFormSelect(array('choices' => $this->expenseType), array('class' => 'expenseType'))
        );
        $z = array('attachment' => new sfWidgetFormInputFileEditable(
                array('edit_mode' => false,
                    'with_delete' => false,
                    'file_src' => ''),
                array('class' => 'attachment')));

        $widgets = array_merge($y,$x,$z);
        $rowForm->setWidgets($widgets);
                // var_dump($widgets);exit;
        $this->embeddedForms['initialRows']->embedForm($num, $rowForm);
        $this->embedForm('initialRows', $this->embeddedForms['initialRows']);
    }

    /**
     *
     * @return TimesheetPeriodService
     */
    
    /**
     * Set TimesheetData Access Object
     * @param TimesheetService $TimesheetService
     * @return void
     */

    public function save() {
        $file = $this->getValue('attachment');

        $timesheet = $this->getExpenseService()->getTimesheetById($this->timesheetId);

        $this->date = $timesheet->getStartDate();
        $this->endDate = $timesheet->getEndDate();
        $this->startDate = $this->date;

    }

    private function _saveResume($file){
        $tempName = $file->getTempName();
    }

    public function getProjectListForCLient($clientId)
    {
        $list = array();
        $projectList= $this->getExpenseService()->getProjectListForCLient($clientId);
        foreach ($projectList as $projectList) {
            $list[''] = __("Select");
            $list[$projectList->getProjectId()] = $projectList->getName();
        }
        return $list;
    }
}

?>
