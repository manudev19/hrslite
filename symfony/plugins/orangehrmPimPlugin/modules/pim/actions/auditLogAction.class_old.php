<?php
class auditLogAction extends sfAction {

	private $employeeNumber;
    private $AuditLog;
    private $AuditLogService;
    public $auditLogRecords;
   
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    
    
    public function getAuditLogDao() {

        if (is_null($this->AuditLog)) {
            $this->AuditLog = new AuditLogDao();
        }
        return $this->AuditLog;
    }

    /**
    * This is the entry function for the request that is hitting the server
    *
    */
  	public function execute($request) {
       
          $this->form = new auditLogForm();
          // Chekcing for the post values. If no post values are there the template will be executed.
          $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id","employeeId");
          $employeeList = UserRoleManagerFactory::getUserRoleManager()
                  ->getAccessibleEntityProperties('Employee', $properties);
          $this->form->employeeList = $employeeList;
    /** 
    * Pagination
    */
          $maxPageLimit = 100; 
          $this->pager = new SimplePager('Report', $maxPageLimit);
          $this->pager->setPage(($request->getParameter('pageNo') != '') ? $request->getParameter('pageNo') : 0);
       
        if ($request->isMethod('post')) {

            $this->id = $request->getParameter('audit_id');
       
        	$selectedModule = $request->getParameter('modules');
			$selectedSection = $request->getParameter('sections');
            $selectedAction = $request->getParameter('actions');
            $selectedActionOwner =$request->getParameter('action_owner');
            $selectedEmployee=$request->getParameter('affected_employee');
            $fromDate =$request->getParameter('from_date');
            $toDate = $request->getParameter('to_date');
            $addedToDate = date('Y-m-d', strtotime('+1 day' , strtotime($toDate)));

            $owner= $this->getAuditLogDao()->getEmployeeId($selectedActionOwner['empId']);
            $emp=$this->getAuditLogDao()->getEmployeeId($selectedEmployee['empId']);

            $ownerId=($selectedActionOwner['empName']=='Type for hints...'||$selectedActionOwner['empName']==''?'':$owner);
            $empId=($selectedEmployee['empName']=='Type for hints...'||$selectedEmployee['empName']==''?'':$emp);
        
			$download=$request->getParameter('download');
            $download1=$request->getParameter('isDownloadable');
     
       		if($fromDate!='' && $toDate!='') {
                $this->form->setModules($selectedModule);
                $this->form->setFromDate($fromDate);
                $this->form->setToDate($toDate);
                $this->form->setSections($selectedSection);
                $this->form->setActions($selectedAction);
                $this->form->setActionOwner($selectedActionOwner);
                $this->form->setAffectedEmployee($selectedEmployee);

        	
        	$demoObject=array();
            $numOfRecords= $this->getAuditLogService()->getSearchEmployeeCount((($selectedModule=='All')?'':$selectedModule),
                          (($selectedSection=='All')?'':$selectedSection),
                          (($selectedAction=='All')?'':$selectedAction),
                            $ownerId,
                            $empId,
                          (($fromDate=='yyyy-mm-dd')?'':$fromDate),
                          (($toDate=='yyyy-mm-dd')?'':$addedToDate));
                 
            $this->pager->setNumResults($numOfRecords);
            $this->pager->init();
            $offset = $this->pager->getOffset();
            $offset = empty($offset) ? 0 : $offset;
            $limit = $this->pager->getMaxPerPage();
           
             
            $result = $this->getAuditLogService()->getAuditDataDownload(
                       (($selectedModule=='All')?'':$selectedModule),
                       (($selectedSection=='All')?'':$selectedSection),
                       (($selectedAction=='All')?'':$selectedAction),
                         $ownerId,
                         $empId,
                        (($fromDate=='yyyy-mm-dd')?'':$fromDate),
                        (($toDate=='yyyy-mm-dd')?'':$addedToDate),$limit, $offset);
                      
            $this->actionLogRecords=$this->getAuditLogService()->getAuditData(
                        (($selectedModule=='All')?'':$selectedModule),
                        (($selectedSection=='All')?'':$selectedSection),
                        (($selectedAction=='All')?'':$selectedAction),
                          $ownerId,
                          $empId,
                        (($fromDate=='yyyy-mm-dd')?'':$fromDate),
                        (($toDate=='yyyy-mm-dd')?'':$addedToDate),$limit, $offset);
                            

			$index = $offset+1;
			foreach ($this->actionLogRecords as $key => $logReport) {
					$auditObject =new AuditLog();
                    $auditObject->loadData($logReport,$index++);
          			array_push($demoObject,$auditObject);
                }
             
            // Preparing data set for the Excel sheet.
    		if($download1=='Download'){
                 if (isset($logReport) && !empty($logReport)) {
                        $excelSheetArray = [];
                        $sheetIndex = 1;
     
                        foreach ($result as $key => $logReport) {
                            $auditObject =new AuditLog();
                            $auditObject->loadData($logReport,$sheetIndex++);
                            array_push($excelSheetArray,$auditObject);
                        }
                      
                        $this->downloadReports($excelSheetArray, $fromDate, $toDate);
                    } else {
                        $url = url_for('pim/auditLog');
                        echo '<script>alert("No records to download");window.location = "'.$url.'";</script>';
                        exit;
                    }
       			}
             
	 			$this->auditLogRecords=$this->actionLogRecords;
	    	} 
		} // 53 post check if
    } // End of function

   

