<?php

require 'Configuration.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeConfirmationmailer extends Configuration
{

	const ADMIN_MAIL = "hrmadmin@suntechnologies.com";
	const HR_GROUP_MAIL = "hr@suntechnologies.com";

	protected $CI;
	private $mailConfiguration;

	public function __construct()
    {
        
        $this->CI = &get_instance();
        $this->mailConfiguration = new Configuration();
	}

	private final function sendMail($managers,$template)
	{
		$this->CI->load->library('email');
		$this->CI->email->initialize( $this->mailConfiguration->mailConfiguration());

		$this->CI->email->from(EmployeeConfirmationmailer::ADMIN_MAIL, 'Employee Probation Confirmation Remainder');
		$this->CI->email->to(EmployeeConfirmationmailer::HR_GROUP_MAIL);  
		
		$this->CI->email->cc($managers);
	  	$this->CI->email->subject('Request For Employee Probation Confirmation');
	  	$this->CI->email->message($template);
		$this->CI->email->send();
		return true;
	}


	public function EmployeeConfirmationEmailTemplate($data)
	{
		$message_head = "Hi,<br />";
		$message_head1 = "Employee Probation Confirmation";
		$message_head2 = "<br> Thanks & Regards, <br>";
	
		$header = "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css'>";
		$header .= "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>";
		$header .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js'></script>";
		$header .= "<script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js'></script>";

		$append_table = "";
		$table_body = "";
		
		$counter = 1;
	
		
			foreach($data->result() as $result)
			{
				$table_body .= "<tr align='center'>";
				$table_body .= "<td style = 'border: 1px solid black',>".$counter."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->employee_id."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->employee_name."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->employee_joined_date."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->manager_name."</td>";
				$table_body .= "</tr>";

				$append_table .= "<table class='table table-responsive' cellspacing='0'>";
				$append_table .= "<tr align='center'>";
				$append_table .= "<th style='border: 1px solid black',> S.No. </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee ID </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Name </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Joining Date </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Manager(s) </th>";
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
				
				$counter++;
				$join=$result->employee_joined_date;
				
				
				if($result->employee_confirmation==null)
		        {	
					$from_date = date('Y-m-d', strtotime($join. '+174 days'));
					switch(true)
					{	
						case (date('D',strtotime($from_date)) == 'Sun'):
						{
							$from_date = date('Y-m-d', strtotime('+1 day' , strtotime($from_date)));
							break;
						}
						case (date('D',strtotime($from_date)) == 'Sat'):
						{
							$from_date = date('Y-m-d', strtotime('+2 day' , strtotime($from_date)));
							break;
						}		
						case (date('D',strtotime($from_date)) == 'Fri'):
						{
							$from_date = date('Y-m-d', strtotime('+3 day' , strtotime($from_date)));
							break;
						}		
						case (date('D',strtotime($from_date)) == 'Thu'):
						{
							$from_date = date('Y-m-d', strtotime('+4 day' , strtotime($from_date)));
							break;
						}
						case (date('D',strtotime($from_date)) == 'Wed'):
						{
							$from_date = date('Y-m-d', strtotime('+5 day' , strtotime($from_date)));
							break;
						}
						case (date('D',strtotime($from_date)) == 'Tue'):
						{
							$from_date = date('Y-m-d', strtotime('+6 day' , strtotime($from_date)));
							break;
						}
					}
				
					if((date('D') == 'Mon') && (date('D',strtotime($from_date)) == 'Mon'))
					{
						$this->sendMail($result->manager_email,$message);
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