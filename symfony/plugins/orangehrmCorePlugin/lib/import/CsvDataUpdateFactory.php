<?php
class CsvDataUpdateFactory {
	
	public function getUpdateClassInstance($updateType){
		
		if($updateType == 'pim'){
			return new PimCsvDataUpdate();
		}
	}
}
?>