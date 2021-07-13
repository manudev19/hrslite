<?php

require 'Configuration.php';
defined('BASEPATH') or exit('No direct script access allowed');

class PreOnboardingSupervisorMailer extends Configuration
{
	const HR_GROUP_MAIL = "hr@suntechnologies.com";
	const IT_MAIL="itsupport@suntechnologies.com,facility@suntechnologies.com";

	protected $CI;
	private $mailConfiguration;

	public function __construct()
	{
		// Assign the CodeIgniter super-objectP
		$this->CI = &get_instance();
		$this->mailConfiguration = new Configuration();
	}

	private final function sendMail($managers, $template)
	{
		$this->CI->load->library('email');
		$this->CI->email->initialize($this->mailConfiguration->mailConfiguration());
		$this->CI->email->from($managers, 'Employee Pre Onboarding Information');
		$this->CI->email->to(PreOnboardingSupervisorMailer::HR_GROUP_MAIL);

		// // TODO : GET MANAGERS  MAIL ADDRESS 
		// // TODO : data should be in  this format => "manikantak@suntechnologies.com,vedavithr@suntechnologies.com"  
		
		$this->CI->email->cc(PreOnboardingSupervisorMailer::IT_MAIL);
		$this->CI->email->subject('Pre Onboarding Employee Information');
		$this->CI->email->message($template);
		$this->CI->email->send();
		return true;
	}
	public function PreOnboardingSupervisorEmailTemplate($data)
	{
		$message_head = "Hi,<br />";
		$message_head1 = "Please find below Pre-Onboarding Employee Information";
		$message_head2 = "<br> Thanks & Regards, <br>";

		$header = "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css'>";
		$header .= "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>";
		$header .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js'></script>";
		$header .= "<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js'></script>";

		$append_table = "";
		$table_body = "";

		$counter = 1;
		foreach ($data->result() as $result) {
            $table_body .= "<tr align='center'>";
           
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->candidate_no . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',padding: 50px>" . $result->joined_Date . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->full_name . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->designation . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->department . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->dedicated . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->international . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->locations . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->manager_name . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->workstation_no . "</td>";
			$table_body .= "</tr>";

			$append_table .= "<table class='table table-responsive' cellspacing='0'>";
            $append_table .= "<tr align='center'>";
          
			$append_table .= "<th style='border: 1px solid black',> Candidate No. </th>";
			$append_table .= "<th style='border: 1px solid black',padding: 50px> Candidate Joining Date </th>";
			$append_table .= "<th style='border: 1px solid black',> Employee Name </th>";
			$append_table .= "<th style='border: 1px solid black',> Designation </th>";
			$append_table .= "<th style='border: 1px solid black',> Department </th>";
			$append_table .= "<th style='border: 1px solid black',> Dedicated/Shared</th>";
			$append_table .= "<th style='border: 1px solid black',> International/Domestic </th>";
			$append_table .= "<th style='border: 1px solid black',> Locations </th>";
			$append_table .= "<th style='border: 1px solid black',> Reporting Manager </th>";
			$append_table .= "<th style='border: 1px solid black',> Work Station No.</th>";
			$append_table .= "</tr>";
			$append_table .= $table_body;
			$append_table .= "</table>";

			$message = "<html>";
			$message .= "<head>" . $header . "</head>";
			$message .= "<main class='container'>";
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head . "</p>";
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head1 . "</p>";
			$message .= $append_table;
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head2 . "</p>";
			$message .= "</main>";
			$message .= "</html>";

			$counter++;
			$issuing_date = $result->issuing_Date;
			
			if ($issuing_date == date('Y-m-d') && $result->workstation_no !=null) 
			{
				$this->sendMail($result->manager_email, $message);
			}
			$table_body = null;
			$message = null;
			$append_table = null;
		}
		if ($counter) {
			return true;
		}
	}
}
