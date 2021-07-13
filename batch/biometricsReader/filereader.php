<?php
function processBiometricsReportFile($biometricsFileName, $conn) {
   $file_name = fopen($biometricsFileName, "r");
      while (!feof($file_name)) {
         $line = fgets($file_name);
         $row  = explode(";", $line);
         if(sizeof($row) > 1) {
            $emp_number = getEmployeeNumber($conn , $row[0]);
            if( $emp_number == 0 ){
               continue;
            }
            $attendance_biometrics_info               			= new stdClass();
            $attendance_biometrics_info->emp_number				= $emp_number;
            $attendance_biometrics_info->date 					   = $row[1];
            $attendance_biometrics_info->shift 					   = $row[2];
            $attendance_biometrics_info->in_time 				   = getStrToDate($row[3], $row[1]);
            $attendance_biometrics_info->out_time 				   = getStrToDate($row[4], $row[1]);
            $attendance_biometrics_info->working_hours   		= $row[5];
            $attendance_biometrics_info->over_time			 	   = $row[6];
            $attendance_biometrics_info->break_time			 	= $row[7];
            $attendance_biometrics_info->actual_working_hours 	= $row[8];
            $attendance_biometrics_info->status 				 	= $row[9];

            add_attendance_db($conn, $attendance_biometrics_info);
         }
      }
      fclose($file_name);
}
function getStrToDate($dateStr, $date2 ){
   $date = DateTime::createFromFormat('d/m/Y H:i', $dateStr);
   if($date){
      return  $date->format('Y-m-d H:i:s');
   }else{
      $date = DateTime::createFromFormat('d/m/Y', $date2);
      if($date){
         return  $date->format('Y-m-d');
      }
   }
   return false;
}
?>