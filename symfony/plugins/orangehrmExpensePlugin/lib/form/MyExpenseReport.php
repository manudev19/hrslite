<?php 
/**
 * my expense report object class
 */
class MyExpenseReport 
{
	
	public function loadData($data,$index)
	{
		// var_dump($data);
		$this->index = $index;
		$this->expenseId = $data['expense_id'];
		$this->customerId = $data['customer_id'];
		$this->projectId = $data['project_id'];
		$this->status = $data['state'];
		$this->date = $data['date'];
		$this->amount = $data['amount'];
		$this->expenseNumber = $data['expenseNumber'];
		$this->linkName = View;
		$this->editLinkName = Edit;

		// var_dump($this->status);

	}
	public function getEditLink() {
		if (!empty($this->expenseId) && $this->status != 'APPROVED' && $this->status != 'PROCESSED') {
			$this->editLinkName = __("Edit");
			return $this->editLinkName;
		}else{
			return null;
		}
	}

	public function getProjectName()
	{
		return $this->projectId;
	}
	public function getCustomerName()
	{
		return $this->customerId;
	}
	public function getExpenseNumber()
	{
		return $this->expenseNumber;
	}
	public function getDate()
	{
		return $this->date;
	} 
	public function getAmount()
	{
		return $this->amount;
	}
	public function getCountDetails(){
		return $this->index;
	}
	public function getStatus()
	{
		
		return $this->status;
	}	
	public function getLink() {
		if (!empty($this->expenseId)) {
			$this->linkName = __("View");
		}
		return $this->linkName;
	}
	public function getExpenseId()
	{
		return $this->expenseId;
	}

}





?>