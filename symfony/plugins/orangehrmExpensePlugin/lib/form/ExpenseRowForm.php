<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TimesheetRowForm
 *
 * @author orangehrm
 */
class ExpenseRowForm extends sfForm {
	private $timesheetDao;
        private $timesheetService;
        private $customerService;
        private $timesheetPeriodService;
        private $expenseTypeConfigurationService;
        private $currencyService;


        // protected static $choices= array('Select','Hotel','Flight','Cab','Parking','Others');
        // protected static $client= array('Select','FHLB-Newyork','FHLB-Atlanta');
        // protected static $project= array('Select','21','22','23');
        protected static $paid= array('Select','NO','YES');
        // protected static $amount= array('dollars');
        private $allowedFileTypes = array(
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "doc" => "application/msword",
        "doc" => "application/x-msword",
        "doc" => "application/vnd.ms-office",
        "odt" => "application/vnd.oasis.opendocument.text",
        "pdf" => "application/pdf",
        "pdf" => "application/x-pdf",
        "rtf" => "application/rtf",
        "rtf" => "text/rtf",
        "txt" => "text/plain"
    );

    public function configure() {
        $currency = $this->getCurrencyList();
        $this->currency = $currency;
	    $this->setValidators($this->getFormValidators());
        $this->widgetSchema->setNameFormat('expense[%s]');
    }

     protected function getFormValidators() {

        $validators = array(
             'customerName'=> new sfValidatorChoice(array('required' => true, 'choices' => array_keys($customerList))),
            'projectName'=> new sfValidatorChoice(array('required' => true, 'choices' => array_keys($projectList))),
            'toDelete' => new sfValidatorPass(array('required' => true)),
            'noAttachment' => new sfValidatorPass(array('required' => true)),
            'Date' => new sfValidatorString(array('required' => true)),
            'description' => new sfValidatorString(array('required' => true)),
            'expense_type' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($expenseType))),          
            'paid_by_company' => new sfValidatorString(array('required' => true)),
            'attachment' => new sfValidatorFile(array('required' => true)),
            'currency' => new sfValidatorString(array('required' => true)),
            'amount' => new sfValidatorNumber(array('required' => true))
        );
        return $validators;
    }

     public function getTimesheetService() {

        if (is_null($this->timesheetService)) {
            $this->timesheetService = new TimesheetService();
        }
        return $this->timesheetService;
    }

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
    public function getStylesheets() {

        $stylesheets = parent::getStylesheets();
        $stylesheets[plugin_web_path('orangehrmPerformancePlugin','css/searchReviewSuccess.css')] = 'all';
        return $stylesheets;
        
    }

 /**
     * Returns Currency Service
     * @returns CurrencyService
     */
    public function getCurrencyService() {
        if (is_null($this->currencyService)) {
            $this->currencyService = new CurrencyService();
        }
        return $this->currencyService;
    }

    /**
     * Returns Currency List
     * @return array
     */
    private function getCurrencyList() {
        $list = array("" => "-- " . __('Select') . " --");
        $currencies = $this->getCurrencyService()->getCurrencyList();

        /*foreach ($currencies as $currency) {
            $list[$currency->getCurrencyId()] = $currency->getCurrencyName();
        }*/
        $list['INR'] = "INR";
        $list['USD'] = "USD";
        return $list;
    }



    public function getWidgets()
    {
        $widgetArray = array( 
        	
            'toDelete' => new sfWidgetFormInputCheckbox(array(), array('class' => 'toDelete')),

            'noAttachment' => new sfWidgetFormInputCheckbox(array(), array('class' => 'noAttachment')), 
            
            'message' => new sfWidgetFormTextarea(array(),array('class' => 'description', 'style' => 'height:30px; width:150px')),
            'paid_by_company' => new sfWidgetFormSelect(array('choices' => self::$paid), array('class' => 'paid')),

            'currency' => new sfWidgetFormSelect(array('choices' => $this->currency), array('class' => 'currency')),
            'amount' => new sfWidgetFormInputText(array(),array('class' => 'amount','maxlength' => 10, 'type' => 'number')),

        );
           
        return $widgetArray;
    }

    public function setTimesheetService(TimesheetService $timesheetService) {

        $this->timesheetService = $timesheetService;
    }
    
    public function getExpenseTypeConfigurationService(){

        if(is_null($this->expenseTypeConfigurationService)){

            $this->expenseTypeConfigurationService = new ExpenseTypeConfigurationService();
        }
        return $this->expenseTypeConfigurationService;
    }

    public function setExpenseTypeConfigurationService(ExpenseTypeConfigurationService $expenseTypeConfigurationService){

        $this->expenseTypeConfigurationService = $expenseTypeConfigurationService;

    }
}

