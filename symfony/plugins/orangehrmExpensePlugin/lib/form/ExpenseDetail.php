<?php
	/**
	 * 
	 */
	class ExpenseDetail
	{

	private $DateOfExpense;
	private $currency;
	private $ExpenseType;
	private $ExpenseMessage;
	private $ExpensePaidBy;
	private $ExpenseAmount;
	private $index = 1;
	private $AttachmentId;
    private $linkName;
    //private $attachment;
	public function loadData($data,$index)
	{ 
		// var_dump($data);
		$this->index = $index;
		
		$this->DateOfExpense = $data['date_of_expense'];
		$this->ExpenseType	= $data['expense_type'];
		$this->ExpenseMessage	= $data['message'];
		$this->ExpensePaidBy = $data['paid_by_company'];
		$this->ExpenseAmount = $data['amount'];
		$this->AttachmentId = $data['id'];
		$this->currency	= $data['currency'];
		$this->noAttachment = $data['noAttachment'];
		//$this->attachment = $data['file_name'];
		//var_dump($this->attachment);exit;		


	}

	public function getFileName()
	{
		return $this->attachment;
	}
	public function getDateOfExpense()
	{
		return $this->DateOfExpense;
	}
	
	public function getExpenseType()
	{
		return $this->ExpenseType;
	}
	public function getExpenseMessage()
	{
		return $this->ExpenseMessage;
	}
	public function getExpensePaidBy()
	{
		
		if($this->ExpensePaidBy == '2'){

			return YES;
		}
		else{
			
			return NO;
		}
		return $this->ExpensePaidBy;
	}
	public function getCountDetails(){
			return $this->index;
		}
	public function getExpenseAmount()
	{
		return $this->ExpenseAmount;
	}

	 public function getAttachmentId() {
        return $this->AttachmentId;
    }
    public function getCurrency() {
        return $this->currency;
    }
	public function getLink() {
        if (!empty($this->AttachmentId)) {
            $this->linkName = __("Download");
        }
         return $this->linkName;
      }
     public function getNoAttachment()
     {		//var_dump($this->noAttachment); exit;
     	if ($this->noAttachment != 1) {
     		return __("No");
     	}else{
     		return __("Yes");
     	}
     }
}



?>



<!-- public function getAttachmentId() {
        return $this->attachmentId;
    }

    public function getLink() {
        if (!empty($this->attachmentId)) {
            $this->linkName = __("Download");
        }
        return $this->linkName;
    } -->
