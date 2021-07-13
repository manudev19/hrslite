<?php

class CostCenterMappingHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();
//var_dump("he"); exit;
		$header1->populateFromArray(array(
		    'name' => 'Cost Center Name',
		    'width' => '33%',
		    'isSortable' => true,
		    'sortField' => 'name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'name'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'Location',
		    'width' => '33%',
		    'isSortable' => true,
		    //'sortField' => 'location',
		    'elementType' => 'link',
		    //'elementProperty' => array('getter' => 'location')
		    'elementProperty' => array(
			'labelGetter' => 'LocationName',
			'placeholderGetters' => array('id' => 'id'),
			'urlPattern' => 'saveCostCenterMapping?id={id}'),
		));

		$header3->populateFromArray(array(
		    'name' => 'Cost Center Admins',
		    'width' => '33%',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'employeeFullName'),
		));

		$this->headers = array($header1, $header2, $header3);
	}
	
	public function getClassName() {
		return 'CostCenterMapping';
	}

}

?>
