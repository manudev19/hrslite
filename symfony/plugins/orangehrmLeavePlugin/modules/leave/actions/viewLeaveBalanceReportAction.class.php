<?php

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
 *
 */

/**
 * Description of viewLeaveBalanceReportAction
 */
class viewLeaveBalanceReportAction extends sfAction {

    public function getForm() {
        return new LeaveBalanceReportForm();
    }
    
    public function getMode() {
        return "admin";
    }
    
    protected function getFormDefaults() {
        $defaults = $this->form->getDefaults();
        
        // Form defaults are in the user date format, convert to standard date format
        $pattern = sfContext::getInstance()->getUser()->getDateFormat();
        $localizationService = new LocalizationService();
        
        $defaults['date']['from'] = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, $defaults['date']['from']);
        $defaults['date']['to'] = $localizationService->convertPHPFormatDateToISOFormatDate($pattern, $defaults['date']['to']);  

        return $defaults;
    }
    
    public function execute($request) {
       
        $this->form = $this->getForm();
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id","employeeId");
        $employeeList = UserRoleManagerFactory::getUserRoleManager()
                ->getAccessibleEntityProperties('Employee', $properties);
            $this->form->employeeList = $employeeList;
        $this->mode = $this->getMode();

        $runReport = false;
        $values = array();
        
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $reportType = $this->form->getValue('report_type');
                $leaveType =$this->form->getValue('leave_type');
                $download= $this->form->getValue('isDownloadable');
                $dateFrom =$this->form->getValue('date');
                $jobTitle =$this->form->getValue('job_title');
                $location =$this->form->getValue('location');
                $subUnit =$this->form->getValue('sub_unit');
                $employee =$this->form->getValue('employee');
                     
                if ($reportType != 0) {
                    $values = $this->form->getValues();          
                    
                    // check permission
                    if (!$this->checkPermissions($this->mode, $reportType, $values)) {
                        //$this->getUser()->setFlash('warning', TopLevelMessages::ACCESS_DENIED);
                        return sfView::SUCCESS;
                    }
                                
                    $runReport = true;
                }   
            }
        } else if ($this->mode == 'my') {
            $reportType = LeaveBalanceReportForm::REPORT_TYPE_EMPLOYEE;
            $values = $this->getFormDefaults();

            $runReport = true;
        }
        
        if ($runReport) {
            $this->reportType = $reportType;
            
            $reportId = ($reportType == LeaveBalanceReportForm::REPORT_TYPE_LEAVE_TYPE) ? 2 : 1;
            $values = $this->convertValues($reportType, $values);
            
            $reportBuilder = new ReportBuilder();
            $numOfRecords = $reportBuilder->getNumOfRecords($reportId, $values);

            if($values['terminated']!=TRUE){
                if($location=='2,-1'){
                    $location=2;
                   }
                   if($location=='1,-1')
                   {
                    $location=1;
                   }
                        $empployeeService=new EmployeeService();
                        $employee=$empployeeService->getEmployeeByDateAndTermination($values['fromDate'],$values['toDate'],$jobTitle,$subUnit);
                        $employeeNo=array_intersect($values['emp_numbers'],$employee);

                if($employeeNo!=null)
                {
                     $values['emp_numbers']=$employeeNo;
                     $numOfRecords=count($employeeNo);
                     if($location!='-1'){
                        $employeeNum=$empployeeService->getEmployeeTerminatedEmployeeByLocation($employeeNo,$location);
                        $values['emp_numbers']=$employeeNum;
                        $numOfRecords=count($employeeNum);
                    }
                }else{
                       $values['emp_numbers']='0';
                   $numOfRecords=0;
                }
            }

            $maxPageLimit = $noOfRecords = sfConfig::get('app_items_per_page'); //$reportBuilder->getMaxPageLimit($reportId);

            $this->pager = new SimplePager('Report', $maxPageLimit);
            $this->pager->setPage(($request->getParameter('pageNo') != '') ? $request->getParameter('pageNo') : 0);

            $this->pager->setNumResults($numOfRecords);
            $this->pager->init();
            $offset = $this->pager->getOffset();
            $offset = empty($offset) ? 0 : $offset;
            $limit = $this->pager->getMaxPerPage();

            if($download=='download')
            {
                    $limit=100000;
            }

            $this->resultsSet = $reportBuilder->buildReport($reportId, $offset, $limit, $values);
            $this->fixResultset($values);

            $this->reportName = $this->getReportName($reportId);

            $headers = $reportBuilder->getDisplayHeaders($reportId);
            $this->tableHeaders = $this->fixTableHeaders($reportType, $headers);

            $this->headerInfo = $reportBuilder->getHeaderInfo($reportId);

            $this->tableWidthInfo = $reportBuilder->getTableWidth($reportId);

            $this->linkParams = $this->getLinkParams($reportType, $values);

            if($download=='download' && $reportType==1){
                if(isset($this->resultsSet) && !empty($this->resultsSet)){
                $this->downloadReport($this->resultsSet,$leaveType,$dateFrom,$jobTitle,$location,$subUnit,$values['terminated']);  
            }
            else{
                 $url = url_for('leave/viewLeaveBalanceReport');
                echo '<script>alert("No records to download");window.location = "'.$url.'";</script>';
                 exit;
            }
                }
            if($download=='download' && $reportType==2){
                    $this->downloadReportForEmployee($this->resultsSet,$dateFrom,$employee);  
                    }
            
        }        
    }
    protected function getLinkParams($reportType, $values) {
        $linkParams = array(
            'fromDate' => array($values['fromDate']),
            'toDate' => array($values['toDate'])
        );
        
        if ($reportType == LeaveBalanceReportForm::REPORT_TYPE_LEAVE_TYPE) {
            $linkParams['leaveTypeId'] = array($values['leaveType']);
        } else {
            $linkParams['empNumber'] = array($values['empNumber']);
        }
        
        return $linkParams;
    }

    protected function downloadReportForEmployee($resultsSet,$dateFrom,$employeeData) {
     
        $employeeService=new EmployeeService();
        $employee = $employeeService->getEmployee($employeeData['empId']);
        $employeeData['empName']= str_replace('US', '', $employeeData['empName']);
        $employeeData['empName']= str_replace('CO', '',  $employeeData['empName']);
        $employeeData['empName']= trim(preg_replace('/[-0-9\s+]+/', ' ', $employeeData['empName']));
      
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','1','Leave Entitlements and Usage Report');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','2','Employee Name : '.$employeeData['empName']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','3','Employee Id : '.$employee->getEmployeeId());
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','4','From : '.$dateFrom['from'].' to '.$dateFrom['to']);
        
        
        $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
        $objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
        $objPHPExcel->getActiveSheet()->mergeCells('A4:F4');
      

        
        $headerArray =array( "Leave Type","Leave Entitlements (Days)", "Leave Pending Approval (Days)","Leave Scheduled (Days)","Leave Taken (Days)","Leave Balance (Days)");
        $queryResultArray = [];
       
        for($i=0; $i<6;$i++){
            $firstempty[$i]=array(); 
            array_push($queryResultArray,$firstempty[$i]);
        }
        array_push($queryResultArray,$headerArray);
         
       
        foreach ($resultsSet as $data) {
          
            $tmparray =array();

            $getLeaveType =  $data['leaveType'];
            array_push($tmparray,$getLeaveType);

            $getLeaveEntitlementTotal = $data['entitlement_total'];
            array_push($tmparray,$getLeaveEntitlementTotal);

            $getLeaveEntitlementPending =  $data['pending'];
            array_push($tmparray,$getLeaveEntitlementPending );

            $getLeaveEntitlementScheduled =  $data['scheduled'];
            array_push($tmparray,$getLeaveEntitlementScheduled);

            $getLeaveEntitlementTaken =  $data['taken'];
            array_push($tmparray,$getLeaveEntitlementTaken);

            $getLeaveEntitlementUnused =  $data['unused'];
            array_push($tmparray,$getLeaveEntitlementUnused);
            array_push($queryResultArray,$tmparray);
          
        }
       
    
        $employeeReportName = $employeeData['empName'] ." Leave Entitlements and Usage Report";
     
        $availableRows = count($queryResultArray) + 2;
        // With the required data available, we need to initiate the Excel download.
       
        // Reading cell by cell
        $objPHPExcel->setActiveSheetIndex(0);
        foreach($queryResultArray as $row => $columns) {
            foreach($columns as $column => $data) {
                
                $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow($column, $row,$data);
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A')
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                       
            }
        } 

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$employeeReportName.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file        
        // Write the Excel file to filename some_excel_file.xlsx in the current directory                
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();

       //Autosize the columns in excel sheet
        foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)
                ->setAutoSize(true);
        } 
      
        $objPHPExcel->getActiveSheet()->getStyle("A6:F6")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle($dateFrom['from'].' to '.$dateFrom['to']);
       
        $objPHPExcel->getActiveSheet()
        ->getStyle('A:F')
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
         $objPHPExcel->getActiveSheet()->getStyle('A1:A6')->applyFromArray($style);
       
        
        $objPHPExcel->getActiveSheet()->getStyle("A1:F6")->getFont()->setBold(true);
       

        $objPHPExcel->getActiveSheet()->getStyle('A6:' . 
            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
            $objPHPExcel->getActiveSheet()->getHighestRow())
        ->getBorders()->getAllBorders()->
        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //Fill color in the header cells
        $objPHPExcel->getActiveSheet()->getCell('A6');
        $objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('B6');
        $objPHPExcel->getActiveSheet()->getStyle('B6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('C6');
        $objPHPExcel->getActiveSheet()->getStyle('C6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('D6');
        $objPHPExcel->getActiveSheet()->getStyle('D6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('E6');
        $objPHPExcel->getActiveSheet()->getStyle('E6')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('F6');
        $objPHPExcel->getActiveSheet()->getStyle('F6')->applyFromArray($styleArray);
       
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        date_default_timezone_set("Asia/Kolkata");
        $objPHPExcel->getActiveSheet()->getCell('B'.$availableRows)->setValue('Generated on ' .date('Y-m-d h:i:s a'). ' IST');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$availableRows)->getFont()->setBold(true);
        $objWriter->save('php://output');
        exit();
    }


    protected function downloadReport($resultsSet,$leaveTypeId,$dateFrom,$jobTitleId,$locationId,$subUnitId,$activeEmployees) {
      
        
        $leaveTypeService = new LeaveTypeService();
        $jobTitleService= new JobTitleService();
        $locationService= new LocationService();
        $companyStructureService = new CompanyStructureService();
        $leaveType = $leaveTypeService->readLeaveType($leaveTypeId);
       if($activeEmployees!='TRUE'){
           $reortFor='Past Employee(s)'  ; 
       }else{
        $reortFor='Active Employee(s)'  ;
       }
        if($jobTitleId!=0){
             $jobs= $jobTitleService->getJobTitleById($jobTitleId);
             $jobTitle=$jobs->getJobTitle();
        }
        else{
             $jobTitle='All';
        }
    
       if($locationId!=-1){
             $locations= $locationService->getLocationById($locationId);
             $location=$locations->getCountryName(). ' - '.$locations->getName();
       }else{
             $location='All';
       }

       if($subUnitId!=0){
             $subUnit=$companyStructureService->getSubunitById($subUnitId);
             $subUnitName= $subUnit->getName();
        }else{
             $subUnitName= 'All';
        }
  
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','1','Leave Entitlements and Usage Report');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','2','Leave Type  : '.$leaveType->getName());
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','3','From  : '.$dateFrom['from'].' to '.$dateFrom['to']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','4','Job Title  : '.$jobTitle);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','5','Location  : '.$location);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','6','Sub Unit  : '.$subUnitName);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('0','7','Report For  : '.$reortFor);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
        $objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
        $objPHPExcel->getActiveSheet()->mergeCells('A4:G4');
        $objPHPExcel->getActiveSheet()->mergeCells('A5:G5');
        $objPHPExcel->getActiveSheet()->mergeCells('A6:G6');
        $objPHPExcel->getActiveSheet()->mergeCells('A7:G7');

        
        $headerArray =array("Employee Id", "Employee Name","Leave Entitlements (Days)", "Leave Pending Approval (Days)","Leave Scheduled (Days)","Leave Taken (Days)","Leave Balance (Days)");
        $queryResultArray = [];
       
        for($i=0; $i<9;$i++){
            $firstempty[$i]=array(); 
            array_push($queryResultArray,$firstempty[$i]);
        }
        array_push($queryResultArray,$headerArray);
         
    
        foreach ($resultsSet as $data) {
          
            $tmparray =array();
            $getEmployeeId = $data['employeeId'];
            array_push($tmparray,$getEmployeeId);

            $getFullName =  $data['employeeName'];
            array_push($tmparray,$getFullName);

            $getLeaveEntitlementTotal = $data['entitlement_total'];
            array_push($tmparray,$getLeaveEntitlementTotal);

            $getLeaveEntitlementPending =  $data['pending'];
            array_push($tmparray,$getLeaveEntitlementPending );

            $getLeaveEntitlementScheduled =  $data['scheduled'];
            array_push($tmparray,$getLeaveEntitlementScheduled);

            $getLeaveEntitlementTaken =  $data['taken'];
            array_push($tmparray,$getLeaveEntitlementTaken);

            $getLeaveEntitlementUnused =  $data['unused'];
            array_push($tmparray,$getLeaveEntitlementUnused);
            array_push($queryResultArray,$tmparray);
          
        }
       
    
            $reportName = "Leave Entitlements and Usage Report";
     


        $availableRows = count($queryResultArray) + 2;
        // With the required data available, we need to initiate the Excel download.
       
        // Reading cell by cell
        $objPHPExcel->setActiveSheetIndex(0);
        foreach($queryResultArray as $row => $columns) {
            foreach($columns as $column => $data) {
                
                $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow($column, $row,$data);
                        $objPHPExcel->getActiveSheet()
                        ->getStyle('A')
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
            }
        } 

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$reportName.'.xlsx"');
        header('Cache-Control: max-age=0');

        // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file        
        // Write the Excel file to filename some_excel_file.xlsx in the current directory                
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_end_clean();

       //Autosize the columns in excel sheet
        foreach (range('A:B', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)
                ->setAutoSize(true);
        } 
      
    

        $objPHPExcel->getActiveSheet()->getStyle("A9:G9")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle($dateFrom['from'].' to '.$dateFrom['to']);
       

       

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
        $objPHPExcel->getActiveSheet()->getStyle('A1:A7')->applyFromArray($style);
       
        
        $objPHPExcel->getActiveSheet()->getStyle("A1:G7")->getFont()->setBold(true);
       

        $objPHPExcel->getActiveSheet()->getStyle('A9:' . 
            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
            $objPHPExcel->getActiveSheet()->getHighestRow())
        ->getBorders()->getAllBorders()->
        setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //Fill color in the header cells
        $objPHPExcel->getActiveSheet()->getCell('A9');
        $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('B9');
        $objPHPExcel->getActiveSheet()->getStyle('B9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('C9');
        $objPHPExcel->getActiveSheet()->getStyle('C9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('D9');
        $objPHPExcel->getActiveSheet()->getStyle('D9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('E9');
        $objPHPExcel->getActiveSheet()->getStyle('E9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('F9');
        $objPHPExcel->getActiveSheet()->getStyle('F9')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('G9');
        $objPHPExcel->getActiveSheet()->getStyle('G9')->applyFromArray($styleArray);
       

      
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
        date_default_timezone_set("Asia/Kolkata");
        $objPHPExcel->getActiveSheet()->getCell('B'.$availableRows)->setValue('Generated on ' .date('Y-m-d h:i:s a'). ' IST');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$availableRows)->getFont()->setBold(true);
        $objWriter->save('php://output');
        exit();
    } 
      
    
    
    protected function convertValues($reportType, $values) {
        
        $today = date('Y-m-d');
        $todayTimestamp = strtotime($today);
        
        $fromDate = $values['date']['from'];
        $fromTimestamp = strtotime($fromDate);
        
        $toDate = $values['date']['to'];
        $toTimestamp = strtotime($toDate);
        
        if ($todayTimestamp < $fromTimestamp) {
            $asOfDate = $today;
        } else if ($todayTimestamp > $toTimestamp) {
            $asOfDate = $toDate;
        } else {
            $asOfDate = $today;
        }
        $this->asOfDate = $asOfDate;
        
        
        $convertedValues = array(
            'leaveType' => $values['leave_type'],
            'empNumber' => $values['employee']['empId'],
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'asOfDate' => $asOfDate,            
        );
        
        if (isset($values['job_title']) && $values['job_title'] != 0) {
            $convertedValues['job_title'] = $values['job_title'];
        }
        
        if (isset($values['sub_unit']) && $values['sub_unit'] != 0) {
            $convertedValues['sub_unit'] = $values['sub_unit'];
        }

        if (isset($values['location']) && $values['location'] != 0 && $values['location'] != -1) {
            $location = $values['location'];
            $locationIds = explode(',', $location);
            $convertedValues['location'] = $locationIds;
        }

        if (!isset($values['include_terminated']) || $values['include_terminated'] != 'on') {
            $convertedValues['terminated'] = 'TRUE';
        }
        
        if ($this->mode != 'my') {
                    
            $userRoleManager = $this->getContext()->getUserRoleManager();
            if ($reportType == LeaveBalanceReportForm::REPORT_TYPE_LEAVE_TYPE) {
                $convertedValues['emp_numbers'] = $userRoleManager->getAccessibleEntityIds('Employee');
            }
        }
        return $convertedValues;
    }
    
    protected function checkPermissions($mode, $reportType, $values) {
        
        $permitted = false;
        
        $dataGroupPermissions = $this->getDataGroupPermissions();
        
        if ($dataGroupPermissions->canRead()) {
            if ($mode == 'my') {
                if ($reportType == LeaveBalanceReportForm::REPORT_TYPE_EMPLOYEE) {
                    if ($values['employee']['empId'] == $this->getUser()->getAttribute('auth.empNumber')) {
                        $permitted = true;
                    }
                }
            } else {
                if ($reportType == LeaveBalanceReportForm::REPORT_TYPE_LEAVE_TYPE) {
                    $permitted = true;
                } else if ($reportType == LeaveBalanceReportForm::REPORT_TYPE_EMPLOYEE) {                
                    $userRoleManager = $this->getContext()->getUserRoleManager();
                    if ($userRoleManager->isEntityAccessible('Employee', $values['employee']['empId'])) {
                        $permitted = true;
                    }
                }
            }        
        }
        
        return $permitted;
    }
    
    /**
     * Fix table headings
     * TODO: Improve report engine to support customizable headers (eg: have a variable in the header)
     * and grouping fields from multiple tables.
     * @param type $headers
     * @return string
     */
    protected function fixTableHeaders($reportType, $headers) {

        $tableHeaders = $headers;
        
        /*$nameKey = $reportType == LeaveBalanceReportForm::REPORT_TYPE_LEAVE_TYPE ? 'personalDetails' : 'leavetype';
      
        $nameHeader = $headers[$nameKey];
        $firstHeader = $headers['g1'];
        $lastHeader = $headers['g6'];
        
        unset($headers[$nameKey]);
        unset($headers['g1']);
        unset($headers['g6']);
        
        $date = $this->form->getValue('date');
        $firstHeader['groupHeader'] = __('As of') . ' ' . set_datepicker_date_format($date['from']);
        $lastHeader['groupHeader'] = __('As of') . ' ' . set_datepicker_date_format(date(time()));         

        
        $otherHeaders = array('groupHeader' => __('From') . ' ' . set_datepicker_date_format($date['from']) . ' ' .
                __('To') . ' ' . set_datepicker_date_format($date['to']));
        
        foreach ($headers as $header) {
            foreach ($header as $key => $label) {
                if ($key != 'groupHeader') {
                    $otherHeaders[$key] = $label;                    
                }
            }
        }
        
        $tableHeaders = array('first' => $nameHeader,
                              'second' => $firstHeader,
                              'rest' => $otherHeaders,
                              'last' => $lastHeader);
        //print_r($tableHeaders);
        
        $tableHeaders = array(
            'leavetype' => array('groupHeader' => '', 'leaveType' => 'Leave Type'),
            'g1' => array('groupHeader' => '', 'entitlement' => 'Entitlements valid as of ' . set_datepicker_date_format($this->asOfDate)), 
            'g2' => array('groupHeader' => '',  'entitlement2' => 'Total Entitlments valid for the period'),
            'g3' => array('groupHeader' => '',  'closing' => 'Leave Balance as of ' . set_datepicker_date_format($this->asOfDate)),
            'g4' => array('groupHeader' => '',  'scheduled' => 'Leave Scheduled'),
            'g5' => array('groupHeader' => '',  'taken' => 'Leave Taken')            
        );
        
        */
        return $tableHeaders;
    }
    
    protected function getDataGroupPermissions() {
        return $this->getContext()->getUserRoleManager()->getDataGroupPermissions(array('leave_entitlements_usage_report'));
    }    
    
    protected function fixResultset($values, $formatNumbers = true) {
        $keep = array();
        
        $configService = new LeaveConfigurationService();
        $includePending = $configService->includePendingLeaveInBalance();        
        
        for ($i = 0; $i < count($this->resultsSet); $i++) {
            $total = isset($this->resultsSet[$i]['entitlement_total']) ? $this->resultsSet[$i]['entitlement_total'] : 0;
            $scheduled = isset($this->resultsSet[$i]['scheduled']) ? $this->resultsSet[$i]['scheduled'] : 0;
            $taken = isset($this->resultsSet[$i]['taken']) ? $this->resultsSet[$i]['taken'] : 0;
            $pending = isset($this->resultsSet[$i]['pending']) ? $this->resultsSet[$i]['pending'] : 0;
            $exclude = isset($this->resultsSet[$i]['exclude_if_no_entitlement']) ? $this->resultsSet[$i]['exclude_if_no_entitlement'] : 0;
            
            if (($total == 0) && ($scheduled == 0) && ($taken == 0) && ($exclude == 1)) {

            } else {
                $unused = $this->getValue($total) - $this->getValue($scheduled) - $this->getValue($taken);
                
                if ($includePending) {
                    $unused = $unused - $this->getValue($pending);
                }
                $this->resultsSet[$i]['unused'] = number_format($unused,2);
                
                if (isset($this->resultsSet[$i]['employeeName']) && isset($this->resultsSet[$i]['termination_id'])) {
                    
                    $this->resultsSet[$i]['employeeName'] = $this->resultsSet[$i]['employeeName'] . " (" . __("Past Employee") . ")";
                }
                if (isset($this->resultsSet[$i]['leaveType']) && isset($this->resultsSet[$i]['leave_type_deleted'])
                        && $this->resultsSet[$i]['leave_type_deleted'] == 1) {
                    
                    $this->resultsSet[$i]['leaveType'] = $this->resultsSet[$i]['leaveType'] . " (" . __("Deleted") . ")";
                }
                
                if ($formatNumbers) {
                    $this->resultsSet[$i]['entitlement_total'] = number_format($this->resultsSet[$i]['entitlement_total'], 2);
                    $this->resultsSet[$i]['scheduled'] = number_format($this->resultsSet[$i]['scheduled'], 2);
                    $this->resultsSet[$i]['pending'] = number_format($this->resultsSet[$i]['pending'], 2);
                    $this->resultsSet[$i]['taken'] = number_format($this->resultsSet[$i]['taken'], 2);
                    $this->resultsSet[$i]['unused'] = number_format($this->resultsSet[$i]['unused'], 2);
                }
                
                $keep[] = $this->resultsSet[$i];
            }
        }
        $this->resultsSet = $keep;
    }
    
    protected function getValue($value) {
        if (empty($value)) {
            $value = 0;
        }
                
        return $value;
    }
    private function getReportName($reportId) {
        $dao = new ReportDefinitionDao();
        $report = $dao->getReport($reportId);
        $reportName = $report->getName();
        return $reportName;
    }
    
}
