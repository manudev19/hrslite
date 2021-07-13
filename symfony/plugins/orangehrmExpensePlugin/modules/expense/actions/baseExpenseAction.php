<?php
abstract class baseExpenseAction extends sfAction {
    
    private $employeeService;
    
    public function preExecute() {
        $sessionVariableManager = new DatabaseSessionManager();
        $sessionVariableManager->setSessionVariables(array(
            'orangehrm_user' => Auth::instance()->getLoggedInUserId(),
        ));
        $sessionVariableManager->registerVariables();
        $this->setOperationName(OrangeActionHelper::getActionDescriptor($this->getModuleName(), $this->getActionName()));  
        
            
        /* For highlighting corresponding menu item */
        $request = $this->getRequest();        
        $initialActionName = $request->getParameter('initialActionName', '');

        if (empty($initialActionName)) {
            $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
            $empNumber = $request->getParameter('empNumber');
            
            if (!empty($loggedInEmpNum) && $loggedInEmpNum == $empNumber) {
                $request->setParameter('initialActionName', 'viewMyDetails');
            } else {
                $request->setParameter('initialActionName', 'viewEmployeeList');
            }
        }        
        
    }
    ?>