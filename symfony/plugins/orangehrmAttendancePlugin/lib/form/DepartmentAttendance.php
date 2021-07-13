<?php

	class DepartmentAttendance {
		
		private $employeId;
		private $employefirstname;
		private $employelastname;
		private $attendanceshift;
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


		public function loadData($data,$index)
		{
			//var_dump($request->getParameter('sub_unit'));exit;
			$this->index = $index;
			$this->employeId = $data['emp_number'];
			$this->empId = $data['employee_id'];
			$this->employefirstname = $data['emp_firstname'];
			$this->employelastname = $data['emp_lastname'];
			$this->monthstartdates = $data['punch_in_user_time'];
			$this->attendanceshift = $data['shift'];
			$this->inputtime = $data['punch_in_user_time'];
			$this->outtime = $data['punch_out_user_time'];
			$this->workinghours = $data['working_hours'];
			$this->overtime = $data['over_time'];
			$this->breaktime = $data['break_time'];
			$this->actualworkinghours = $data['actual_working_hours'];
			$this->status = $data['state'];
			$this->loginDate = $data['login_date'];
			if (isset($data['departmentattendance']) && !empty($data['departmentattendance'])) {
				$this->selectedDepartment = $data['departmentattendance'];
			} else {
				$this->selectedDepartment = "";
			}
		
	   }

	   public function getempId(){
			return $this->empId;
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

		public function getMonthStartDates(){
			$date=$this->monthstartdates;
			 echo date('d-M-y', strtotime($date));
			//return $this->monthstartdates;
		}
		public function getAttendanceShift(){
			return $this->attendanceshift;
		}

		public function getInputTime(){
			return $this->inputtime;
		}

		public function getOutTime(){
			return $this->outtime;
		}
	
		public function getShift(){
			return $this->index;
		}
		 
		public function getCountDetails(){
			return $this->index;
		}

		public function getWorkingHours(){
			return $this->workinghours;
		}
		
		public function getOverTime(){
			return $this->overtime;
		}
		
		public function getBreakTime(){
			return $this->breaktime;
		}
	
		public function getActualWorkingHours(){
			return $this->actualworkinghours;
		}
	
		public function getStatus(){
			return $this->status;
		}
		
		public function getLoginDate()
		{
			return $this->loginDate;
		}

		public function getDepartmentName(){
			return $this->selectedDepartment;
		}

	}
?>