<?php
date_default_timezone_set('Asia/Calcutta'); 
/*Author : Manohar NG 
* This is page is for saving and editing the expense
*/
class applyExpenseAction extends baseTimeAction {
    private $totalrows= 0;
    private $employeeService;
 
    public function getExpenseService()
    {
        if(is_null($this->expenseService)){
            $this->expenseService = new ExpenseService();
        }
        return $this->expenseService;
    }

    public function getEmployeeService() {

        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function getEmailService() {
        if (empty($this->emailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
    * This is the entry function for the request that is hitting the servers
    */
    public function execute($request) {
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $user = $userRoleManager->getUser();
        $this->expenseId = $request->getParameter(expenseId);
        $this->employeeId = $this->getUser()->getEmployeeNumber();
        $emp = $this->getEmployeeService()->getEmployee($this->employeeId);
        $empNumber = $emp['employeeId'];
        $empNo = $emp['empNumber'];
        $this->state = 'SUBMITTED';
        //details of expense reporter employee for sending an email
        $result1 = $this->getExpenseService()->getEmpNameForEmail($this->expenseId);
        $expenseRequesterName = $result1[0]["emp_firstname"].' '.$result1[0]["emp_lastname"];
        $expenseRequesterWorkMail = $result1[0]["emp_work_email"];

        if($this->expenseId !=NULL) {
            $this->form = new ApplyExpenseForm(
                array(), 
                array('expenseId' => $this->expenseId)
            );

            /**
            * Check if the logged in user has access to apply expense report
            */
            $employeeService = new EmployeeService();
            $employee = $employeeService->getEmployee($user->emp_number);

            if ($employee->work_station != 3 && $employee->work_station != 8 && $employee->work_station != 2 && $employee->work_station != 4 && $employee->work_station != 5 && $employee->work_station != 9 && $employee->work_station != 18 && $employee->work_station != 34 && $employee->work_station != 36 &&  $user->id!= 1 ) {
                $message = __('You are not eligible for viewing this report');
                $this->context->getUser()->setFlash('error.nofade', $message, false);
                $this->context->getController()->forward('core', 'displayMessage');
                throw new sfStopException();
            }
            $existingExpenseItems =  $this->form->getExpenseItem($this->expenseId);
            //To get status:
            $temp= $this->getExpenseService()->getStatus1($this->expenseId);
            $this->submitStatus = $temp[0]['state'];

            /*getting expense item for editing*/
            $this->expenseItemsValuesArray = $existingExpenseItems[0];
            $this->totalrows = count($this->expenseItemsValuesArray);
        } else {
            $this->form = new ApplyExpenseForm();
              /**
              * Check if the logged in user has access to apply expense report
              */
              $employeeService = new EmployeeService();
              $employee = $employeeService->getEmployee($user->emp_number);
                           //var_dump($user->emp_number); exit;
              if ($employee->work_station != 3 && $employee->work_station != 8 && $employee->work_station != 2 && $employee->work_station != 4 && $employee->work_station != 5 && $employee->work_station != 9 && $employee->work_station != 18 && $employee->work_station != 34 && $employee->work_station != 36 &&  $user->id!= 1 ) {
                  $message = __('You are not eligible for viewing this report');
                  $this->context->getUser()->setFlash('error.nofade', $message, false);
                  $this->context->getController()->forward('core', 'displayMessage');
                  throw new sfStopException();
              }
        }
        $this-> setTemplate('applyExpense');
        $this->messageData = array($request->getParameter('messageData[0]'),$request->getParameter('messageData[1]'));
        if ($request->isMethod('post') ) {
            /*getting expenseId for editting*/
            $expenseId= $request->getParameter('expenseId');
            $customerName = $request->getParameter('customerName');
            $projectName = $request->getParameter('projectName');
            $tripName = $request->getParameter('tripName');
            if ($request->getParameter('btnSave') || $request->getParameter('btnSaveOnly')) {
                if( $this->form->getCSRFtoken() == $request->getParameter('_csrf_token')) {
                    $backAction = $this->backAction;
                    if ($expenseId == null) {
                        $currentDate = date('Y-m-d H:i:s');
                        if ($request->getParameter('btnSaveOnly')) {
                            $this->state = "NOT SUBMITTED";
                            $this->messageData = array('success', __('Successfully Saved'));
                        } else{
                            $this->state = "SUBMITTED";
                            $currentDate = date('Y-m-d H:i:s'); 
                            $this->messageData = array('success', __('Successfully Submitted'));
                        }
                        $inputExpenseItems = $request->getParameter('initialRows'); 
                        $files = $request->getFiles('initialRows');
                        $expense = $this->getExpenseService()->saveExpense($this->employeeId, $this->state,  $customerName  ,$projectName , $currentDate, $tripName);

                        $this->expenseId = $expense['expenseId'];
                        $id = $this->expenseId;
                        $len = strlen($id);
                        $expId="";
                        /*generating expense number*/
                        $expId = 'EXP'.$empNumber.str_pad($id, 5, '0', STR_PAD_LEFT);
                        // Updating existing row
                        $this->getExpenseService()->updateExpenseNum($id, $expId);
                        //repeating the above since i need t extract date
                        $x =$this->getExpenseService()->saveExpenseActionLog(NULL, $this->state, $currentDate, $this->employeeId,$id);
                        $date = $x['dateTime'];

                        foreach ($inputExpenseItems as $i => $inputExpenseItem) {
                            if ($files[$i]['attachment']['name'] != null) {
                                $inputExpenseItem['noAttachment'] = 1;
                            } else {
                                $inputExpenseItem['noAttachment'] = null;
                            }
                            $expenseItem = $this->getExpenseService()->saveExpenseItems(
                                $this->expenseId,
                                $inputExpenseItem,
                                $this->employeeId,
                                $this->totalrows
                            );
                            $this->itemId = $expenseItem['expensetItemId'];
                            if($files[$i]['attachment']['name'] != null
                            ) { 
                                $fileName = $files[$i]['attachment']['name'];
                                $fileType = $files[$i]['attachment']['type'];
                                $fileSize = $files[$i]['attachment']['size'];
                                $fileTempName = $files[$i]['attachment']['tmp_name'];
                                $fileContent = file_get_contents($fileTempName);
                                /*saving attachements into table*/
                                $this->getExpenseService()->saveExpenseAttachments($this->expenseId, $fileName, $fileType, $fileSize, 
                                    $fileContent, $this->itemId);   
                            }
                        } // End of for_loop for attachments 
                    } else{ 
                        // Expense_id!= null 
                        /*updating the editted expense record*/
                        $result = $this->getExpenseService()->getExpenseItem($expenseId);
                        /*Fetching Expense Submission date*/
                        $checkDateFromExpense = $this->getExpenseService()->getSubmitedExpenseDate($expenseId);
                        $currentDate = date('Y-m-d H:i:s');
                        if ($request->getParameter('btnSaveOnly')) {
                            if ($result[1][0]['state'] == 'SUBMITTED'){
                                $this->state = 'SUBMITTED';
                                $this->messageData = array('success', __('Successfully Saved'));
                            } else {
                                $this->state = 'NOT SUBMITTED';
                                  $this->messageData = array('success', __('Successfully Saved'));
                            }
                        } else {
                            $this->state = 'SUBMITTED';
                            $this->messageData = array('success', __('Successfully Submitted'));
                        }
                        $x =$this->getExpenseService()->saveExpenseActionLog(null, $this->state, $currentDate, $this->employeeId, $expenseId);
                        $inputExpenseItems = $request->getParameter('initialRows'); 
                        $files = $request->getFiles('initialRows');
                        $this->getExpenseService()->updateExpense(
                            $this->employeeId,
                            $this->state,
                            $customerName,
                            $projectName,
                            $currentDate,
                            $expenseId,
                            $tripName
                        ); 
                        foreach ($inputExpenseItems as $i => $inputExpenseItem) {
                            $fileName = $files[$i]['attachment']['name'];
                            $fileType = $files[$i]['attachment']['type'];
                            $fileSize = $files[$i]['attachment']['size'];
                            $fileTempName = $files[$i]['attachment']['tmp_name'];
                            $fileContent = file_get_contents($fileTempName);

                            if($this->expenseItemsValuesArray[$i]['file_name'] !=null 
                                || $files[$i]['attachment']['name'] != null
                            ) {
                                $inputExpenseItem['noAttachment'] = 1;
                            } else {
                                $inputExpenseItem['noAttachment'] = '';
                            }
                            if($this->expenseItemsValuesArray[$i]['item_id']!=null) {
                                /*updating the edited expense item*/
                                $this->getExpenseService()->updateExpenseItems(
                                    $inputExpenseItem['Date'],
                                    $inputExpenseItem['expense_type'],
                                    $inputExpenseItem['message'],
                                    $inputExpenseItem['paid_by_company'],
                                    $inputExpenseItem['amount'],
                                    $inputExpenseItem['currency'],
                                    $inputExpenseItem['noAttachment'],
                                    $this->expenseItemsValuesArray[$i]['item_id']
                                );
                            } else {
                            /*saving new row if added while editting*/
                            $saveExp = $this->getExpenseService()->saveExpenseItems($expenseId, $inputExpenseItem, $this->employeeId, $this->totalrows);
                            $this->edittedRowId = $saveExp['expensetItemId'];
                        }
                        /*replacing the existing attachment*/
                        if($fileName != NULL && $this->expenseItemsValuesArray[$i]['item_id'] != NULL) { 
                           $this->getExpenseService()->deleteExpenseAttachment($this->expenseItemsValuesArray[$i]['item_id']);
                           /*saving attachements into table*/
                           $this->getExpenseService()->saveExpenseAttachments($expenseId, $fileName, $fileType, $fileSize, 
                            $fileContent,$this->expenseItemsValuesArray[$i]['item_id']);                      
                       } else if ($fileName == NULL && $inputExpenseItem['noAttachment'] == 'on') {
                       /*deleting the existing attachment while declaring no attachment while editing */
                         $this->getExpenseService()->deleteExpenseAttachment($this->expenseItemsValuesArray[$i]['item_id']);
                       } else if($fileName != NULL && $this->expenseItemsValuesArray[$i]['item_id'] == NULL) {
                       /*saving attachment when added in new row while editing*/
                             $this->getExpenseService()->saveExpenseAttachments($expenseId, $fileName, $fileType, $fileSize, 
                            $fileContent,$this->edittedRowId);
                        }
                   } 
               }//This closes else
                /**
                *Trigger Email Upon Submission
                */
                if($this->state == 'SUBMITTED'){
                    $result1 = $this->getExpenseService()->getEmpNameForEmail($this->expenseId);
                    $expenseRequesterName = $result1[0]["emp_firstname"].' '.$result1[0]["emp_lastname"];
                    $expenseRequesterWorkMail = $result1[0]["emp_work_email"];
                    //$comment= $request->getParameter('Comment');
                    $state = $this->state;  
                    /**
                    *This is the details of expense report. It will be a comman body for for the employee and the manager
                    */
                  $expenseDetail = $this->getExpenseService()->getExpenseItem($this->expenseId);

                  $expRepExpenseNo = $expenseDetail[1][0]['expenseNumber'];
                  $expRepExpenseId = $expenseDetail[1][0]['empNumber'];
                  $expRepEmpName = $expenseDetail[1][0]['emp_firstname'].' '.$expenseDetail[1][0]['emp_lastname'];
                  $expRepClientName = $expenseDetail[1][0]['clientName'];
                  $expRepProjectname = $expenseDetail[1][0]['name'];
                  $expRepSubmitedDate = $expenseDetail[1][0]['date'];
                  $expenseRequesterId = $expenseDetail[1][0]['empId'];
                  //$expenseDueINR = $expenseDetail[2][0]['amount'];
                  //$expenseDueUSD = $expenseDetail[2][1]['amount'];
                  
                  /*To fetch direct and indirect managers of the expense reporte*/
                  $supervisorList = $this->getEmployeeService()->getImmediateSupervisors($expenseRequesterId);
                  $supervisorDetails = [];
                  foreach ($supervisorList as $key => $singleSupervisor) {
                    $supervisorDetails[$key]['emailId'] 
                      = $singleSupervisor->supervisor->emp_work_email;
                    $fullName = $singleSupervisor->supervisor->firstName.' '.$singleSupervisor->supervisor->lastName;
                    $supervisorDetails[$key]['fullName'] = $fullName;
                  }
                  //$comment=$x['comment'];
                  $state= $x['state'];
                  $date = $x['dateTime'];
                  //$expenseUrl = 'expense/viewExpenseDetail?expenseId='.$request->getParameter('expenseId');

                  $emailData =  array(
                    'status'=> $state,
                    'headName'=>$performedByName,
                    //'comment'=> $comment,
                    'expenseId' => $expRepExpenseNo,
                    'empId' => $expRepExpenseId,
                    'empName' => $expRepEmpName,
                    'clientName' => $expRepClientName,
                    'projectName' => $expRepProjectname,
                    'submitDate' => $expRepSubmitedDate,
                    'expenseUrlManager' => url_for('expense/viewExpenseReport'),
                    'expenseUrlReportee' => url_for('expense/viewMyExpenseReport')
                  );
                  $empExpenseDue = [];
                  foreach ($expenseDetail[2] as $key => $singleExpense) {
                      $empExpenseDue[$key]['currency'] = $singleExpense['currency'];
                      if($singleExpense['currency'] == 'USD')
                        {
                           $singleUSDExpense = $singleExpense['amount'];
                        }
                        else {
                            $singleINRExpense = $singleExpense['amount'];
                        }
                  }
                  if($singleINRExpense == ''){
                    $singleINRExpense = 'NIL';
                  }
                  if($singleUSDExpense == ''){
                    $singleUSDExpense = 'NIL';
                  }
                  /*Pushing INR and USD Expense into email data*/
                  $emailData['expenseDueINR'] = $singleINRExpense;
                  $emailData['expenseDueUSD'] = $singleUSDExpense;
                  
                  /**
                  * This email is triggered to the Expense Requester
                  */
                  $this->getEmailService()->sendEmailUsingTemplate('submittedByExpenseMail', $emailData , $expenseRequesterWorkMail);

          /**
          * This email is triggered to Manager (Direct & Indirect) & 3rd Level User
          */
          foreach ($supervisorDetails as $key => $singleSupervisor) {
            $emailData['recipientFirstName'] = $singleSupervisor['fullName'];
            $this->getEmailService()->sendEmailUsingTemplate('submittedToExpenseMail', $emailData , $singleSupervisor['emailId']);
          }      
                }
            $this->redirect('expense/applyExpense?'.http_build_query(array('messageData' => $this->messageData)));
           } // CSRF Check 
       } // save button check
       if ($request->getParameter('btnRemoveRows')) {
        $this->messageData = array('success', __('Successfully Removed'));
    }
}

} // Execute Check
} // End of Class
?>