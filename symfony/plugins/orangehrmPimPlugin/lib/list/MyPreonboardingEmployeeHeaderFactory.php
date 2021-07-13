<?php

	class MyPreonboardingEmployeeHeaderFactory extends ohrmListConfigurationFactory{
		
		protected function init() {
		
			$header1=new ListHeader();
			$header2 = new ListHeader();
			$header3 = new ListHeader();
	        $header4 = new ListHeader();
	        $header5 = new ListHeader();
	        $header6 = new ListHeader();
	        $header7 = new ListHeader();
	        $header8 = new ListHeader();
			$header9 = new ListHeader();
			$header10 = new ListHeader();
			$header11 = new ListHeader();
			$header12 = new ListHeader();
			
			$header1->populateFromArray(array(
				'name' => 'Candidate No',
				'width' => '7%',
				'isSortable' => false,
				'sortField' => 'status',
				'elementType' => 'label',
				'elementProperty' => array('getter' => 'getCandidateNumber'),
				
			));
			$header2->populateFromArray(array(
				'name' => 'Joining Date',
				'width' => '8%',
				'isSortable' => false,
				'filters' => array('I18nCellFilter' => array()),
				'elementType' => 'label',
				'elementProperty' => array('getter' => 'getJoiningDate'),
				
			));
		$header3->populateFromArray(array(
		    'name' => 'Employee Name',
		    'width' => '16%',
		    'isSortable' => false,
		    'sortField' => 'user_name',
			'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getFullName'),
		    
		));
		
		 $header4->populateFromArray(array(
		    'name' => 'Designation',
		    'width' => '15%',
		    'isSortable' => false,
			'filters' => array('I18nCellFilter' => array()),
			'sortField' => 'user_name',
			'elementType' => 'label',
		   'elementProperty' => array('getter' => 'getDesignation'),
		    
		));
                
        $header5->populateFromArray(array(
		    'name' => 'Department',
		    'width' => '15%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()),
		    'sortField' => 'user_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDepartment'),
		    
		));
	
		$header6->populateFromArray(array(
		    'name' => 'Reporting Manager ',
		    'width' => '16%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()),
		    'sortField' => 'user_name',
			'elementType' => 'label', 
			'elementProperty' => array('getter' => 'getManagerName'),
		    
		));
		$header7->populateFromArray(array(
		    'name' => 'Work Station No',
		    'width' => '8%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()),
		    'sortField' => 'status',
			'elementType' => 'label',
			 'elementProperty' => array('getter' => 'getWorkstation'),
		    
		));
		$header8->populateFromArray(array(
		    'name' => 'Dedicated',
		    'width' => '7%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()),
		    'sortField' => 'user_name',
			'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDedicated'),
		    
		));
		$header9->populateFromArray(array(
		    'name' => 'International',
		    'width' => '7%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()),
		    'sortField' => 'user_name',
			'elementType' => 'label', 
		   'elementProperty' => array('getter' => 'getInternational'),
		    
		));
		$header10->populateFromArray(array(
		    'name' => 'Location',
		    'width' => '4%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()),
		    'sortField' => 'user_name',
			'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getLocation'),
		    
		));
	   $header11->populateFromArray(array(
         'name' => 'Edit',
         'width' => '4%',
         'isSortable' => false,
        'filters' => array('I18nCellFilter' => array()),
       'elementType' => 'label',
       'elementType' => 'link',
       'elementProperty' => array(
      'labelGetter' => 'getEditLinkForAdmin',
      'placeholderGetters' => array('id' => 'getCandidateNumber'),
       'urlPattern' => 'viewMyPreOnboarding?candidate_no={id}'),
  ));
  $header12->populateFromArray(array(
	'name' => 'Edit',
	'width' => '5%',
	'isSortable' => false,
	'filters' => array('I18nCellFilter' => array()),
    'elementType' => 'label',
    'elementType' => 'link',
    'elementProperty' => array(
    'labelGetter' => 'getEditLinkForManager',
    'placeholderGetters' => array('id' => 'getCandidateNumber'),
    'urlPattern' => 'viewMyPreOnboarding?candidate_no={id}'),
));

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == "Yes") 
{
	$this->headers = array($header1,$header2,$header3,$header4,$header5,$header6,$header7,$header11);
}
else{
	$this->headers = array($header1,$header2,$header3,$header4,$header5,$header7,$header8,$header9,$header10,$header12);
	
}
		
}

	public function getClassName() {
		return '';
	}
  }

?>