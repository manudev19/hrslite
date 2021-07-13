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

class OnboardingEmployeeConfirmationRemainderMailerService extends \Restserver\Libraries\REST_Controller
{
	use Employee_Confirmation_mailer_key;
//! read it FROM A CONFIG FILE

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
		
		$this->load->library("libMail/onboardingemployeeconfirmationmailer",'onboardingemployeeconfirmationmailer');	
		$this->load->library("libMail/configuration","configuration");
		
		$this->load->model("Select_model", "select");
	}

	//  // TODO :  This post function should accept a parameter.aboutLink
	// //  TODO : Use that value to compare with the key from the file that you are reading from trait.
 	public function OnboardingEmployeeConfirmationmailer_post($key)
	{
        
		 if($key == $this->get_employee_confirmation_mailer_key($this->configuration->mailerKeyPath())[0])
		 {
		 
		// // TODO : Initialize select model and get data of employees on joining date calculating apprisal date
		// // TODO : Save data in array and send it to library.

		$get_employee_data = $this->select->select_pre_onboarding_employee();
		$data=$this->onboardingemployeeconfirmationmailer->OnboardingEmployeeConfirmationEmailTemplate($get_employee_data);
	    $this->set_response(
			[
			OnboardingEmployeeConfirmationRemainderMailerService::SUCCESS => $data,
			OnboardingEmployeeConfirmationRemainderMailerService::MESSAGE => "Mail Has been sent"
			],
			\Restserver\Libraries\REST_Controller::HTTP_OK);
		}
	   else
	   {
		 $this->set_response(
		   [
			OnboardingEmployeeConfirmationRemainderMailerService::SUCCESS => FALSE, 
			OnboardingEmployeeConfirmationRemainderMailerService::MESSAGE => 'Invalid Mail Request'
		   ],
		   \Restserver\Libraries\REST_Controller::HTTP_CONFLICT);
	   }
	}
 

   /**
    *   API TEST FUNCTION
    */

	// public function GetfooBar_get($getParam)
	// {
	//    $this->set_response(
	// 	 [
	// 	   'status' => TRUE,
	// 	   'message' => $getParam
	// 	 ],
	//    \Restserver\Libraries\REST_Controller::HTTP_OK);
	// }
}
