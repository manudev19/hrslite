<?php

	class ExpenseReport {
		
		private $employeId;
		private $employefirstname;
		private $employelastname;
		private $timesheetstatus;
		private $timesheetWeekDates;
		private $employeeService;
		private $employeeManagers;
		private $selectedDepartment;
       	private $index=0; 

		public function getEmployeeService() {
	        if(is_null($this->employeeService)) {
	            $this->employeeService = new EmployeeService();
	            $this->employeeService->setEmployeeDao(new EmployeeDao());
	        }
        	return $this->employeeService;
    	}

    	public function getImmediateSupervisors($empnumber) {
    		return $this->getEmployeeService()->getSupervisorListForEmployee($empnumber);
    	}

		public function loadData($data,$index)

		{
		
		// var_dump($data[0]['date']);
			$this->index = $index;
			// $expense = [];
			//var_dump($request->getParameter('sub_unit'));exit;
			// foreach ($data as $i => $data) {
			// var_dump($data);
			$this->linkName = View;

			$this->employeId = $data['emp_id'];
			$this->projectId = $data['name'];
			$this->expenseId = $data['expense_id'];
			$this->expenseNumber = $data['expenseNumber'];
   			$this->date = $data['date'];
   			$this->status = $data['state'];
   			$this->employeeFullName = $data['employeeFullName'];
   			$this->deptName = $data['deptName'];
			$this->employefirstname = $data['emp_firstname'];
			$this->employelastname = $data['emp_lastname'];
			$this->amount = $data['amount'];
			// var_dump($this->status);exit;
			/*$this->timesheetWeekStartDate = $data['start_date'];
			$this->timesheetWeekEndDate = $data['end_date'];
			if (isset($data['department']) && !empty($data['department'])) {
				$this->selectedDepartment = $data['department'];
			} else {
				$this->selectedDepartment = "";
			}*/
			
			// $getManagerDetails = $this->getImmediateSupervisors($data['emp_number']);
			// $mangerNames = array();
			// foreach ($getManagerDetails as $value) {
			// 	$manger=$value['emp_firstname']." ".$value['emp_lastname'];
			// 	array_push($mangerNames,$manger);
				
			// } 

			// $this->employeeManagers=implode(', ',$mangerNames);
	   }

	   	public function getDeptName(){
	   		return $this->deptName;
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
		public function getLink() {
	        if (!empty($this->expenseId)) {
	            $this->linkName = __("View");
	        }
         	return $this->linkName;
     	}


		/*public function getTimesheetStatus(){
			return $this->timesheetstatus;
		}

		public function getTimesheetWeekDates(){
			return $this->timesheetWeekStartDate." ". __("to") . " " .$this->timesheetWeekEndDate;
		}

		public function getReportingManagerDetails(){
			return $this->employeeManagers;
		}

		public function getDepartmentName(){
			return $this->selectedDepartment;
		}*/
	public function getCountDetails(){
			return $this->index;
		}
	public function getStatus()
     {
     	
      return $this->status;
     }	
     public function getAmount()
     {
     	
      return $this->amount;
     }
     public function getProjectName()
     {
      return $this->projectId;
     }
     public function getDate()
     {
      return $this->date;
     }
     public function getEmployee()
     {
     	
      return $this->employeId;
     }
 public function getExpenseNumber()
     {
     	
      return $this->expenseNumber;
     }

     public function getExpenseId()
     {
     	return $this->expenseId;
     }

     public function getEmployeeFullName()
     {
     	return $this->employeeFullName;
     }


}
?>
