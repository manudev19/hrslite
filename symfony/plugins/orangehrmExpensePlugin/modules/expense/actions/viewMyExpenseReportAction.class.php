<?php
/**
 * view my expense action calss
 */
class viewMyExpenseReportAction extends sfaction
{
    private $expenseService;

    public function getExpenseService() {

        if (is_null($this->expenseService)) {

            $this->expenseService = new ExpenseService();
        }

        return $this->expenseService;
    }

    public function execute($request)
    {
      $userRoleManager = $this->getContext()->getUserRoleManager();
      $user = $userRoleManager->getUser();
      $loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();
      //var_dump($loggedInEmpNumber); exit;
      $isPaging = $request->getParameter('pageNo');
     
      if ($isPaging > 0) {
          $pageNumber = $isPaging;
      } else {
          $pageNumber = 1;
      }

      $limit = 50;
      $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;

      $result = $this->getExpenseService()->getExpenseForEmp($loggedInEmpNumber, $limit, $offset);
      $count = count($result[1]);
      $index = $offset+1;
      $demoObject=array();
      foreach ($result[0] as $key => $expenseOfMonth) {
                    // $selectedEmployeeId = $expenseOfMonth['employee_id'];
        $departmentStatusObject =new MyExpenseReport();
        $departmentStatusObject->loadData($expenseOfMonth,$index++);
        array_push($demoObject,$departmentStatusObject);
        }
        $this->_setListComponent($demoObject,$limit,$pageNumber,$count);
        /**
        * Check if the logged in user has access to my expense report
        */
        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($user->emp_number);
       if ($employee->work_station != 3 && $employee->work_station != 8 && $employee->work_station != 2 && $employee->work_station != 4 && $employee->work_station != 5 && $employee->work_station != 9 && $employee->work_station != 18 && $employee->work_station != 34 && $employee->work_station != 36 &&  $user->id!= 1 ) {
            $message = __('You are not eligible for viewing this report');
            $this->context->getUser()->setFlash('error.nofade', $message, false);
            $this->context->getController()->forward('core', 'displayMessage');
            throw new sfStopException();
        }

    }
private function _setListComponent($systemUserList, $limit, $pageNumber, $recordCount) {

    $configurationFactory = $this->getTimesheetHeaderFactory();

    $configurationFactory->setRuntimeDefinitions(array(
        'hasSelectableRows' => false,
        'hasSummary'=>false,
        'title'=>false
    ));

    ohrmListComponent::setPageNumber($pageNumber);
    ohrmListComponent::setConfigurationFactory($configurationFactory);
    ohrmListComponent::setListData($systemUserList);
    ohrmListComponent::setItemsPerPage($limit);
    ohrmListComponent::setNumberOfRecords($recordCount);
    }


protected function getTimesheetHeaderFactory() {
    return new MyExpenseReportHeaderFactory();
    }

}



?>