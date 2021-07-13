<?php 
class Configuration
{

	protected $CI;
	
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
    public function mailConfiguration()
    {
        $config['protocol']="smtp";
		$config['smtp_host']="ssl://email-smtp.us-east-1.amazonaws.com";
		$config['smtp_port']="465";
		$config['smtp_user']="AKIA34I7YWRULMMJ25KM";
		$config['smtp_pass']="BLmFl9xdx+nRN65+pv9W+/uCnmPIwVL4maBWm4CHDBVN";
		$config['charset']="iso-8859-1";
		$config['newline']="\r\n";
		$config['mailtype'] = "html";
		return $config;
	}
	
	public function mailerKeyPath()
	{
		return "E:\\wamp\\www\\ApiCaller\\application\\third_party\\EmployeeConfirmation_key.txt";
	}
}