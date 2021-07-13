<?php
class displayDepartmentLeadTimesheetSummaryReportCriteriaAction extends baseTimeAction {

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


    /**
    * This is the entry function for the request that is hitting the server
    *
    */
  	public function execute($request) {
        $sfUser = sfContext::getInstance()->getUser();
        $userId = $sfUser->getAttribute('auth.empNumber');
        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($userId);
        if ($employee->role_in_department == null) {
            $message = __('You are not eligible for viewing this report');
            $this->context->getUser()->setFlash('error.nofade', $message, false);
            $this->context->getController()->forward('core', 'displayMessage');
            throw new sfStopException();
        } else {
            $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets');
            
            $this->form = new displayDepartmentLeadTimesheetSummaryReportForm();
              // Chekcing for the post values. If no post values are there the template will be executed.
            
            if ($request->isMethod('post')) {
            	// Getting the values from the post request.
            	//$selectedMonth = $request->getParameter('month');
            	//$selectedYear = $request->getParameter('year');
            	
    			$selectedDepartment = $request->getParameter('sub_unit');
                
                $sfUser = sfContext::getInstance()->getUser();
                $userId = $sfUser->getAttribute('auth.empNumber');
                $employeeService = new EmployeeService();
                $employee = $employeeService->getEmployee($userId);
                if ($employee->role_in_department == 1) {
                    $selectedDepartment = $employee->work_station;
                }

                $excelSheetNameDepartmentId = $selectedDepartment;
                //$selectedStatus = $request->getParameter('status');
                $notSubmitted = $request->getParameter('not_submitted');
                $submitted = $request->getParameter('submitted');
                $approved = $request->getParameter('approved');
                
                $employeeDetails = $request->getParameter('employeeName');
                $selectedEmployeeName = $employeeDetails['empName'];
                $employeeId = $employeeDetails['empId'];
                //var_dump($employeeId);exit;
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
            	  
            	$limit =20;
            	$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $limit) : ($request->getParameter('pageNo', 1) - 1) * $limit;
            	
    	        	
           		if( 
                    ($fromDate != null || $fromDate != "") && ($toDate != null || $toDate != "")
            	) {
                    
            		
            	 	//$this->form->setMonth($selectedMonth);
            	 	//$this->form->setYear($selectedYear);
            	 	//$this->form->setStatus($selectedStatus);
                    $this->form->setFromDate($fromDate);
                    $this->form->setToDate($toDate);
                    $this->form->setSelectedEmployee($employeeDetails);
                    
                    $this->form->setNotSubmitted($notSubmitted);
                    $this->form->setSubmitted($submitted);
                    $this->form->setApproved($approved);
            	 	$allData=array();
            	 	$demoObject=array();
                    $selectedOption = '';
                    if (!empty($employeeId)) {
                        $result = $this->getTimesheetService()->getTimeSheetOfEmployeeForDepartmentLead($employeeId, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $limit, $offset);
                        // To set the sub-unit of the selected employee in the form.
                        $employeeRecord = $result[0];
                        $workStation = $employeeRecord[0]['work_station'];
                        $this->form->setSubUnit($workStation);
                    } else {
                        $result = $this->getTimesheetService()->getTimesheetForDeparmtmentLead($selectedDepartment, $fromDate, $toDate, $notSubmitted, $submitted, $approved, $limit, $offset);
                    }

                    $count = count($result[1]);
                    $selectedEmployeeId = '';
                    // Preparing data-set(array) for the table to get populated.
    				$index = $offset+1;
    				foreach ($result[0] as $key => $timeSheetOfMonth) {
                        $selectedEmployeeId = $timeSheetOfMonth['emp_number'];
    					$departmentStatusObject =new Department();
              			$departmentStatusObject->loadData($timeSheetOfMonth,$index++);
              			array_push($demoObject,$departmentStatusObject);
    				}

                    // Preparing data set for the Excel sheet.
        			if(isset($download) && $download!=null  && $download=='download' ){
                        if (isset($timeSheetOfMonth) && !empty($timeSheetOfMonth)) {
                            $excelSheetArray = [];
                            $sheetIndex = 1;
                            foreach ($result[1] as $key => $timeSheetOfMonth) {
                                $departmentStatusObject =new Department();
                                $departmentStatusObject->loadData($timeSheetOfMonth,$sheetIndex++);
                                array_push($excelSheetArray,$departmentStatusObject);
                            }
                            $this->downloadReports($excelSheetArray, $fromDate, $toDate, $employeeId, $selectedDepartment, $selectedEmployeeName);
                        } else {
                            $url = url_for('time/displayDepartmentLeadTimesheetSummaryReportCriteria');
                            echo '<script>alert("No records to download");window.location = "'.$url.'";</script>';
                            exit;
                        }
           			}

    				$this->_setListComponent($demoObject,$limit,$pageNumber,$count);

    	 			$this->pendingApprovelTimesheets=$allData;
    	    	} 
    		} // 53 post check if
        } // End of else check for role in department.
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
        return new TimesheetHeaderFactory();
    }


    /**
    * Function to read the data and pass it into an excel sheet
    */
    private function downloadReports($download, $fromDate, $toDate, $employeeId, $selectedDepartment, $selectedEmployeeName) 
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
        $selectedDepartmentId = $selectedDepartment;
        // Creating the first array
        $headerArray =array("Sl. No", "Employee Name", "Status", "Timesheet Period","Reporting Manager");
        $queryResultArray = [];
        array_push($queryResultArray,$headerArray);
        array_push($queryResultArray,$headerArray);
        // Creating the second array based on the query result.
        foreach ($download as $data) {
            
            $tmparray =array();
            $getCountDetails = $data->getCountDetails();
            array_push($tmparray,$getCountDetails);

            $getFullName = $data->getFullName();
            array_push($tmparray,$getFullName);

            $getTimesheetStatus = $data->getTimesheetStatus();
            array_push($tmparray,$getTimesheetStatus);

            $getTimesheetWeekDates = $data->getTimesheetWeekDates();
            array_push($tmparray,$getTimesheetWeekDates );

            $getReportingManagerDetails = $data->getReportingManagerDetails();
            array_push($tmparray,$getReportingManagerDetails);

            $selectedDepartment = $data->getDepartmentName();
            array_push($queryResultArray,$tmparray);
            
        }

        // To name the downloaded excel sheets this block is used.
        if (!empty($employeeId)) {
            $selectedDepartment = $selectedEmployeeName;    
        }

        if ($selectedDepartmentId == "0" && empty($employeeId)) {
            $selectedDepartment = "All Departments";
        }


        $availableRows = count($queryResultArray) + 2;
        // With the required data available, we need to initiate the Excel download.
        $objPHPExcel = new PHPExcel();
        // Reading cell by cell
        $objPHPExcel->setActiveSheetIndex(0);
        foreach($queryResultArray as $row => $columns) {
            foreach($columns as $column => $data) {
                $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow($column, $row, $data);
            }
        } 

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

        $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle($fromDate.' - '.$toDate);
       
        $styleArray = array (
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'f28c38'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')
            ->getBorders()->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->getActiveSheet()->getStyle('A2:' . 
        $objPHPExcel->getActiveSheet()->getHighestColumn() . 
        $objPHPExcel->getActiveSheet()->getHighestRow())
            ->getBorders()->getAllBorders()->
            setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //Fill color in the header cells
        $objPHPExcel->getActiveSheet()->getCell('A1');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('B1');
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('C1');
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('D1');
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('E1');
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
                
        $objPHPExcel->getActiveSheet()->getCell('B'.$availableRows)->setValue('Generated on ' .date('Y-m-d h:i:s a'). ' IST');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$availableRows)->getFont()->setBold(true);
        $objWriter->save('php://output');
        exit();
    }
	
}

?> 
