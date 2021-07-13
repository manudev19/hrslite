<?php 
/**
 * my preonboarding object class
 */
class MyPreonboardingEmployee
{
	public function loadData($data)
	{
		$this->candidate_no = $data['candidate_no'];
		$this->full_name = $data['full_name'];
		$this->designation = $data['designation'];
		$this->department = $data['department'];
		$this->joined_Date = $data['joined_Date'];
        $this->manager_name = $data['reporting_manager'];
        $this->workstation_no=$data['workstation_no'];
		$this->editLinkNameAdmin = Edit;
    }
    public function loadDataManager($data)
	{ 
	   $this->manager_name = $data['reporting_manager'];
        $this->candidate_no = $data['candidate_no'];
        $this->full_name = $data['full_name'];
        $this->joined_Date = $data['joined_Date'];
        $this->designation = $data['designation'];
		$this->department = $data['department'];
        $this->dedicated = $data['dedicated'];
		$this->international = $data['international'];
        $this->locations = $data['locations'];
        $this->workstation_no=$data['workstation_no'];
        $this->editLinkNameManager = Edit;

    }
	public function getEditLinkForAdmin() {
		$this->editLinkNameAdmin = __("Edit");
			return $this->editLinkNameAdmin;
		}	
        public function getEditLinkForManager() {
            $this->editLinkNameManager = __("Edit");
                return $this->editLinkNameManager;
            }	
	public function getCandidateNumber()
	{
		return $this->candidate_no;
	}
	public function getFullName()
	{
		return $this->full_name;
	}
	public function getDesignation()
	{
		return $this->designation;
	}
	public function getDepartment()
	{
		return $this->department;
	} 
	public function getJoiningDate()
	{
		return $this->joined_Date;
	}
	public function getManagerName(){
		return $this->manager_name;
    }
    public function getDedicated()
	{
		return $this->dedicated;
	} 
	public function getInternational()
	{
		return $this->international;
	}
	public function getWorkstation(){
		return $this->workstation_no;
    }
    public function getLocation()
	{
		return $this->locations;
	}
    }
    ?>