<?php
include('biometricsReader/filereader.php');
include('db/database.php');
include('biometricsReader/attendance_functions.php');

$conn = connect($config);
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
$sourceBiometricsReader 	 	= '\\\stipl-srv-biom\TextOutPut/';
$destinationBiometricsReadFiles	= chmod('E:\biomertrics\processed/', 0755);
$openDirSource 					= opendir($sourceBiometricsReader);
$validExtension					= array('Dat');
$filesMoved						= 0;
while(($file = readdir($openDirSource)) !== false) {
	if ($file == '.' || $file == '..') { 
        continue; 
    }
	$file_date = basename($file, ".Dat");
	$file_created_date 	   = sprintf($file_date);
	if(pathinfo($file, PATHINFO_EXTENSION) == 'Dat') {
		$stringToDate = DateTime::createFromFormat("dmY", $file_created_date);
		$report_file_created_date = $stringToDate->format("d-m-Y");
		$system_date = Date('d-m-Y');
		if(strtotime($report_file_created_date) >= strtotime($system_date)) {
			echo "Report Has not been generated for ". $report_file_created_date . "<br>";
			continue;
		}
		$biometrics_file_process = processBiometricsReportFile($sourceBiometricsReader . $file , $conn);
		$fileType = pathinfo($file, PATHINFO_EXTENSION);
		if(in_array($fileType, $validExtension)) {
			if(!rename($sourceBiometricsReader.$file, "E:\biomertrics\processed/$file")) {
				echo "Failed to move file";
			} else {
				$filesMoved++;
			}
		}
	}
}
echo "<br>$filesMoved files were moved";
closedir($openDirSource);
?>