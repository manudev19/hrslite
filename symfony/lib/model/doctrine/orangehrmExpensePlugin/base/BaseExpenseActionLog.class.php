<?php
	/**
	 * 
	 */
	abstract class BaseExpenseActionLog extends sfDoctrineRecord
	{
		
		public function setTableDefinition()
    	{
	        $this->setTableName('ohrm_expense_action_log');
	        $this->hasColumn('expense_action_log_id as expenseActionLogId', 'integer', null, array(
	             'type' => 'integer',
	             'primary' => true,
	             'autoincrement' => true,
	             ));
	        $this->hasColumn('expense_id as expenseId', 'integer', null, array(
	             'type' => 'integer',
	             ));
	        $this->hasColumn('performed_by as performedBy', 'string', 255, array(
	             'type' => 'string',
	             'length' => 255,
	             ));
	        $this->hasColumn('action as state', 'string', 255, array(
	             'type' => 'string',
	             'length' => 255,
	             ));
	        $this->hasColumn('comment', 'string', 255, array(
	             'type' => 'string',
	             'length' => 255,
	             ));
	        $this->hasColumn('date_time as dateTime', 'string', null, array(
	             'type' => 'string',
	             ));
    }

}


?>