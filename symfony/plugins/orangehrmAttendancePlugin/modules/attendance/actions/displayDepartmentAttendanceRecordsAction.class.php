<?php
class displayDepartmentAttendanceRecordsAction extends baseTimeAction {

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
            $this->timesheetService = new TimesheetService();
        }
        return $this->timesheetService;
    }

    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {

            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
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

       /**
    * This is the entry function for the request that is hitting the server
    *
    */
    public function execute($request) {
    
        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets');
        $this->form = new displayDepartmentAttendanceRecordsForm();
       
          // Chekcing for the post values. If no post values are there the template will be executed.

        if ($request->isMethod('post')) {
            // Getting the values from the post request.
            $selectedMonth = $request->getParameter('month');
            $selectedYear = $request->getParameter('year');
            $selectedDepartment = $request->getParameter('sub_unit');
            $isPaging = $request->getParameter('pageNo');
            //$weekDays= str_replace( array('[',']','"'),'',$request->getParameter('weekdays'));
            $weekdays_arr= explode("," ,$weekDays);
            $download=$request->getParameter('download');
            /*$selectedDepartment = 3;
            $selectedMonth = 3;
            $selectedYear = 2017;*/
            if($isPaging > 0 ){
                $pageNumber=$isPaging;
            }else{
                $pageNumber=1;
            }
              
            $limit =20;
            $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;
                
            if( (isset($selectedDepartment)) && 
                ($selectedMonth != null || $selectedMonth != "") && ($selectedYear != null || $selectedYear != "")
            ) {

                $this->form->setSubUnit($selectedDepartment);
                $this->form->setMonth($selectedMonth);
                $this->form->setYear($selectedYear);
                $allData=array();
             
                $demoObject=array();
                $selectedOption = '';

                if ($selectedDepartment > 0) {
                    $selectedOption = "selected_department_search";

                   $result = $this->getAttendanceService()->getDepartmentAttendanceRecords($selectedDepartment, $selectedMonth+1, $selectedYear,$limit,$offset);
                     //$employeeRecord = $result[0];
                    //$workStation = $employeeRecord[0]['work_station'];
                    //$this->form->setSubUnit($workStation);
                } else {

                    $selectedOption = "department_search";
                    $result = $this->getAttendanceService()->getAllDepartmentAttendanceRecords($selectedMonth+1, $selectedYear,$limit,$offset);
                }
                

                $count = count($result[1]);
                $selectedEmployeeId = '';
                $index = $offset+1;

                /*foreach ($result[0] as $key => $timeSheetOfMonth) {
                    $selectedEmployeeId = $timeSheetOfMonth['work_station'];
                    $departmentStatusObject =new DepartmentAttendance();
                    $departmentStatusObject->loadData($timeSheetOfMonth,$index++);
                    array_push($demoObject,$departmentStatusObject);
                }*/
                
                if(isset($download) && $download!=null  && $download=='download' ){
                    $excelSheetArray = [];
                    $sheetIndex = 1;

                    foreach ($result[1] as $key => $timeSheetOfMonth) {
                        $departmentStatusObject =new DepartmentAttendance();
                        $departmentStatusObject->loadData($timeSheetOfMonth,$sheetIndex++);
                        array_push($excelSheetArray,$departmentStatusObject);
                    }
                     
                }

                $this->downloadReports($excelSheetArray,$selectedMonth,$selectedYear,$selectedDepartment);
                } else {

                        $url = url_for('attendance/displayDepartmentAttendanceRecords');
                        echo '<script>alert("No records to download");window.location = "'.$url.'";</script>';
                        exit;
                    }

                $this->_setListComponent($demoObject,$limit,$pageNumber,$count);

                $this->pendingApprovelTimesheets=$allData;
             // 91 if end
        } // 58 post check if
    } // End of function

    private function _setListComponent($systemUserList, $limit, $pageNumber, $recordCount) {

        $configurationFactory = $this->getDepartmentAttendanceRecordsHeaderFactory();

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

     protected function getDepartmentAttendanceRecordsHeaderFactory() {
        return new DepartmentAttendanceRecordsHeaderFactory();
    }

     /**
    * Function to read the data and pass it into an excel sheet
    */
    private function downloadReports($download,$month,$year,$selectedDepartment) 
    { 

       $list = [];
       $dateInSlashFormat = [];
       $month = $month+1;

       $daysInSelectedMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year); 
    

        for($d = 1; $d <= $daysInSelectedMonth; $d++) {
            
            $time = mktime(12, 0, 0, $month, $d, $year);
           
            if (date('m', $time)==$month) 
                $list[]= date('d-M-y', $time);       
                $dateInSlashFormat[] = date('d/m/Y', $time);
        }
        echo "<pre>";

        $headerArray =array("Emp ID","Employee Name","Pay Days","Present Days");
        $header = array_merge($headerArray,$list);
        
        $queryResultArray = [];

        $result = [];
        $count = 0;

        $newResult = [];

        $employeeId = $download[0]->getempId();
        $noOfEmployees = 1;
        foreach ($download as $key => $row) {
            if ($employeeId == $row->getempId()) {

                $monthlyArray['employee_id'] = $employeeId;
                $monthlyArray['employee_name'] = $row->getFullName();
                $monthlyArray['status'] = $row->getStatus();
                $monthlyArray['login_date'] = $row->getLoginDate();
                $newResult[$noOfEmployees][] = $monthlyArray;

            } else {
                $noOfEmployees++;
                $monthlyArray['employee_id'] = $employeeId;
                $monthlyArray['employee_name'] = $row->getFullName();
                $monthlyArray['status'] = $row->getStatus();
                $monthlyArray['login_date'] = $row->getLoginDate();
                $newResult[$noOfEmployees][] = $monthlyArray;
                $employeeId = $row->getempId();
            }
        }
        

        if ($selectedDepartmentId == "0") {
            $selectedDepartment = "All Departments";
        }
        
        $availableRows = count($queryResultArray) + 2; 

        // With the required data available, we need to initiate the Excel download.
        $objPHPExcel = new PHPExcel();
        // Reading cell by cell
        $objPHPExcel->setActiveSheetIndex(0);

        // Populating the company details header.
        $objPHPExcel
            ->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 1, "Sun Technology Integrators Pvt Ltd");
        $objPHPExcel
            ->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 2, "#510, AEYKAS, 1ST STAGE, 4TH BLOCK HBR LAYOUT, BANGALORE-560043");

        // Populating 4th row headers in the excel sheet
        foreach ($header as $key => $temp) {
            $objPHPExcel
                ->getActiveSheet()
                ->setCellValueByColumnAndRow($key, 4, $temp);
        }

        // Index will be intiated from 1
        foreach ($newResult as $noOfEmployee => $employeeMonthlyRecord) {

            $objPHPExcel
                ->getActiveSheet()
                ->setCellValueByColumnAndRow(0,4+$noOfEmployee,$employeeMonthlyRecord[0]['employee_id']);
            // TO populate the employee name.
            $objPHPExcel
                ->getActiveSheet()
                ->setCellValueByColumnAndRow(1,4+$noOfEmployee,$employeeMonthlyRecord[0]['employee_name']);
            // For Paydays the days in the selected month.
            $objPHPExcel
                ->getActiveSheet()
                ->setCellValueByColumnAndRow(2,4+$noOfEmployee,$daysInSelectedMonth);

            //$monthDays = 4;
            $noOfPresentDays = 0;
            foreach ($employeeMonthlyRecord as $daysInMonth => $dailyRecord) {
                foreach ($dateInSlashFormat as $index => $date) {
                   
                   if ($date == $dailyRecord['login_date']) {
                        $objPHPExcel
                            ->getActiveSheet()
                            ->setCellValueByColumnAndRow($index+4,4+$noOfEmployee,$dailyRecord['status']);
                        
                        if (strcmp($dailyRecord['status'], 'A ') == 1) {
                            $noOfPresentDays++;
                            
                        }//277
                        break;
                   }  // if date is present in the employee monthly array.

                }//270
            }//foreach for employee's monthly record array

            $objPHPExcel
                ->getActiveSheet()
                ->setCellValueByColumnAndRow(3,4+$noOfEmployee,$noOfPresentDays);
            $noOfPresentDays = 0;
        }//foreach for no of employees in the selected department.
        
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="AttendanceReport.xlsx"');
        header('Cache-Control: max-age=0');

        // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file        
        // Write the Excel file to filename some_excel_file.xlsx in the current directory                
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();

       //Autosize the columns in excel sheet
        foreach(range('B','G') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
            ->setAutoSize(true);
        }
        $objWriter->save('php://output');
        exit();
    }
}

?> 