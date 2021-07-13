<?php
/**
 * view my expense action calss
 */
class viewPreonboardingEmployeeTableAction extends sfaction
{
    private $expenseService;

    // public function getExpenseService() {

    //     if (is_null($this->expenseService)) {

    //         $this->expenseService = new ExpenseService();
    //     }

    //     return $this->expenseService;
    // }
    public function getEmployeeService() {
        if(is_null( $this->EmployeeService))
        {
          $this->EmployeeService = new EmployeeService();
        }
        return $this->EmployeeService;
    }

    public function execute($request)
    {
      $userRoleManager = $this->getContext()->getUserRoleManager();
      $user = $userRoleManager->getUser();
      $loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();
      $result= $this->getEmployeeService()->getEmployeeDetailForTable($loggedInEmpNumber);

     $AdminShow=$result[0];
     $ManagerShow=$result[1];
     if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes"){
       $demoAdminObject=array();
       foreach ($AdminShow as $key => $res) 
       {
        $EmployeeObject =new MyPreonboardingEmployee();
        $EmployeeObject->loadData($res);
        array_push($demoAdminObject,$EmployeeObject);
       }
     $this->_setListComponent($demoAdminObject,$limit,$pageNumber,$count);
    }
    else{
        $demoManagerObject=array();
        foreach ($ManagerShow as $key => $res) 
        {
         $EmployeeObject =new MyPreonboardingEmployee();
         $EmployeeObject->loadDataManager($res);
         array_push($demoManagerObject,$EmployeeObject);
       }
        $this->_setListComponent($demoManagerObject,$limit,$pageNumber,$count);
     }
    
        /**
        * Check if the logged in user has access to my expense report
      */
      /*
        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($user->emp_number);
        if ($employee->work_station != 3 && $employee->work_station != 8 && $employee->work_station != 2 && $employee->work_station != 4 && $employee->work_station != 5 && $employee->work_station != 9 && $employee->work_station != 18 && $employee->work_station != 34 && $employee->work_station != 36 &&  $user->id!= 1 ) {
            $message = __('You are not eligible for viewing this report');
            $this->context->getUser()->setFlash('error.nofade', $message, false);
            $this->context->getController()->forward('core', 'displayMessage');
            throw new sfStopException();
        }*/

    }
private function _setListComponent($UserList, $limit, $pageNumber, $recordCount) {
    $configurationFactory = $this->getTimesheetHeaderFactory();
    $configurationFactory->setRuntimeDefinitions(array(
        'hasSelectableRows' => false,
        'hasSummary'=>false,
        'title'=>false
    ));

    ohrmListComponent::setPageNumber($pageNumber);
    ohrmListComponent::setConfigurationFactory($configurationFactory);
    ohrmListComponent::setListData($UserList);
    ohrmListComponent::setItemsPerPage($limit);
    ohrmListComponent::setNumberOfRecords($recordCount);
    }


protected function getTimesheetHeaderFactory() {
    return new MyPreonboardingEmployeeHeaderFactory();
    }

}

?>