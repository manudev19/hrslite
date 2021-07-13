<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

trait Preonboarding_mailer_key
{
  function get_preonboarding_mailer_key($location)
  {
	$file = new SplFileObject($location);

	$preonboarding_mailer_key_array = array();
	
	$counter = 0;
	
	while (!$file->eof()) 
	{
		$preonboarding_mailer_key_array[$counter] = $file->current();
		$file->next();
		$counter++;
	}  

	return $preonboarding_mailer_key_array; 
  }

}
class PreOnboardingMailerService extends \Restserver\Libraries\REST_Controller
{
	use Preonboarding_mailer_key;
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
		
		$this->load->library("libMail/preonboardingmailer",'preonboardingmailer');	
		$this->load->library("libMail/configuration","configuration");
		
		$this->load->model("select_model", "select");
    }
    
    public function PreOnboardingMailer_post($key)
	{
		 if($key == $this->get_preonboarding_mailer_key($this->configuration->mailerKeyPath())[0])
		 {
		 
		// // TODO : Initialize select model and get data of employees on joining date calculating apprisal date
		// // TODO : Save data in array and send it to library.

			$get_employee_data = $this->select->select_pre_onboarding_employee();
			
			         $data = $this->preonboardingmailer->PreOnboardingEmailTemplate($get_employee_data);
		          	 $this->set_response(
			      	 	   [
							PreOnboardingMailerService::SUCCESS => $data,
							PreOnboardingMailerService::MESSAGE => "Mail Has been sent"
							 ],\Restserver\Libraries\REST_Controller::HTTP_OK);
		}
	   else
	   {
		 $this->set_response(
		   [
			PreOnboardingMailerService::SUCCESS => FALSE, 
			PreOnboardingMailerService::MESSAGE => 'Invalid Mail Request'
		   ],
		   \Restserver\Libraries\REST_Controller::HTTP_CONFLICT);
	   }
	}
 
}