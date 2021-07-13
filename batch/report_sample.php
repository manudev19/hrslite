<?php

	require_once('db/database.php');
	require_once('db/temp.php');
	$conn    = connect($config);

			/* $filename = "toBeApprovedTimeSheet.xls";
			    //create the output  and hs_hr_employee.employee_id=2134 
			    $output = //<table> code goes here. 
			    //set the header to treat this as an excel file
			    header("Content-Disposition: attachment; filename=\"$filename\"");
			    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			    header("Expires: 0");
			    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			    header("Cache-Control: private",false);
			    echo $output;  */
			    
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
				<th>Week Time</th>
				<!-- <th>Dates</th>
				<th>status</th> -->

			</tr>
			<?php
				$employeeDetails=AutomationFormate($conn);
				$index=0;

				if($employeeDetails!=null){

					foreach($employeeDetails as $d ){
						
					     $startDate= $d["start_date"]." 00:00:00";
						 $endDate= $d["end_date"]." 23:59";

						$timesheetData=getTimeSheetDetailsByday($conn,$d["emp_number"],
									$startDate,$endDate);

						if($timesheetData!=null){
						
								echo "<tr>";
								 	 echo "<td>".++$index."</td>" ;
									 echo "<td>".$d['employee_id']."</td>" ;
									 echo "<td>".$d['emp_firstname']. " ".$d['emp_middle_name']." ".$d['emp_lastname'].
									 		"</td>" ;
									 echo "<td>".$d["start_date"]." to ".
									 		$d["end_date"]."</td>";

									
								 echo "</tr>";

							/* foreach($timesheetData as $timesheet ){

								 echo "<tr>";
								 	 echo "<td>".++$index."</td>" ;
									 echo "<td>".$d['employee_id']."</td>" ;
									 echo "<td>".$d['emp_firstname']. " ".$d['emp_middle_name']." ".$d['emp_lastname'].
									 		"</td>" ;
									 echo "<td>".$d["start_date"]." to ".
									 		$d["end_date"]."</td>";

									 echo "<td>". date('Y-m-d',strtotime($timesheet["punch_in_user_time"]))."</td>";
									 echo "<td>".$timesheet["state"]."</td>";
								 echo "</tr>";
							} */
						}	
					
				}

				}else{
					echo "ther is no records in database";
				}
				
			?>


		</table>
	</body>
</html>
