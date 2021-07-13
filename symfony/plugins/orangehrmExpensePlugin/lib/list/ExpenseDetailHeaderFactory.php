<?php

	class ExpenseDetailHeaderFactory extends ohrmListConfigurationFactory{
		
		protected function init() {
			
			$header1 = new ListHeader();
			$header2 = new ListHeader();
			$header3 = new ListHeader();
	        $header4 = new ListHeader();
	        $header5 = new ListHeader();
	        $header6 = new ListHeader();
	        $header7 = new ListHeader();
	        $header8 = new ListHeader();
		$header1->populateFromArray(array(
		    'name' => 'Date Of Transaction',
		    'width' => '20%',
		    'isSortable' => false,
		    'sortField' => 'date',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDateOfExpense'),
		    
		));
		$header6->populateFromArray(array(
		    'name' => 'Description',
		    'width' => '30%',
		    'isSortable' => false,
		    'sortField' => 'user_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getExpenseMessage'),
		    
		));
		
		$header2->populateFromArray(array(
		    'name' => 'Paid In Advance',
		    'width' => '10%',
		    'isSortable' => false,
		    'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'display_name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getExpensePaidBy'),
		    
		));

		 $header3->populateFromArray(array(
		    'name' => 'Expense Type',
		    'width' => '15%',
		    'isSortable' => false,
		  
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getExpenseType'),
		    
		));
                
        $header4->populateFromArray(array(
		    'name' => 'Amount',
		    'width' => '10%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'status',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getExpenseAmount'),
		    
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
		    'name' => 'Currency',
		    'width' => '10%',
		    'isSortable' => false,
            'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'status',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getCurrency'),
		    
		));
		
		$header8->populateFromArray(array(
      			'name' => 'Attachment',
	      		'width' => '10%',
	      		'isSortable' => false,
            	'filters' => array('I18nCellFilter' => array()
                              ),
  				'elementType' => 'link',
            	'elementProperty' => array(
                'labelGetter' => 'getLink',
                'placeholderGetters' => array('id' => 'getAttachmentId'),
                'urlPattern' => 'viewExpenseAttachment?attachId={id}'),

      
  ));


		$this->headers = array($header5,$header1,$header3,$header6,$header2,$header4,$header7,$header8);
	}

	public function getClassName() {
		return '';
	}
  }

?>