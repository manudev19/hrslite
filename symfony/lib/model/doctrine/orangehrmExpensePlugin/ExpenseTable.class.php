<?php 
/**
 * manohar
 */
class ExpenseTable extends PluginExpenseTable
{
	
	public static function getInstance()
	{
		return Doctrine_Core::getTable('Expense');
	}
}
?>