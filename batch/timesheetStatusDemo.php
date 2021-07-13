<?php

	require_once('db/database.php');
	require_once('db/temp.php');
	$conn    = connect($config);
	
			    /*   $filename = "SunHRM/batch/excelFile.xls";
			    //create the output
			    $output = //<table> code goes here. 
			    //set the header to treat this as an excel file
			    header("Content-Disposition: attachment; filename=\"$filename\"");
			    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			    header("Expires: 0");
			    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			    header("Cache-Control: private",false);
			 echo $output; */
?>


<!DOCTYPE html>
<html>
<head>
	<title>Demo</title>
</head>
	<body>
		<table border="1"> 
			<tr>
				<th>Srl No</th>
				<th>Employee_id</th>
				<th>Employee_name</th>
				<th>Joined_date</th>
				<th>Managers</th>
				<!--<th>Week Time</th>-->
				<th>Biometric details(Date,Actual working hours,State)</th>
			</tr>

			<?php
				$employeeDetails=getEmployeeInfoDemo($conn);
				//echo json_encode($employeeDetails);

				$index=0;
				$totalWeeks=0;
				
				foreach($employeeDetails as $d ){
					 

				$timeSheetWeekData=getEmloyeNotSubmitedData($conn,$d["employee_id"]);
			
					if($timeSheetWeekData!=null){
							$totalWeeks+=count($timeSheetWeekData);
							 echo "<tr>";
							 echo "<td>".++$index."</td>" ;
							 echo "<td>".$d['employee_id']."</td>" ;
							 echo "<td>".$d['emp_firstname']. " ".$d['emp_middle_name']." ".$d['emp_lastname']." (<span style='color:green; font-weight:bold;' > count ".count($timeSheetWeekData)."</span>)</td>" ; // 
							 echo "<td>".$d['joined_date']."</td>" ;

						$managerDetails=getEmployeemanager($conn,$d["emp_number"]);	 
						echo "<td>";
							foreach($managerDetails as $manger ){
								echo $manger["emp_firstname"]. " ".$manger['emp_middle_name']." ".$manger['emp_lastname'].",";
							}
						echo "</td>";
						
						
							echo "<td>";
							
							foreach($timeSheetWeekData as $weekdata ){
									
									$startDate= $weekdata["start_date"]." 00:00:00 ";
									$endDate= $weekdata["end_date"]." 23:59";
									
								$timesheetData=getTimeSheetDetailsByday($conn,$d["emp_number"],
									$startDate,$endDate);
								//echo json_encode($timesheetData);
								echo "<p style='border-top:1px solid black;'>";

                                echo "<p style='border-bottom:1px solid black;'>".$weekdata["start_date"]." to ".$weekdata["end_date"]." (".$weekdata["state"].") </p>";
								if($timesheetData!=null){
									echo "<table style='border-collapse: collapse;'>";
									foreach($timesheetData as $timesheet ){
										//echo "<p>".$timesheet["punch_in_user_time"];
										
										$cn='A';
										if(trim($timesheet["state"])===$cn){

											echo "<tr style='color:red;'> <td>".date('Y-m-d', strtotime($timesheet["punch_in_user_time"]))."</td>";
											echo "<td>".$timesheet["state"]."</td>";
											
										}else{
											

											$date_arr= explode(":", $timesheet['actual_working_hours']);
											if($date_arr[0] < 8 ){
												echo "<tr style='color:#ff8000; font-weight:bold;'> <td>".date('Y-m-d', strtotime($timesheet["punch_in_user_time"]))."</td>";
												echo "<td style='color:#ff8000; font-weight:bold;' >".$timesheet['actual_working_hours']."</td>";
												echo "<td font-weight:bold;' >".$timesheet["state"]."</td>";
											}else if($date_arr[0] >= 10 ){

												echo "<tr style='color:#0000ff; font-weight:bold;'> <td>".date('Y-m-d', strtotime($timesheet["punch_in_user_time"]))."</td>";
												echo "<td style='color:#0000ff; font-weight:bold;' >".$timesheet['actual_working_hours']."</td>";
												echo "<td font-weight:bold;' >".$timesheet["state"]."</td>";


											}else {
												echo "<tr> <td>".date('Y-m-d', strtotime($timesheet["punch_in_user_time"]))."</td>";
												echo "<td >".$timesheet['actual_working_hours']."</td>";
												echo "<td>".$timesheet["state"]."</td>";
											}
										}
										
										 echo "</tr>";
									}
									echo "</table>";
								}
							}

							echo "</td>";
							
						}
						echo "</tr>";
					}
					echo "<tr><td colspan='6' style='text-align:center; font-weight:bold;' > Total weeks not submitted : ".$totalWeeks."</td></tr>";
				
			?>

		</table>


	</body>
</html>


