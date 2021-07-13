<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

trait Separation_manager_Confirmation_mailer_key
{
  function get_Separation_manager_confirmation_mailer_key($location)
  {
	$file = new SplFileObject($location);
	$Separation_manager_confirmation_mailer_key_array = array();
	
	$counter = 0;
	
	while (!$file->eof()) 
	{
		$Separation_manager_confirmation_mailer_key_array[$counter] = $file->current();
		$file->next();
		$counter++;
	}  

	return $Separation_manager_confirmation_mailer_key_array; 
  }

}

class SeparationManagerConfirmationMailerService extends \Restserver\Libraries\REST_Controller
{
	use Separation_manager_Confirmation_mailer_key;
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
		
		$this->load->library("libMail/Separationmanagerconfirmationmailer",'Separationmanagerconfirmationmailer');	
		$this->load->library("libMail/configuration","configuration");
		
    $this->load->model("select_model", "select");
	}

	//  // TODO :  This post function should accept a parameter.aboutLink
	// //  TODO : Use that value to compare with the key from the file that you are reading from trait.
 	public function SeparationManagerConfirmationMailer_post($key)
	{
        
		 if($key == $this->get_Separation_manager_confirmation_mailer_key($this->configuration->mailerKeyPath())[0])
		 {
		 
		// // TODO : Initialize select model and get data of employees on joining date calculating apprisal date
		// // TODO : Save data in array and send it to library.

			$get_employee_data = $this->select->Separation_manager_process();
			
			         $data = $this->separationmanagerconfirmationmailer->SeparationManagerConfirmationEmailTemplate($get_employee_data);
		          	 $this->set_response(
			      	 	   [
							SeparationManagerConfirmationMailerService::SUCCESS => $data,
							SeparationManagerConfirmationMailerService::MESSAGE => "Mail Has been sent"
							 ],\Restserver\Libraries\REST_Controller::HTTP_OK);
					
				
		}
	   else
	   {
		 $this->set_response(
		   [
			SeparationManagerConfirmationMailerService::SUCCESS => FALSE, 
			SeparationManagerConfirmationMailerService::MESSAGE => 'Invalid Mail Request'
		   ],
		   \Restserver\Libraries\REST_Controller::HTTP_CONFLICT);
	   }
	}
 
}