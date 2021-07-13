<?php
require_once 'PHPMailerAutoload.php';

// require_once('util/timesheet_util.php');
/*require_once('PHPMailer/src/PHPMailer.php');
require_once('PHPMailer/src/SMTP.php');
require_once('PHPMailer/src/Exception.php');

*/
/*define("SMTP_HOST", "postfix.sti.com");
define("SMTP_AUTH", "True");
define("SMTP_USERNAME", "hrm@sti.com"); // hrm@sti.com
define("SMTP_PASSWORD", 'sun12345'); //Zh25Q\7
define("SMTP_SECURE", "No");
define("SMTP_PORT", "25");
define("FROM_EMAIL", "hrm@suntechnologies.com");
define("IS_HTML_EMAIL", "True"); */

define("SMTP_HOST", "email-smtp.us-east-1.amazonaws.com"); // secure.emailsrvr.com
define("SMTP_AUTH", "True");
define("SMTP_USERNAME", "AKIA34I7YWRULMMJ25KM"); // hrm@sti.com
define("SMTP_PASSWORD", 'BLmFl9xdx+nRN65+pv9W+/uCnmPIwVL4maBWm4CHDBVN'); //Zh25Q\7
define("SMTP_SECURE", "ssl");
define("SMTP_PORT", "465");
define("FROM_EMAIL", "hrmadmin@suntechnologies.com");
define("IS_HTML_EMAIL", "True");