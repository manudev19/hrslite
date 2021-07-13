<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

trait Employee_Confirmation_mailer_key
{
  function get_employee_confirmation_mailer_key($location)
  {
	$file = new SplFileObject($location);
	$employee_confirmation_mailer_key_array = array();
	
	$counter = 0;
	
	while (!$file->eof()) 
	{
		$employee_confirmation_mailer_key_array[$counter] = $file->current();
		$file->next();
		$counter++;
	}  

	return $employee_confirmation_mailer_key_array; 
  }

}

class EmployeeConfirmationRemainderMailerService extends \Restserver\Libraries\REST_Controller
{
	use Employee_Confirmation_mailer_key;


	const MESSAGE = "message";
	const SUCCESS = "success";

	public function __construct()
    {
        parent::__construct();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Content-Type', 'application/json');
		
		$this->load->library("libMail/employeeconfirmationmailer",'employeeconfirmationmailer');	
		$this->load->library("libMail/configuration","configuration");
		
		$this->load->model("select_model", "select");
	}

	
 	public function EmployeeConfirmationRemainderMailer_post($key)
	{
        
		 if($key == $this->get_employee_confirmation_mailer_key($this->configuration->mailerKeyPath())[0])
		 {
		 
		

			$get_employee_data = $this->select->select_employee_confirmation();
			
			         $data = $this->employeeconfirmationmailer->EmployeeConfirmationEmailTemplate($get_employee_data);
		          	 $this->set_response(
			      	 	   [
							EmployeeConfirmationRemainderMailerService::SUCCESS => $data,
							EmployeeConfirmationRemainderMailerService::MESSAGE => "Mail Has been sent"
							 ],\Restserver\Libraries\REST_Controller::HTTP_OK);
					
				
		}
	   else
	   {
		 $this->set_response(
		   [
			EmployeeConfirmationRemainderMailerService::SUCCESS => FALSE, 
			EmployeeConfirmationRemainderMailerService::MESSAGE => 'Invalid Mail Request'
		   ],
		   \Restserver\Libraries\REST_Controller::HTTP_CONFLICT);
	   }
	}
 

}