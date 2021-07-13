<?php

require 'Configuration.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class SeparationAdminConfirmationMailer extends Configuration
{  
	 const IT_MAIL = "itsupport@suntechnologies.com,facility@suntechnologies.com,mallanpatil@suntechnologies.com";
	 const HR_GROUP_MAIL = "hr@suntechnologies.com";
	 

	protected $CI;
	private $mailConfiguration;

	public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI = &get_instance();
        $this->mailConfiguration = new Configuration();
	}

	//TODO : use sendMail in AppraisalEmailTemplate

	private final function sendMail($managers_email,$emp_mail,$template)
	{
		
		$this->CI->load->library('email');
		$this->CI->email->initialize( $this->mailConfiguration->mailConfiguration());

		$this->CI->email->from(SeparationAdminConfirmationMailer::HR_GROUP_MAIL, 'Employee Separation Confirmation Reminder');
		$this->CI->email->to(SeparationAdminConfirmationMailer::IT_MAIL);
		  
		// // TODO : GET MANAGERS  MAIL ADDRESS 
		// // TODO : data should be in  this format => "manikantak@suntechnologies.com,vedavithr@suntechnologies.com"  
		
		$result = $emp_mail . ',' . $managers_email;
		$this->CI->email->cc($result);
	  	$this->CI->email->subject('Request For Separation Confirmation');
	  	$this->CI->email->message($template);
		$this->CI->email->send();
		return true;
	}
	private final function sendMailEmp($managers_email,$emp_mail,$template)
	{
		
		$this->CI->load->library('email');
		$this->CI->email->initialize( $this->mailConfiguration->mailConfiguration());

		$this->CI->email->from(SeparationAdminConfirmationMailer::HR_GROUP_MAIL, 'Employee Separation Confirmation ');
	
		$this->CI->email->to($managers_email);
		  
		// // TODO : GET MANAGERS  MAIL ADDRESS 
		// // TODO : data should be in  this format => "manikantak@suntechnologies.com,vedavithr@suntechnologies.com"  
		
		$this->CI->email->cc($emp_mail);
	  	$this->CI->email->subject('Request For Separation Confirmation');
	  	$this->CI->email->message($template);
		$this->CI->email->send();
		return true;
	}


	public function SeparationAdminConfirmationEmailTemplate($data)
	{
		$message_head = "Hi,<br />";
		$message_head1 = "Please find below Employee Separation details. Need your confirmation";
		$message_head2 = "<br> Thanks & Regards, <br>";
	
		$header = "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css'>";
		$header .= "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>";
		$header .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js'></script>";		$header .= "<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js'></script>";

		$append_table = "";
		$table_body = "";
		
		$counter = 1;
	
		
			foreach($data->result() as $result)
			{
				
				$table_body .= "<tr align='center'>";
				$table_body .= "<td style = 'border: 1px solid black',>".$counter."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->employee_id."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->employee_name."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->emp_resion_of_resignation."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->emp_Date_of_resignation."</td>";
				if($result->work_station!=4){
				$table_body .= "<td style = 'border: 1px solid black',>".$result->Manager_Status."</td>";
				}
				
				$table_body .= "<td style = 'border: 1px solid black',>".$result->HR_Status."</td>";

				$table_body .= "</tr>";

				$append_table .= "<table class='table table-responsive' cellspacing='0'>";
				$append_table .= "<tr align='center'>";
				$append_table .= "<th style='border: 1px solid black',> S.No. </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee ID </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Name </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Reason for Resignation  </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Date of Resignation </th>";
				if($result->work_station!=4){
				$append_table .= "<th style='border: 1px solid black',>Manager Approval </th>";
				}
				if($result->work_station==4){
				$append_table .= "<th style='border: 1px solid black',>Manager/HR Approval </th>";
				} else{
					$append_table .= "<th style='border: 1px solid black',>HR Approval </th>";
				}
				$append_table .= "</tr>"; 
				$append_table .= $table_body;
				$append_table .= "</table>";



				$message = "<html>";
				$message .= "<head>".$header."</head>";
				$message .= "<main class='container'>";
				$message .= "<p style='font-size:14px; font-style: Calibri;'>".$message_head."</p>";
				$message .= "<p style='font-size:14px; font-style: Calibri;'>".$message_head1."</p>";
				$message .= $append_table;
				$message .= "<p style='font-size:14px; font-style: Calibri;'>".$message_head2."</p>";
				$message .= "</main>";
				$message .= "</html>";

				
				if($result->work_station==4)
				{
					
					if($result->HR_Status=="Approved" )	
				{	
					
					$this->sendMail($result->emp_manager_id,$result->emp_work_email,$message);
				}
				else if($result->HR_Status=="Rejected")
				{
					
					$this->sendMailEmp($result->emp_manager_id,$result->emp_work_email,$message);
				}
				}
				else {
		
				if($result->HR_Status=="Approved" && $result->Manager_Status=="Approved" )	
				{	
					
					$this->sendMail($result->emp_manager_id,$result->emp_work_email,$message);
				}
				else if($result->HR_Status=="Rejected" && $result->Manager_Status=="Approved")
				{
					
					$this->sendMailEmp($result->emp_manager_id,$result->emp_work_email,$message);
				}
				}
				$table_body = null;
				$message = null;
				$append_table = null;
			}
		if($counter)
		{
			return true;
		}

	}
}