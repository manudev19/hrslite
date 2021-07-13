<?php
date_default_timezone_set('Asia/Calcutta'); 
class viewExpenseReportAction extends baseTimeAction {

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

        $this->supervisor = $this->getEmployeeService()->isSupervisor($supervisorId);
        $isSup = $this->supervisor;
        $subList = $this->getEmployeeService()->getSubordinateIdListBySupervisorId($supervisorId);
 
        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets');
        $this->form = new viewExpenseReportForm();

        /**
        * Check if the logged in user has access to accounts expense report
        */
        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($user->emp_number);
      if ($employee->work_station != 3 && $employee->work_station != 8 && $employee->work_station != 2 && $employee->work_station != 4 && $employee->work_station != 5 && $employee->work_station != 9 && $employee->work_station != 18 && $employee->work_station != 34 && $employee->work_station != 36 &&  $user->id!= 1 ) {
            $message = __('You are not eligible for viewing this report');
            $this->context->getUser()->setFlash('error.nofade', $message, false);
            $this->context->getController()->forward('core', 'displayMessage');
            throw new sfStopException();
        }

        // Chekcing for the post values. If no post values are there the template will be executed.
        if ($request->isMethod('post')) {
            // Getting the values from the post request.
            $selectedStatus = $request->getParameter('status');
            $selectedDepartment = $request->getParameter('sub_unit');
            $selectedProject = $request->getParameter('projectName');
            $excelSheetNameDepartmentId = $selectedDepartment;
            $employeeDetails = $request->getParameter('employeeName');
            $selectedEmployeeName = $employeeDetails['empName'];
            if( $selectedEmployeeName!='Type for hints...' ){
  
                if(empty($employeeDetails['empId'])){
                    if($selectedEmployeeName!=''){
                    $this->getUser()->setFlash('warning', __('Please Enter Valid Employee Name/Id'));
                    $this->redirect('expense/viewExpenseReport');
                }
                }else{
                    $employeeId = $employeeDetails['empId'];
                    }
            }
            

            $fromDate = $request->getParameter('from_date');
            $toDate = $request->getParameter('to_date');
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
            
            
            if( 
                ($fromDate != null || $fromDate != "") && ($toDate != null || $toDate != "")
            ) {

                $this->form->setSubUnit($selectedDepartment);
                $this->form->setStatus($selectedStatus);
                $this->form->setFromDate($fromDate);
                $this->form->setToDate($toDate);
                $this->form->setSelectedEmployee($employeeDetails);

                $this->form->setSelectedProject($selectedProject);
                $allData=array();
                $demoObject=array();
                if($selectedStatus == 'ALL'){
                $result = $this->getTimesheetService()->viewAllForSup($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$selectedDepartment,$limit,$offset);
                }else{
                    //IF any radio button other than all is checked!
                $result = $this->getTimesheetService()->getExpense($employeeId, $fromDate, $toDate, $selectedProject,$selectedStatus,$subList,$isSup,$selectedDepartment,$limit,$offset); 
                }

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
                    if(isset($result[1]) && !empty($result[1])){
                        $this->downloadReports($result[1]);    
                    }
                    else{
                         $url = url_for('expense/viewExpenseReport');
                        echo '<script>alert("No records to download");window.location = "'.$url.'";</script>';
                         exit;
                    }
                }

                $this->_setListComponent($demoObject,$limit,$pageNumber,$count);
                $this->pendingApprovelTimesheets=$allData;
            } 
        }
        // 53 post check if
    } // End of function

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
        return new ExpenseHeaderFactory();
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

        /*Concatenate First_Name and Last_Name (i.e 2 elements of an array and push it back into the same array)*/
        foreach ($downloadData as $key => $value) {
            $downloadData[$key]['full_name'] = $value['emp_firstname'].' '.$value['emp_lastname'];
        }
        $selectedDepartment = 'Employee Expense Report';
        // Creating the first array
        $headerArray =array("Sl No", "Employee ID",  "Employee Name", "Expense ID","Department Name", "Project Name","Status", "Submitted On", "Amount");
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

        $columnName = [ 'employee_id', 'full_name', 'expenseNumber', 'deptName', 'name', 'state', 'date', 'amount'];
          
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$selectedDepartment.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file        
        // Write the Excel file to filename some_excel_file.xlsx in the current directory                
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();

       //Autosize the columns in excel sheet
        foreach (range('B', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)
            ->setAutoSize(true);
        } 

        $objPHPExcel->getActiveSheet()->getStyle("A3:H3")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle('Employee Expense Report');
        
          
        $objPHPExcel->getActiveSheet()
        ->getStyle('B')
        ->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
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
        //Heading
        for ($i=0; $i < count($headerArray); $i++) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,'3',$headerArray[$i]);
        }
        //Total Amount
        $totalAmount = 0;
        for ($i=0; $i < count($downloadData); $i++) { 
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, '4'+$i, $i+1); 
            $totalAmount = $downloadData[$i]['amount'] + $totalAmount;
        }
        //Data
        for ($i=0; $i <= count($downloadData) +1; $i++) { 
            for ($j=0; $j <= count($downloadData[$i]); $j++) { 

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('1'+$j, '4'+$i, $downloadData[$i][$columnName[$j]]); 
            }
        }

        $availableRows = count($downloadData) + 3;
        $toPrintDateRow = count($downloadData) + 5;
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
