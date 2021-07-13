<?php
date_default_timezone_set('Asia/Calcutta'); 
/**
* OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
* all the essential functionalities required for any enterprise.
* Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
*
* OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
* the GNU General Public License as published by the Free Software Foundation; either
* version 2 of the License, or (at your option) any later version.
*
* OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with this program;
* if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
* Boston, MA  02110-1301, USA
*/
class viewExpenseDetailAction extends baseTimeAction {

  private $expenseService;
  private $timesheetPeriodService;
  // private $timesheetActionLog;
  private $employeeService;
  // private $expenseId;
  public $empName;

  public function getEmployeeService() {

    if (is_null($this->employeeService)) {
      $this->employeeService = new EmployeeService();
    }

    return $this->employeeService;
  }

  public function setEmployeeService($employeeService) {

    if ($employeeService instanceof EmployeeService) {
      $this->employeeService = $employeeService;
    }
  }
  public function getExpenseService() {

    if (is_null($this->expenseService)) {
      $this->expenseService = new ExpenseService();
    }

    return $this->expenseService;
  }
  public function getSystemUserService() {
    $this->systemUserService = new SystemUserService();
    return $this->systemUserService;
  }
  
  public function getLoggedInUserRoleIds() {
    $userId = $this->getUser()->getAttribute('auth.userId');
    $systemUser = $this->getSystemUserService()->getSystemUser($userId);
    $allowedUserRoles = $this->getUserRoles($systemUser);
    $roleIds = array();
    foreach ($allowedUserRoles as $allowedUserRole) {
      $roleIds[] = strval($allowedUserRole->getId());
    }
    return $roleIds;
  }

  public function getUserRoles($user) {

    $roles = array($user->getUserRole());

    // Check for supervisor:
    $empNumber = $user->getEmpNumber();
    if (!empty($empNumber)) {

      if ($user->getUserRole()->getName() != 'ESS') {
        $roles[] = $this->getSystemUserService()->getUserRole('ESS');
      }

      if ($this->getUser()->getAttribute('auth.isProjectAdmin')) {
        $roles[] = $this->getSystemUserService()->getUserRole('ProjectAdmin');
      }

      if ($this->getUser()->getAttribute('auth.isHiringManager')) {
        $roles[] = $this->getSystemUserService()->getUserRole('HiringManager');
      }

      if ($this->getUser()->getAttribute('auth.isInterviewer')) {
        $roles[] = $this->getSystemUserService()->getUserRole('Interviewer');
      }

      if ($this->getUser()->getAttribute('auth.isSupervisor')) {
        $roles[] = $this->getSystemUserService()->getUserRole('Supervisor');
      }
    }

    return $roles;
  }
  public function getEmailService() {
    if (empty($this->emailService)) {
      $this->emailService = new EmailService();
    }
    return $this->emailService;
  }

