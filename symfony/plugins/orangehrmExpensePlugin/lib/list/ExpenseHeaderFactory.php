<?php

	class ExpenseHeaderFactory extends ohrmListConfigurationFactory{
		
		protected function init() {
			
			$header1 = new ListHeader();
			$header2 = new ListHeader();
			$header3 = new ListHeader();
	        $header4 = new ListHeader();
	        $header5 = new ListHeader();
	        $header6 = new ListHeader();
	        $header7 = new ListHeader();
			$header8 = new ListHeader();
	        $header9 = new ListHeader();
			$header11 = new ListHeader();
			
		$header1->populateFromArray(array(
		    'name' => 'Employee name',
		    'width' => '23%',
		    'isSortable' => false,
		    'sortField' => 'user_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getFullName'),
		    
		));

		$header9->populateFromArray(array(
			'name' => 'Employee Id',
			'width' => '8%',
			'isSortable' => false,
			'sortField' => 'user_id',
			'elementType' => 'label',
			'elementProperty' => array('getter' => 'getEmployee'),
			
		));

		$header6->populateFromArray(array(
		    'name' => 'Project name',
		    'width' => '22%',
		    'isSortable' => false,
		    'sortField' => 'user_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getProjectName'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'Status',
		    'width' => '10%',
		    'isSortable' => false,
		    'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'display_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getStatus'),
		    
		));

		 $header3->populateFromArray(array(
		    'name' => 'Submitted on',
		    'width' => '15%',
		    'isSortable' => false,
		  
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDate'),
		    
		));
                
        $header4->populateFromArray(array(
		    'name' => 'Amount',
		    'width' => '10%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'status',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getAmount'),
		    
		));
		$header5->populateFromArray(array(
		    'name' => 'Sl No',
		    'width' => '5%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'status',
		    'elementType' => 'label', 
		    //echo ++$index,
		    'elementProperty' => array('getter' => 'getCountDetails'),
		    
		));
		$header7->populateFromArray(array(
		    'name' => 'Expense ID',
		    'width' => '5%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'status',
		    'elementType' => 'label', 
		    //echo ++$index,
		    'elementProperty' => array('getter' => 'getExpenseNumber'),
		    
		));
		$header8->populateFromArray(array(
      			'name' => 'Report',
	      		'width' => '10%',
	      		'isSortable' => false,
            	'filters' => array('I18nCellFilter' => array()
                              ),
      // 'elementType' => 'label',
      // 'elementProperty' => array('getter' => 'getReportingManagerDetails'),

      			'elementType' => 'link',
     			'elementProperty' => array(
   				'labelGetter' => 'getLink',
   				'placeholderGetters' => array('id' => 'getExpenseId'),
   				'urlPattern' => 'viewExpenseDetail?expenseId={id}'),
  

      
  ));

		$header11->populateFromArray(array(
		    'name' => 'Department Name',
		    'width' => '19%',
		    'isSortable' => false,
		    'filters' => array('I18nCellFilter' => array()
                              ),
		    //'sortField' => false,
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDeptName'),
		    
		));

		$this->headers = array($header5, $header9, $header1,$header7,$header11,$header6,$header2,$header3,$header4,$header8);
	}

	public function getClassName() {
		return '';
	}
  }

?>