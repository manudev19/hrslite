<?php
require 'Configuration.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class OnboardingEmployeeConfirmationmailer extends Configuration
{
	const ADMIN_MAIL = "hrmadmin@suntechnologies.com";
	const GROUP_MAIL="itsupport@suntechnologies.com,facility@suntechnologies.com,hr@suntechnologies.com";
	
	protected $CI;
	private $mailConfiguration;

	public function __construct()
    {
        
        $this->CI = &get_instance();
        $this->mailConfiguration = new Configuration();
	}

	private final function sendMail($managers,$template)
	{
	//	$manager="mohanb@suntechnologies.com";
	$this->CI->load->library('email');
	$this->CI->email->initialize( $this->mailConfiguration->mailConfiguration());
	$this->CI->email->from(OnboardingEmployeeConfirmationmailer::ADMIN_MAIL, 'Employee On boarding Reminder');
	$this->CI->email->to($managers);  
    $this->CI->email->cc(OnboardingEmployeeConfirmationmailer::GROUP_MAIL);
	$this->CI->email->subject('Employee Onboarding Information');
	$this->CI->email->message($template);
	$this->CI->email->send();
	return true;
	}
 

	public function OnboardingEmployeeConfirmationEmailTemplate($data)
	{
		$message_head = "Hi,<br />";
		$message_head1 = "Please find below  Onboarding Employee Information";
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
			//	$table_body .= "<td style = 'border: 1px solid black',>".$counter."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->candidate_no."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->full_name."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->joined_Date."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->manager_name."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->designation."</td>";
				$table_body .= "<td style = 'border: 1px solid black',>".$result->department."</td>";
				$table_body .= "</tr>";

				$append_table .= "<table class='table table-responsive' cellspacing='0'>";
				$append_table .= "<tr align='center'>";
			//	$append_table .= "<th style='border: 1px solid black',> S.No.
			//	</th>";
				$append_table .= "<th style='border: 1px solid black',> Candidate Number </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Name </th>";
				$append_table .= "<th style='border: 1px solid black',> Employee Joining Date </th>";
				$append_table .= "<th style='border: 1px solid black',> Reporting Manager(s) </th>";
				$append_table .= "<th style='border: 1px solid black',> Designation </th>";
				$append_table .= "<th style='border: 1px solid black',> Department </th>";
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
				
				$join=$result->joined_Date;
				
				
				if($result->designation!=null)
		        {	
				$confirmation_date1 = date('Y-m-d', strtotime('+7 days'));
				$confirmation_date2=date('Y-m-d', strtotime('+1 days'));
				
				if($join==$confirmation_date1 || $join==$confirmation_date2)
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
	

?>