    /**
    * Function to read the data and pass it into an excel sheet
    */
    private function downloadReports($download, $fromDate, $toDate) 
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
       
        // Creating the first array
        $headerArray =array("Sl. No", "Date and Tme", "User Employee Id", "User","Entity Id","Affected Entity","Action",
        "Table Name","Old Value(s)","Updated Value(s)","Old Data","New Data");
        $queryResultArray = [];
        array_push($queryResultArray,$headerArray);
        array_push($queryResultArray,$headerArray);
        // Creating the second array based on the query result.
        
        foreach ($download as $data) {
    
            $tmpArray =array();
            $value=['Action is INSERT','Not Updated','Action is DELETE'];
            $getCountDetails = $data->getCountDetails();
            array_push($tmpArray,$getCountDetails);

            $selectedTimeStamp = $data->getTimestamp();
            array_push($tmpArray,$selectedTimeStamp);

            $selectedActionOwnerId = $data->getActionOwnerId();
            array_push($tmpArray,$selectedActionOwnerId);

            $selectedActionOwnerName = $data->getActionOwnerName();
            array_push($tmpArray,$selectedActionOwnerName );

            $selectedEmployeeId = $data->getEntityId();
            array_push($tmpArray,$selectedEmployeeId);

            $selectedEntity = $data->getEntity();
            array_push($tmpArray,$selectedEntity);

            $selectedAction = $data->getActions();
            array_push($tmpArray,$selectedAction);

            $actionTableName = $data->getActionTableName();
            array_push($tmpArray,$actionTableName);

            $actionAffectedOldData = $data->getActionAffectedOldData();
            if(!in_array($actionAffectedOldData,$value)){
                foreach($actionAffectedOldData as $old){
                   $oldData[]=$old;
                }
                $generatedOldData=implode($oldData,', ');
                $oldData=array();
                array_push($tmpArray,$generatedOldData);
            }else{
                array_push($tmpArray,$actionAffectedOldData);
                }
            $actionAffectedNewData = $data->getActionAffectedNewData();
            if(!in_array($actionAffectedNewData,$value)){
                foreach($actionAffectedNewData as $new){
                   $newData[]=$new;
                }
                $generatedNewData=implode($newData,', ');
                $newData=array();
                array_push($tmpArray,$generatedNewData);
            }else{
                array_push($tmpArray,$actionAffectedNewData);
                }
        
            $actionOldData = $data->getActionOldData();
            $actionOldData= ($actionOldData==null?'NULL':$actionOldData);
            array_push($tmpArray,$actionOldData);
            $actionNewData = $data->getActionNewData();
            
            $actionNewData=($actionNewData==null?'NULL':$actionNewData);
            array_push($tmpArray,$actionNewData);
            array_push($queryResultArray,$tmpArray);
            
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
        header('Content-Disposition: attachment; filename="Audit Log File.xlsx"');
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

        $objPHPExcel->getActiveSheet()->getStyle("A1:L1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->setTitle($fromDate.' - '.$toDate);
       
        
        $styleArray = array (
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'f28c38'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle('A1:L1')
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
        $objPHPExcel->getActiveSheet()->getCell('F1');
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('G1');
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('H1');
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('I1');
        $objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('J1');
        $objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('K1');
        $objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getCell('L1');
        $objPHPExcel->getActiveSheet()->getStyle('L1')->applyFromArray($styleArray);
        
        date_default_timezone_set("Asia/Kolkata");
        $objPHPExcel->getActiveSheet()->getCell('B'.$availableRows)->setValue('Generated on ' .date('Y-m-d h:i:s a'). ' IST');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$availableRows)->getFont()->setBold(true);
        $objWriter->save('php://output');
        exit();
    }
	
}

?> 
