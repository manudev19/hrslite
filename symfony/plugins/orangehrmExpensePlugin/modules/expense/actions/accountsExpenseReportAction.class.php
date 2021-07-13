<?php
date_default_timezone_set('Asia/Calcutta'); 
class accountsExpenseReportAction extends baseTimeAction {

    const NUM_PENDING_TIMESHEETS = 100;
    private $employeeNumber;
    private $timesheetService;
    private $employeeService;
    private $reportingService;
    private $systemUserService;

    public function getSystemUserService() {
        $this->systemUserService = new SystemUserService();
        return $this->systemUserService;
    }
    public function getTimesheetService() {
        if (is_null($this->timesheetService)) {
            $this->timesheetService = new ExpenseService();
        }
        return $this->timesheetService;
    }

    public function getEmployeeService() {

        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function getEmployeesBySubUnitList($sub_unit) {
        $includeTerminatedEmployees = false;
        return $this->getEmployeeService()->getEmployeesBySubUnit($sub_unit, $includeTerminatedEmployees);
    }

    public function getImmediateSupervisors($empnumber) {
        return $this->getEmployeeService()->getSupervisorListForEmployee($empnumber);
    }

    public function getExpenseService() {
        if (is_null($this->expenseService)) {
            $this->expenseService = new ExpenseService();
        }
        return $this->expenseService;
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
    /**
    * This is the entry function for the request that is hitting the server
    *
    */
    public function execute($request) {
        $roleIds = $this->getLoggedInUserRoleIds();
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $user = $userRoleManager->getUser();
        $supervisorId = $user->getEmpNumber();
        // $this->supervisor = $this->getEmployeeService()->isSupervisor($supervisorId);
        $isSup = $this->supervisor;
        $subList = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($supervisorId);
 
        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets');

        $this->form = new accountsExpenseReportForm();
        
          // Chekcing for the post values. If no post values are there the template will be executed.

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($user->emp_number);
        if ($employee->work_station != 8 && $user->id!= 1 ) {
            $message = __('You are not eligible for viewing this report');
            $this->context->getUser()->setFlash('error.nofade', $message, false);
            $this->context->getController()->forward('core', 'displayMessage');
            throw new sfStopException();
        }

        if ($request->isMethod('post')) {
            // Getting the values from the post request.
            $selectedStatus = $request->getParameter('status');
            $state = $request->getParameter('state');
            $selectedDepartment = $request->getParameter('sub_unit');
            $selectedProject = $request->getParameter('projectName');
            $excelSheetNameDepartmentId = $selectedDepartment;
            $employeeDetails = $request->getParameter('employeeName');
            $selectedEmployeeName = $employeeDetails['empName'];
            if( $selectedEmployeeName!='Type for hints...' ){
  
                if(empty($employeeDetails['empId'])){
                    if($selectedEmployeeName!=''){
                    $this->getUser()->setFlash('warning', __('Please Enter Valid Employee Name/Id'));
                    $this->redirect('expense/accountsExpenseReport');
                }
                }else{
                    $employeeId = $employeeDetails['empId'];
                    }
            }
            $employeeNo = $request->getParameter('employeeNo');
            $empNo = $user['emp_number'];
            $empHeadLocation = $this->getExpenseService()->empHeadLocation($empNo);
            $adminLocation = '';
            foreach ($empHeadLocation as $key => $value) {
                $adminLocation .= $value['location'].',';
             }
             $adminLocations = rtrim($adminLocation,',');
             
            $fromDate = $request->getParameter('from_date');
            $toDate = $request->getParameter('to_date');
            // @todo don't delete this validation part
            /*$this->form->setValidators(array(
                'month'    => new sfValidatorString(),
                'year'   => new sfValidatorString(),
                'status' => new sfValidatorString(),
                'sub_unit' => new sfValidatorString()
            ));
            if ($this->form->isValid()) {
                
            } */

            $isPaging = $request->getParameter('pageNo');
            $weekDays= str_replace( array('[',']','"'),'',$request->getParameter('weekdays'));
            $weekdays_arr= explode("," ,$weekDays);
            $download=$request->getParameter('download');
            if($isPaging > 0 ){
                $pageNumber=$isPaging;
            }else{
                $pageNumber=1;
            }
            
            $limit =50;
            $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;
            
            
            if ( 
                ($fromDate != null || $fromDate != "") 
                && ($toDate != null || $toDate != "")
            ) {

                $this->form->setSubUnit($selectedDepartment);
                $this->form->setStatus($selectedStatus);
                $this->form->setFromDate($fromDate);
                $this->form->setToDate($toDate);
                $this->form->setSelectedEmployee($employeeDetails);

                $this->form->setSelectedProject($selectedProject);
                $allData=array();
                $demoObject=array();

                if($selectedStatus == 1){
                    // var_dump($selectedDepartment); exit;
                    $result = $this->getTimesheetService()
                    ->managerApprovedExpenseForFinance1(
                        $employeeId, $fromDate, $toDate, $selectedProject,
                        $selectedStatus,$subList,$isSup,$adminLocations, $selectedDepartment,$limit,$offset
                );
                }else{
                    $result = $this->getTimesheetService()
                    ->managerApprovedExpenseForFinance(
                        $employeeId, $fromDate, $toDate, $selectedProject,
                        $selectedStatus,$subList,$isSup,$adminLocations, $selectedDepartment,$limit,$offset
                );

                }
                /*To display all reports when all radio button is checked*/
                if($selectedStatus == 4){
                   $result = $this->getTimesheetService()
                    ->viewAllForFinanceTeam(
                        $employeeId, $fromDate, $toDate, $selectedProject,
                        $selectedStatus,$subList,$isSup,$adminLocations, $selectedDepartment,$limit,$offset
                ); 
                }

                // $result = $this->getTimesheetService()
                //     ->managerApprovedExpenseForFinance(
                //         $employeeId, $fromDate, $toDate, $selectedProject,
                //         $selectedStatus,$subList,$isSup
                // );
                // var_dump($result);exit;
                $count = count($result[1]);
                $selectedEmployeeId = '';
                // Preparing data-set(array) for the table to get populated.
                $index = $offset+1;
                foreach ($result[0] as $key => $expenseOfMonth) {
                    $departmentStatusObject =new ExpenseReport();
                    $departmentStatusObject->loadData($expenseOfMonth,$index++);
                    array_push($demoObject,$departmentStatusObject);
                }

                 // Preparing data set for the Excel sheet.
                if(isset($download) && $download!=null  && $download=='download' ){
                    if (isset($result[1]) && !empty($result[1])) {
                         $this->downloadReports($result[1]);
                    } else {
                        $url = url_for('expense/accountsExpenseReport');
                        echo '<script>alert("No records to download");window.location = "'.$url.'";</script>';
                        exit;
                    }
                }

                $this->_setListComponent($demoObject,$limit,$pageNumber,$count);

                $this->pendingApprovelTimesheets=$allData;
            } 
        }
        // this section sends the mail to the selected employee.
     /* $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee(465);
        $employeeName = $employee->firstName;
        $empWorkEmail = $employee->emp_work_email;
        $endDate = date('Y-m-d',strtotime($startDate.' + 6 days'));
        $emailData =  array('recipientFirstName' => $employeeName, 'StartDate' => $startDate, 'status'=> $state,
                                        'headName'=>$headName, 'enddate'=>$endDate, 'comment'=> $comment);
                                        $this->getEmailService()->sendEmailUsingTemplate('rejectedTimesheet' , $emailData , $empWorkEmail); */
        // 53 post check if
    } // End of function

    private function _setListComponent($systemUserList, $limit, $pageNumber, $recordCount) {

        $configurationFactory = $this->getAccountsExpenseReportHeaderFactory();

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

    
    protected function getAccountsExpenseReportHeaderFactory() {
        return new AccountsExpenseReportHeaderFactory();
    }


    /**
    * Function to read the data and pass it into an excel sheet
    */
    private function downloadReports($downloadData) 
    { 
        /**
        * Required arrays
        * 1) An array that contains the Header values. (Since the header cannot be 
        * retrieved from the result of a query)
        * 2) An array of arrays. 
        *       Ex : $result[
        *               'row1'=>['John', 'john@mail.com'], 
        *               'row2' => ['Max', 'max@mail.com']
        *           ]
        */

        foreach ($downloadData as $key => $value) {
            $downloadData[$key]['full_name'] = $value['emp_firstname'].' '.$value['emp_lastname'];
        }
        $selectedDepartment = 'Employee Expense Report';
        // Creating the first array
        $headerArray =array("Sl No", "Employee ID",  "Employee Name", "Expense ID", "Department Name", "Project Name", "Status", "Submitted On", "Amount");
         $queryResultArray = [];
        array_push($queryResultArray,$headerArray);
        array_push($queryResultArray,$headerArray);

        // With the required data available, we need to initiate the Excel download.
        $objPHPExcel = new PHPExcel();
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','1','Sun Technologies, Inc.');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','2','Employee Expense Report');
        $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
	    $objPHPExcel->getActiveSheet()->mergeCells('A2:I2'); 
        // Reading cell by cell
        $objPHPExcel->setActiveSheetIndex(0);


        $columnName = [  'employee_id','full_name', 'expenseNumber',  'deptName', 'name', 'state', 'date', 'amount'];
          
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$selectedDepartment.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file        
        // Write the Excel file to filename some_excel_file.xlsx in the current directory                
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();

       //Autosize the columns in excel sheet
        foreach (range('C', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)
            ->setAutoSize(true);
        } 

        $objPHPExcel->getActiveSheet()->getStyle("A3:I3")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Employee Expense Report');
        
        
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

        $objPHPExcel->getActiveSheet()
         ->getStyle('B')
         ->getAlignment()
             ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
             
         $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')
        ->getBorders()->getAllBorders()
        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->getActiveSheet()->getStyle('A3:' . 
            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
            $objPHPExcel->getActiveSheet()->getHighestRow())
        ->getBorders()->getAllBorders()->
        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //Fill color in the header cells
        $objPHPExcel->getActiveSheet()->getCell('A3');
        $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
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
        $objPHPExcel->getActiveSheet()->getCell('I3');
        $objPHPExcel->getActiveSheet()->getStyle('I3')->applyFromArray($styleArray);
        // $objPHPExcel->getActiveSheet(0)->mergeCells('C1:D1');        

        for ($i=0; $i < count($headerArray); $i++) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,'3',$headerArray[$i]);
        }
        $totalAmount = 0;
        for ($i=0; $i < count($downloadData); $i++) { 
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, '4'+$i, $i+1); 
            $totalAmount = $downloadData[$i]['amount'] + $totalAmount;
        }
        for ($i=0; $i <= count($downloadData) + 1; $i++) { 
            for ($j=0; $j <= count($downloadData[$i]); $j++) { 
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('1'+$j, '4'+$i, $downloadData[$i][$columnName[$j]]); 
            }
        }

        $availableRows = count($downloadData) + 3;
        $toPrintDateRow = count($downloadData) + 5;
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
        $objPHPExcel->getActiveSheet()->getCell('H'.($availableRows+1))->setValue('Total');
        $objPHPExcel->getActiveSheet()->getCell('I'.($availableRows + 1))->setValue($totalAmount);
        date_default_timezone_set("Asia/Kolkata");
        $objPHPExcel->getActiveSheet()->getCell('B'.$toPrintDateRow)->setValue('Generated on ' .date('Y-m-d h:i:s a'). ' IST');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$toPrintDateRow)->getFont()->setBold(true);
         $objPHPExcel->getActiveSheet()->getStyle('H'.($availableRows+1))->getFont()->setBold(true);
         $objPHPExcel->getActiveSheet()->getStyle('I'.($availableRows+1))->getFont()->setBold(true);
        $objWriter->save('php://output');
        exit();
    }    
}

?> 
