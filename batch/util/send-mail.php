<?php

require_once('config/email_config.php');

function sendMail($messageBody, $subject, $empworkmail, $imagePath = null, $selectedDLs = null, $employeeFullName = null)
{
	$mail = new PHPMailer;
    $mail->isSMTP();                                     
    $mail->Host       = SMTP_HOST;  
    $mail->SMTPAuth   = SMTP_AUTH;                              
    $mail->Username   = SMTP_USERNAME;                 
    $mail->Password   = SMTP_PASSWORD;                           
    $mail->SMTPSecure = SMTP_SECURE;                            
    $mail->Port       = SMTP_PORT;  
    $mail->From       = FROM_EMAIL;
	$mail->SMTPSecure = 'ssl';
    $mail->IsHTML(IS_HTML_EMAIL);
    //$mail->SMTPDebug = 4;
    $message = $messageBody;
    $mail2 = clone $mail;
    $mail2->addAddress($empworkmail, $employeeFullName);
    $mail2->ClearReplyTos();
    $mail2->addReplyTo($empworkmail);

    if ($selectedDLs != null) {
        foreach ($selectedDLs as $key => $temp) {
            $mail2->addBCC($temp);
        }    
    }
    $mail2->Subject = $subject;
    $mail2->Body = $message;
    $mail2->AddEmbeddedImage($imagePath,'logo_2u');
    if(!$mail2->send()) {
      $errorMessage = 'Message could not be sent.'.$empworkmail;
      $errorMessage = $errorMessage . 'Mailer Error: ' . $mail2->ErrorInfo;
      echo $errorMessage;
    } else {
      $message = $message . 'Message sent '.$employeeFullName;
      echo "Mail sent to". $employeeFullName. "<br>";; 
    }
}

function sendBirthdayMail($messageBody, $subject, $empworkmail, $imagePath = null, $selectedDLs = null, $employeeFullName = null)
{
  $toAddress='hrmadmin@suntechnologies.com';
	$mail = new PHPMailer;
    $mail->isSMTP();                                     
    $mail->Host       = SMTP_HOST;  
    $mail->SMTPAuth   = SMTP_AUTH;                              
    $mail->Username   = SMTP_USERNAME;                 
    $mail->Password   = SMTP_PASSWORD;                           
    $mail->SMTPSecure = SMTP_SECURE;                            
    $mail->Port       = SMTP_PORT;  
    $mail->From       = FROM_EMAIL;
	$mail->SMTPSecure = 'ssl';
    $mail->IsHTML(IS_HTML_EMAIL);
    //$mail->SMTPDebug = 4;
    $message = $messageBody;
    $mail2 = clone $mail;
    $mail2->addAddress($toAddress, $employeeFullName);
    $mail2->ClearReplyTos();
    $mail2->addReplyTo($empworkmail);

    if ($selectedDLs != null) {
        foreach ($selectedDLs as $key => $temp) {
            $mail2->addBCC($temp);
        }    
    }
    $mail2->Subject = $subject;
    $mail2->Body = $message;
    $mail2->AddEmbeddedImage($imagePath,'logo_2u');
    if(!$mail2->send()) {
      $errorMessage = 'Message could not be sent.'.$empworkmail;
      $errorMessage = $errorMessage . 'Mailer Error: ' . $mail2->ErrorInfo;
      echo $errorMessage;
    } else {
      $message = $message . 'Message sent '.$employeeFullName;
      echo "Mail sent to". $employeeFullName. "<br>";; 
    }
}


function sendMailToHr($toAddress, $messageBody, $subject)
{
	
   
    $mail = new PHPMailer;
    $mail->isSMTP();                                     
    $mail->Host       = SMTP_HOST;  
    $mail->SMTPAuth   = SMTP_AUTH;                              
    $mail->Username   = SMTP_USERNAME;                 
    $mail->Password   = SMTP_PASSWORD;                           
    $mail->SMTPSecure = SMTP_SECURE;                          
    $mail->Port       = SMTP_PORT;  
    $mail->From       = FROM_EMAIL;
	$mail->SMTPSecure = 'ssl';
    $mail->IsHTML(IS_HTML_EMAIL);
    //$mail->SMTPDebug = 4;
    $message = $messageBody;
    $mail2 = clone $mail;
    $mail2->addAddress($toAddress, 'HRM Admin');
	$mail2->addAddress('hrm-wishalerts@suntechnologies.com', 'HRM Wish Alerts');
    $mail2->Subject = $subject;
    $mail2->Body = $message;
    if(!$mail2->send()) {

      $errorMessage = 'Message could not be sent.';
      $errorMessage = $errorMessage . 'Mailer Error: ' . $mail2->ErrorInfo;
      echo $errorMessage; exit;
    } else {
      $message = $message . 'List sent to HR';
      echo "List Sent to HR" . "<br>"; exit;
    }
}



function sendTimesheetToManagers($toAddress, $messageBody, $subject)
{
    
    $mail = new PHPMailer;
    $mail->isSMTP();                                     
    $mail->Host       = SMTP_HOST;  
    $mail->SMTPAuth   = SMTP_AUTH;                              
    $mail->Username   = SMTP_USERNAME;                 
    $mail->Password   = SMTP_PASSWORD;                           
    $mail->SMTPSecure = SMTP_SECURE;                            
    $mail->Port       = SMTP_PORT;  
    $mail->From       = FROM_EMAIL;
	$mail->SMTPSecure = 'ssl';
    $mail->IsHTML(IS_HTML_EMAIL);
    //$mail->SMTPDebug = 4;
    $message = $messageBody;
	
    $mail2 = clone $mail;
    $mail2->addAddress($toAddress, 'HRM Admin');
    $mail2->Subject = $subject;
    $mail2->Body = $message;
    if(!$mail2->send()) {

      $errorMessage = 'Message could not be sent.';
      $errorMessage = $errorMessage . 'Mailer Error: ' . $mail2->ErrorInfo;
      echo $errorMessage;
    } else {
      $message = $message . 'Message sent ';
      echo "Mail sent to Manager" . "<br>";; 
    } 
}

function sendMailToPravin()
{
    $toAddress = "hrm-wishalerts@suntechnologies.com";
    $mail = new PHPMailer;
	
    $mail->isSMTP();                                     
    $mail->Host       = SMTP_HOST;  
    $mail->SMTPAuth   = SMTP_AUTH;                              
    $mail->Username   = SMTP_USERNAME;                 
    $mail->Password   = SMTP_PASSWORD;                           
    $mail->SMTPSecure = SMTP_SECURE;                          
    $mail->Port       = SMTP_PORT;  
    $mail->From       = FROM_EMAIL;
	$mail->SMTPSecure = 'ssl';
    $mail->IsHTML(IS_HTML_EMAIL);
	/*$mail->SMTPOptions = ['ssl'=>[
		'allow_self_signed' => true,
		//'peer_fingerprint'=>'751608908fd7361258827e081c136cb4ae73df4c'
	]]; */
    $mail->SMTPDebug = 1;
    
    $mail2 = clone $mail;
    $mail2->addAddress($toAddress, 'HRM Admin');
    $mail2->Subject = "Mail Test";
    $mail2->Body = "This is a test message";
	
    if(!$mail2->send()) {
      $errorMessage = 'Message could not be sent.';
      $errorMessage = $errorMessage . 'Mailer Error: ' . $mail2->ErrorInfo;
      echo $errorMessage;
    } else {
      $message = $message . 'Message sent ';
      echo "Mail sent to Manager" . "<br>";; 
    }
}
?>