  public function execute($request) {
    /* For highlighting corresponding menu item */
    $request->setParameter('initialActionName', 'viewMyExpenseReport');
    $employeeId = $request->getParameter('employeeId');
    $roleIds = $this->getLoggedInUserRoleIds();
    $userRoleManager = $this->getContext()->getUserRoleManager();
    $user = $userRoleManager->getUser();
    $this->roleId = $roleIds[0];
    $this->expenseId= $request->getParameter("expenseId");
    //details of expense reporter for sending an email
    $result1 = $this->getExpenseService()->getEmpNameForEmail($this->expenseId);
    $expenseRequesterName = $result1[0]["emp_firstname"].' '.$result1[0]["emp_lastname"];
    $expenseRequesterWorkMail = $result1[0]["emp_work_email"];
    $expenseRequesterLocationId = $result1[0]['location_id'];
    /*Get the List of cost center admin based on expense reporter location*/
    $costCenterAdminList = $this->getExpenseService()->getCCAdminList($expenseRequesterLocationId);

    $this->messageData = array($request->getParameter('messageData[0]'),$request->getParameter('messageData[1]'));
    $this->loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();
    $result = $this->getExpenseService()->getExpenseItem($this->expenseId);
    $this->supervisor = $this->getEmployeeService()->isSupervisor($this->loggedInEmpNumber);
    $this->actionDetails = $this->getExpenseService()->getActionDetails($this->expenseId);
    
    $this->approvalForDownload = $this->getExpenseService()->getActionDetailsFordownload($this->expenseId);

    $this->data = array(
    'empnum' => $result[1][0]['empNumber'],
    'name' => $result[1][0]['emp_firstname'] ." ". $result[1][0]['emp_lastname'],
    'date' => $result[1][0]['date'],
    'clientName' =>  $result[1][0]['clientName'],
    'projectName' => $result[1][0]['name'],
    'state' =>  $result[1][0]['state'],
    'dueamount' => $result[2],
    'totalamount'=>$result[3],
    'expenseNumber'=>$result[1][0]['expenseNumber'],
    'expenseId' => $result[1][0]['expenseId'],
    'empId' => $result[1][0]['empId']
      );
    $index = 1;
    $demoObject = array();
    foreach ($result[0] as $key => $expenseOfMonth) {
      $departmentStatusObject =new ExpenseDetail();
      $departmentStatusObject->loadData($expenseOfMonth,$index++);
      array_push($demoObject,$departmentStatusObject);
    }
      /*executed when approved by the supervisior*/
    if($request->isMethod('post')){
      if($request->getParameter('btnComment')) {
        $actionLogState = "COMMENTED";
        $expenseRequesterId = $expenseDetail[1][0]['empNumber'];
        $comment= $request->getParameter('Comment');
        $currentDate = date('Y-m-d H:i:s');
        $x = $this->getExpenseService()->saveExpenseActionLog($comment, $actionLogState, $currentDate, $this->loggedInEmpNumber,$this->expenseId);
        /**
        *This is the details of expense report. It will be a comman body for for the employee and the manager
        */
        $expenseDetail=$this->getExpenseService()->getExpenseItem($this->expenseId);
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
        $supervisorList= $this->getEmployeeService()->getImmediateSupervisors($expenseRequesterId);
        $supervisorDetails = [];
        foreach ($supervisorList as $key => $singleSupervisor) {
          $supervisorDetails[$key]['emailId'] 
              = $singleSupervisor->supervisor->emp_work_email;
          $fullName = $singleSupervisor->supervisor->firstName.' '.$singleSupervisor->supervisor->lastName;
          $supervisorDetails[$key]['fullName'] = $fullName;
        }

        /*Performed by The manager who logs in and approves the report*/
        $performedBy =  $this->getEmployeeService()->getEmployee($x['performedBy']);
          $performedByName= $performedBy['firstName'].' '.$performedBy['lastName'];

        $comment=$x['comment'];
        $state= $x['state'];
        $date = $x['dateTime'];
        //$expenseUrl = 'expense/viewExpenseDetail?expenseId='.$request->getParameter('expenseId');
        $emailData=  array(
          'status'=> $state,
          'headName'=>$performedByName,
          'comment'=> $comment,
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
        $this->getEmailService()->sendEmailUsingTemplate('commentedExpenseReport', $emailData , $expenseRequesterWorkMail);

        /** 
        * This email is triggered to Manager (Direct & Indirect)
        */
        foreach ($supervisorDetails as $key => $singleSupervisor) {
          $emailData['recipientFirstName'] = $singleSupervisor['fullName'];
          $this->getEmailService()->sendEmailUsingTemplate('supCommentedExpenseReport', $emailData , $singleSupervisor['emailId']);
        }         

        $this->getUser()->setFlash('success', 'Successfully Commented');
        $this->redirect($request->getReferer());
      }
        if($request->getParameter('btnApprove')){
          $state = "APPROVED";
          $comment= $request->getParameter('Comment');
          $currentDate = date('Y-m-d H:i:s');
          //Saving in action log of expense
          $x = $this->getExpenseService()->saveExpenseActionLog($comment, $state, $currentDate, $this->loggedInEmpNumber,$this->expenseId);
          //Updating in state in expense table
          $this->getExpenseService()->updateExpenseState($state,$this->expenseId); 
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
          /**
          *To fetch direct and indirect managers of the expense reporte
          */
          $supervisorList = $this->getEmployeeService()->getImmediateSupervisors($expenseRequesterId);
          $supervisorDetails = [];
          foreach ($supervisorList as $key => $singleSupervisor) {
            $supervisorDetails[$key]['emailId'] 
              = $singleSupervisor->supervisor->emp_work_email;
            $fullName = $singleSupervisor->supervisor->firstName.' '.$singleSupervisor->supervisor->lastName;
            $supervisorDetails[$key]['fullName'] = $fullName;
          }

          /**
          *To fetch Cost Center Admin Email Address and name specific to Expense Reportee Location
          */

          $costCenterAdminDetails = [];
          foreach ($costCenterAdminList as $key => $singleAdmin) {
            $costCenterAdminDetails[$key]['emailId'] = $singleAdmin['emp_work_email'];
            $fullName = $singleAdmin['emp_firstname'].' '.$singleAdmin['emp_lastname'];
            $costCenterAdminDetails[$key]['fullName'] = $fullName;
          }
          /**
          *This array contains the list of Admins and Supervisors to whome the email must be triggered once expense is approved 
          */
          $supervisorAndAdminList = array_merge($supervisorDetails, $costCenterAdminDetails);

          /*Performed by The manager who logs in and approves the report*/
          $performedBy= $this->getEmployeeService()->getEmployee($x['performedBy']);
          $performedByName= $performedBy['firstName'].' '.$performedBy['lastName'];

          $comment=$x['comment'];
          $state= $x['state'];
          $date = $x['dateTime'];
          // $expenseUrl = 'expense/viewExpenseDetail?expenseId='.$request->getParameter('expenseId');
          // common body content for both emails
          $emailData =  array(
            'status'=> $state,
            'headName'=>$performedByName,
            'comment'=> $comment,
            'expenseId' => $expRepExpenseNo,
            'empId' => $expRepExpenseId,
            'empName' => $expRepEmpName,
            'clientName' => $expRepClientName,
            'projectName' => $expRepProjectname,
            'submitDate' => $expRepSubmitedDate,
            'expenseUrlManager' => url_for('expense/viewExpenseReport'),
            'expenseUrlAccount' => url_for('expense/accountsExpenseReport'),
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
          $this->getEmailService()->sendEmailUsingTemplate('approvedExpenseReport', $emailData , $expenseRequesterWorkMail);

          /**
          * This email is triggered to Manager (Direct & Indirect) and 3rd Level User
          */
            
         foreach ($supervisorDetails as $key => $singleSupervisor) {
            $emailData['recipientFirstName'] = $singleSupervisor['fullName'];
            $this->getEmailService()->sendEmailUsingTemplate('supapprovedExpenseReport', $emailData , $singleSupervisor['emailId']);
          }
            
          // cost center
          foreach ($costCenterAdminDetails as $key => $singleSupervisor) {
            $emailData['recipientFirstName'] = $singleSupervisor['fullName'];
            $this->getEmailService()->sendEmailUsingTemplate('approvedExpenseReportToSup', $emailData , $singleSupervisor['emailId']);
          }
            
//          foreach ($supervisorAndAdminList as $key => $singleSupervisor) {
//            $emailData['recipientFirstName'] = $singleSupervisor['fullName'];
//            $this->getEmailService()->sendEmailUsingTemplate('supapprovedExpenseReport', $emailData , $singleSupervisor['emailId']);
//          }         
          $this->messageData = array('success', __('Successfully Approved'));
          $this->redirect($request->getReferer());
        }
        if($request->getParameter('btnReject')){
          $comment= $request->getParameter('Comment');
          $state = "REJECTED";
          $currentDate = date('Y-m-d H:i:s');
          //Saving in Action Log of expense
          $x = $this->getExpenseService()->saveExpenseActionLog($comment, $state, $currentDate, $this->loggedInEmpNumber,$this->expenseId);
          //Updating the state in expense table
          $this->getExpenseService()->updateExpenseState($state,$this->expenseId);

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

          /*Performed by The manager who logs in and approves the report*/
          $performedBy= $this->getEmployeeService()->getEmployee($x['performedBy']);
          $performedByName= $performedBy['firstName'].' '.$performedBy['lastName'];

          $comment=$x['comment'];
          $state= $x['state'];
          $date = $x['dateTime'];
          //$expenseUrl = 'expense/viewExpenseDetail?expenseId='.$request->getParameter('expenseId');

          $emailData =  array(
            'status'=> $state,
            'headName'=>$performedByName,
            'comment'=> $comment,
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
          $this->getEmailService()->sendEmailUsingTemplate('approvedExpenseReport', $emailData , $expenseRequesterWorkMail);

          /**
          * This email is triggered to Manager (Direct & Indirect) & 3rd Level User
          */
          foreach ($supervisorDetails as $key => $singleSupervisor) {
            $emailData['recipientFirstName'] = $singleSupervisor['fullName'];
            $this->getEmailService()->sendEmailUsingTemplate('supapprovedExpenseReport', $emailData , $singleSupervisor['emailId']);
          }         
          $this->messageData = array('success', __('Successfully Rejected'));
          $this->redirect($request->getReferer());
        }
        /*executed when clicked on download*/
        if ($request->getParameter('btnDownload')) {
          $sheetindex = 1;
          $excelsheetArray = array();
          foreach ($result[0] as $key => $expenseOfMonth) {
            $departmentStatusObject =new ExpenseDetail();
            $departmentStatusObject->loadData($expenseOfMonth,$sheetindex++);
            array_push($excelsheetArray,$departmentStatusObject);
          }
          //calling download function to download the report
          $this->downloadExpense($excelsheetArray, $this->data);
        }

      }
      $this->_setListComponent($demoObject);
}//end of execute

private function _setListComponent($systemUserList) {

  $configurationFactory = $this->getTimesheetHeaderFactory();

  $configurationFactory->setRuntimeDefinitions(array(
    'hasSelectableRows' => false,
    'hasSummary'=>false,
    'title'=>false
  ));

  ohrmListComponent::setConfigurationFactory($configurationFactory);
  ohrmListComponent::setListData($systemUserList);
}


protected function getTimesheetHeaderFactory() {
  return new ExpenseDetailHeaderFactory();
}

/*downloading the expense report*/
public function downloadExpense($download, $other)
{
  $headerArray =array("Date of expense", "Expense type", "Description","Paid in advance","Bill Attached ", "Amount","Currency");
  $queryResultArray = [];
  array_push($queryResultArray,$headerArray);

  // Creating the second array based on the query result.
  foreach ($download as $data) {

    $tmparray =array();
    /* $getCountDetails = $data->getCountDetails();
    array_push($tmparray,$getCountDetails);*/

    $getDateOfExpense = $data->getDateOfExpense();
    array_push($tmparray,$getDateOfExpense);

    $getExpenseType = $data->getExpenseType();
    array_push($tmparray,$getExpenseType );

    $getExpenseMessage = $data->getExpenseMessage();
    array_push($tmparray,$getExpenseMessage);

    $getExpensePaidBy = $data->getExpensePaidBy();
    array_push($tmparray,$getExpensePaidBy);

    $getNoAttachment = $data->getNoAttachment();
    array_push($tmparray,$getNoAttachment);
   /* $getNoAttachment = $data->getFileName();
    array_push($tmparray,$getNoAttachment);*/

    $getExpenseAmount = $data->getExpenseAmount();
    array_push($tmparray,$getExpenseAmount);

    $getCurrency = $data->getCurrency();
    array_push($tmparray, $getCurrency);
    array_push($queryResultArray,$tmparray);

  }
/* --data displayed correctly
*/
  $topheaderArray =array("Expense ID","Employee Number","Employee name", "Submitted date","Client","Project", "Status");
  $topArray=[];
  /*foreach ($other as $key => $other) {
  array_push($topArray, $other);
}*/

$top= count($other);
$topArray[0]=$other['expenseNumber'];
$topArray[1]=$other['empnum'];
$topArray[2]=$other['name'];
$topArray[3]=$other['date'];
$topArray[4]=$other['clientName'];
$topArray[5]= $other['projectName'];
$topArray[6]= $other['state'];

$availableRows = count($queryResultArray) + 8 +count($top);
// With the required data available, we need to initiate the Excel download.
$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('2','1','Sun Technologies, Inc.');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('2','2','Expense Report');
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('1','6','Detailed Expense');
$objPHPExcel->getActiveSheet()->mergeCells('C1:H1');
$objPHPExcel->getActiveSheet()->mergeCells('C2:H2');
$objPHPExcel->getActiveSheet()->mergeCells('B6:H6');
$objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
$objPHPExcel->setActiveSheetIndex(0);
/*to populate data for the first table in download*/
for ($i=0; $i < count($topheaderArray); $i++) {
  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i+'1','3',$topheaderArray[$i]);
  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i+'1','4',$topArray[$i]);

}
// Reading cell by cell and storing data into second table
foreach($queryResultArray as $row => $columns) {
  foreach($columns as $column => $data) {
    $objPHPExcel->getActiveSheet()
    ->setCellValueByColumnAndRow($column+'1', '7'+$row , $data);
  }
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$topArray[2].'_Expense_Report'.'.xlsx"');
header('Cache-Control: max-age=0');
$gdImage = imagecreatefrompng(plugin_web_path('orangehrmExpensePlugin/images/logo_SunTech.png'));

// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
$objDrawing = new PHPExcel_Worksheet_Drawing();

$objDrawing->setName('logo');
$objDrawing->setDescription('suntechnologies_logo');
$objDrawing->setPath('E:/wamp/www'.plugin_web_path('orangehrmExpensePlugin/images').'logo_SunTech.png');
$objDrawing->setWidthAndHeight(100,70);
$objDrawing->setOffsetX(10);
$objDrawing->setOffsetY(10);
$objDrawing->setCoordinates('B1');

// $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
// $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);

$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
// Write the Excel file to filename some_excel_file.xlsx in the current directory
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();



//Autosize the columns in excel sheet
foreach (range('D', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
  $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
// $objPHPExcel->getActiveSheet()->getRowDimension(20)->set Height(20);

$objPHPExcel->getActiveSheet()->getStyle("B7:H7")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("C1")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("B6")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("B3:H3")->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle("C1")->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setSize(14);

$objPHPExcel->getActiveSheet()->setTitle('Expense Report of '.$topArray[0]);


$styleArray = array (
  'fill' => array(
    'type' => PHPExcel_Style_Fill::FILL_SOLID,
    'color' => array('rgb' => 'f28c38'),
    'size'  => 10,
    'name'  => 'Verdana'
  )
);
$style = array('alignment' => array(
  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
)
);
$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($style);
// $objPHPExcel ->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getStyle('C2')->applyFromArray($style);
// $objPHPExcel ->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getStyle('B7:H7') ->getBorders()->getAllBorders() ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B4:H4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('C1:H1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('C2:H2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B8:'.$objPHPExcel->getActiveSheet()->getHighestDataColumn($objPHPExcel->getActiveSheet()->getHighestRow()). $objPHPExcel->getActiveSheet()->getHighestRow()) ->getBorders()->getAllBorders()->
setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



//Fill color in the header cells
// $objPHPExcel->getActiveSheet()->getCell('A7');
// $objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('B7');
$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('C7');
$objPHPExcel->getActiveSheet()->getStyle('C7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('D7');
$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('E7');
$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('F7');
$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('G7');
$objPHPExcel->getActiveSheet()->getStyle('G7')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('H7');
$objPHPExcel->getActiveSheet()->getStyle('H7')->applyFromArray($styleArray);

// $objPHPExcel->getActiveSheet()->getCell('A3');
// $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('B3');
$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('C3');
$objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('D3');
$objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('E3');
$objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('F3');
$objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('G3');
$objPHPExcel->getActiveSheet()->getStyle('G3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getCell('H3');
$objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($styleArray);
// $objPHPExcel->getActiveSheet()->getCell('H3');
// $objPHPExcel->getActiveSheet()->getStyle('H3')->applyFromArray($styleArray);
//  $objPHPExcel->getActiveSheet()->getCell('I3');
// $objPHPExcel->getActiveSheet()->getStyle('I3')->applyFromArray($styleArray);


$x = $availableRows+2; $y = $availableRows+3; $z = $availableRows+4; $i = $availableRows+5; $j = $availableRows+6;
$k = $availableRows - 2; $l = $k + 1;
$objPHPExcel->getActiveSheet()->getCell('E'.$i)->setValue('Generated on ' .date('Y-m-d h:i:s a'). ' IST');
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getCell('B'.$x)->setValue('Employee Sign: ');
$objPHPExcel->getActiveSheet()->getStyle('B'.$x)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('E'.$x)->setValue('Date Paid: ');
$objPHPExcel->getActiveSheet()->getStyle('E'.$x)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('B'.$y)->setValue('Approved By: ');
$objPHPExcel->getActiveSheet()->getStyle('B'.$y)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('C'.$y)->setValue($this->approvalForDownload[0]['emp_firstname'] . ' ' . $this->approvalForDownload[0]['emp_lastname']);
$objPHPExcel->getActiveSheet()->getCell('E'.$y)->setValue('Cheque #: ');
$objPHPExcel->getActiveSheet()->getStyle('E'.$y)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('B'.$z)->setValue('Remarks: ');
$objPHPExcel->getActiveSheet()->getStyle('B'.$z)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('C'.$z)->setValue($this->approvalForDownload[0]['comment']);


for ($a=0; $a < count($other['totalamount']); $a++) {
  $objPHPExcel->getActiveSheet()->getCell('E'.$k)->setValue('Total expense ('.$other['totalamount'][$a]['currency'].') :');
  $objPHPExcel->getActiveSheet()->getStyle('E'.$k)->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getCell('G'.$k)->setValue($other['totalamount'][$a]['amount']);
  $objPHPExcel->getActiveSheet()->getStyle('G'.$k)->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->mergeCells('E'.$k.':F'.$k);
  $k++;
}

for ($a=0; $a < count($other['dueamount']); $a++) {
  $objPHPExcel->getActiveSheet()->getCell('E'.$k)->setValue('Expense Due  ('.$other['dueamount'][$a]['currency'].') :');
  $objPHPExcel->getActiveSheet()->getStyle('E'.$k)->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getCell('G'.$k)->setValue($other['dueamount'][$a]['amount']);
  $objPHPExcel->getActiveSheet()->getStyle('G'.$k)->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->mergeCells('E'.$k.':F'.$k);
  $k++;
}
/*$objPHPExcel->getActiveSheet()->getCell('E'.$k)->setValue('Total expense in '.$other['amount'][0]['currency'].':');
$objPHPExcel->getActiveSheet()->getStyle('E'.$k)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('G'.$k)->setValue($other['amount'][0]['amount']);
$objPHPExcel->getActiveSheet()->getStyle('G'.$k)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getCell('E'.$l)->setValue('Total expense in '.$other['amount'][1]['currency'].':');
$objPHPExcel->getActiveSheet()->getStyle('E'.$l)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getCell('G'.$l)->setValue($other['amount'][1]['amount']);
$objPHPExcel->getActiveSheet()->getStyle('G'.$l)->getFont()->setBold(true);*/

$objPHPExcel->getActiveSheet()->mergeCells('C'.$z.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('C'.$x.':D'.$x);
$objPHPExcel->getActiveSheet()->mergeCells('F'.$x.':H'.$x);
$objPHPExcel->getActiveSheet()->mergeCells('F'.$y.':H'.$y);
$objPHPExcel->getActiveSheet()->mergeCells('C'.$y.':D'.$y);
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('B'.$z.':B'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('E'.$z.':H'.$z);

// $objPHPExcel->getActiveSheet()->mergeCells('E'.$l.':F'.$l);
$objPHPExcel->getActiveSheet()->getCell('B'.$j)->setValue('** Please attach all receipts with this report, including non billable expenses. (Itinerary, event ticket and all travel related expenses.)');
// $objPHPExcel->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$j)->getFont()->setSize(8);
// $objPHPExcel->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('B1:H'.$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('B1:H'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('B1:H'.$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle('B1:H'.$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getRowDimension($x)->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getRowDimension($y)->setRowHeight(30);
$objWriter->save('php://output');
exit();
}

}
