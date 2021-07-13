<?php
defined('BASEPATH') || exit('No direct script access allowed');


//* ADDING TRAIT TO FETCH TEMPLATE PATH
//TODO  :  create a function that accepts a flag and returns the respective template depending on flag


class InsertService extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();

		$this->load->model('create_model', 'create');
		$this->load->model('insert_model', 'insert');
		$this->load->model('select_model', 'select');
		$this->load->model('update_model', 'update');

	}
    
	public function hs_hr_employee($txtEmployeeId,$emp_resion_of_resignation,$DOR,$emp_manager_id,$empNumber)
	{
		 $resion = str_replace(' ', '%20', $emp_resion_of_resignation);
		 $resion1 = str_replace('"', '%22', $resion);
	
		$this->update->update_hs_hr_employee($txtEmployeeId,$resion1,$DOR,$emp_manager_id,$empNumber);

	}
	public function hr_aproval($txtEmployeeId,$personal_Hr_Status,$empNumber)
	{
	
	$this->update->update_hr_aproval($txtEmployeeId,$personal_Hr_Status,$empNumber);

	}
	public function manager_aproval($txtEmployeeId,$personal_Manager_Status,$empNumber)
	{
		
			$this->update->update_manager_aproval($txtEmployeeId,$personal_Manager_Status,$empNumber);

	}

}
