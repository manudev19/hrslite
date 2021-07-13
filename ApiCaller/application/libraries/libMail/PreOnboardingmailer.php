<?php

require 'Configuration.php';
defined('BASEPATH') or exit('No direct script access allowed');

class PreOnboardingmailer extends Configuration
{

const ADMIN_MAIL = "hrmadmin@suntechnologies.com"; 
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

		$this->CI->email->from(PreOnboardingmailer::ADMIN_MAIL, 'Employee Pre Onboarding Information');
	    $this->CI->email->to($managers);

		// // TODO : GET MANAGERS  MAIL ADDRESS 
		// // TODO : data should be in  this format => "manikantak@suntechnologies.com,vedavithr@suntechnologies.com"  

		$this->CI->email->subject('Pre Onboarding Employee Information');
		$this->CI->email->message($template);
		$this->CI->email->send();
		return true;
	}
	public function PreOnboardingEmailTemplate($data)
	{
		$message_head = "Hi,<br />";
		$message_head1 = "Please find below Pre-Onboarding Employee Information";
		$message_head4 = "Please click on the below link to fill remaining information";
		$message_head3 = "http://hrm.sti.com/symfony/web/index.php/pim/viewMyPreOnboarding" ;
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
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->joined_Date . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->full_name . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->designation . "</td>";
			$table_body .= "<td style = 'border: 1px solid black',>" . $result->department . "</td>";
		
			$table_body .= "</tr>";

			$append_table .= "<table class='table table-responsive' cellspacing='0'>";
			$append_table .= "<tr align='center'>";
			$append_table .= "<th style='border: 1px solid black',> Candidate No. </th>";
			$append_table .= "<th style='border: 1px solid black',> Candidate Joining Date </th>";
			$append_table .= "<th style='border: 1px solid black',> Employee Name </th>";
			$append_table .= "<th style='border: 1px solid black',> Designation </th>";
			$append_table .= "<th style='border: 1px solid black',> Department </th>";
		
			$append_table .= "</tr>";
			$append_table .= $table_body;
			$append_table .= "</table>";

			$message = "<html>";
			$message .= "<head>" . $header . "</head>";
			$message .= "<main class='container'>";
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head . "</p>";
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head1 . "</p>";
			$message .= $append_table;
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head4 . "</p>";
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head3 . "</p>";
			$message .= "<p style='font-size:14px; font-style: Calibri;'>" . $message_head2 . "</p>";
			$message .= "</main>";
			$message .= "</html>";

			$counter++;
			$issuing_date = $result->issuing_Date;
			
			if ($issuing_date == date('Y-m-d')) {
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
