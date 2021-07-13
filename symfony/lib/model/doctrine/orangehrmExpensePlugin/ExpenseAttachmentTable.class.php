<?php 
/**
 * manohar
 */
class ExpenseAttachmentTable extends PluginExpenseAttachmentTable
{
	
	public static function getInstance()
	{
		return Doctrine_Core::getTable('ExpenseAttachment');
	}
}
?>