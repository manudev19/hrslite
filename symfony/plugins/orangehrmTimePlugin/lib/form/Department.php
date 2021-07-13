<?php

	class Department {
		
		private $employeId;
		private $employefirstname;
		private $employelastname;
		private $timesheetstatus;
		private $timesheetWeekDates;
		private $employeeService;
		private $employeeManagers;
		private $selectedDepartment;
        private $employeIdForTimesheet;
        private $timsheetweeklytotalhours;
       	private $index=0; 

		public function getEmployeeService() {
	        if(is_null($this->employeeService)) {
	            $this->employeeService = new EmployeeService();
	            $this->employeeService->setEmployeeDao(new EmployeeDao());
	        }
        	return $this->employeeService;
    	}

		public function getEmpID($empnumber) {
			return $this->getEmployeeService()->getEmpID($empnumber);
		}
	
    	public function getImmediateSupervisors($empnumber) {
    		return $this->getEmployeeService()->getSupervisorListForEmployee($empnumber);
        }
        
        public function getTimsheetweeklyhours($empnumber,$startdate,$endsate,$state) {
    		return $this->getEmployeeService()->getTimsheetWeeklyTotalHours($empnumber,$startdate,$endsate,$state);
    	}

		public function loadData($data,$index)
		{
			//var_dump($request->getParameter('sub_unit'));exit;
			$this->index = $index;
			$this->employeId = $data['emp_number'];
			$this->employefirstname = $data['emp_firstname'];
			$this->employelastname = $data['emp_lastname'];
			$this->timesheetstatus = $data['state'];
			$this->timesheetWeekStartDate = $data['start_date'];
			$this->timesheetWeekEndDate = $data['end_date'];
			if (isset($data['department']) && !empty($data['department'])) {
				$this->selectedDepartment = $data['department'];
			} else {
				$this->selectedDepartment = "";
			}
			$employeeIdForTimesheets= $this->getEmpID($data['emp_number']);
 			
 			$employeIds = array();
 			foreach ($employeeIdForTimesheets as $key=>$value) {
 				
 				$this->employeIdForTimesheet=$value['employeNumber'];
	 			} 
			
			$getManagerDetails = $this->getImmediateSupervisors($data['emp_number']);
			$mangerNames = array();
			foreach ($getManagerDetails as $value) {
				$manger=$value['emp_firstname']." ".$value['emp_lastname'];
				array_push($mangerNames,$manger);
				
			} 

            $this->employeeManagers=implode(', ',$mangerNames);
            
            $timsheetweekhours= $this->gettimsheetweeklyhours($data['emp_number'],$data['start_date'],$data['end_date'], $data['state']);
			
			
			foreach ($timsheetweekhours as $key=>$value) {
				if( $data['state']!='NOT SUBMITTED'){
					$timesheetService = new TimesheetService();
					$timeInSecs = $value['duration'];
					$this->timsheetweeklytotalhours= $timesheetService->convertDurationToHours($value['duration']);		
				}
			} 
	   }
        
         public function getemployeeid(){
		   return $this->employeId;
       }
       
       public function getTimsheetWeeklyTotalHours(){
		return $this->timsheetweeklytotalhours;
	   }
	   
	   public function getemployeIdForTimesheet(){
 
		return $this->employeIdForTimesheet;
		}

		public function getemployefirstname(){
			return $this->employefirstname;
		}

		public function getemployelastname(){
			return $this->employelastname;
		}

		public function getFullName(){
			return $this->employefirstname." ".$this->employelastname;
		}

		public function getTimesheetStatus(){
			if($this->timesheetstatus=='SUBMITTED'){
				return 'NOT APPROVED';
			 }else{
				return $this->timesheetstatus;
				}
		}

		public function getTimesheetWeekDates(){
			return $this->timesheetWeekStartDate." ". __("to") . " " .$this->timesheetWeekEndDate;
		}

		public function getReportingManagerDetails(){
			return $this->employeeManagers;
		}

		public function getCountDetails(){
			return $this->index;
		}

		public function getDepartmentName(){
			return $this->selectedDepartment;
		}

	}
?>
