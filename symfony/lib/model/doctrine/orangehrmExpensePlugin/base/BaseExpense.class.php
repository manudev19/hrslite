<?php

/**
 * BaseTimesheet
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $timesheetId
 * @property integer $employeeId
 * @property string $state
 * @property date $startDate
 * @property date $endDate
 * @property Doctrine_Collection $TimesheetItem
 * @property Doctrine_Collection $TimesheetActionLog
 * 
 * @method integer             getTimesheetId()        Returns the current record's "timesheetId" value
 * @method integer             getEmployeeId()         Returns the current record's "employeeId" value
 * @method string              getState()              Returns the current record's "state" value
 * @method date                getStartDate()          Returns the current record's "startDate" value
 * @method date                getEndDate()            Returns the current record's "endDate" value
 * @method Doctrine_Collection getTimesheetItem()      Returns the current record's "TimesheetItem" collection
 * @method Doctrine_Collection getTimesheetActionLog() Returns the current record's "TimesheetActionLog" collection
 * @method Timesheet           setTimesheetId()        Sets the current record's "timesheetId" value
 * @method Timesheet           setEmployeeId()         Sets the current record's "employeeId" value
 * @method Timesheet           setState()              Sets the current record's "state" value
 * @method Timesheet           setStartDate()          Sets the current record's "startDate" value
 * @method Timesheet           setEndDate()            Sets the current record's "endDate" value
 * @method Timesheet           setTimesheetItem()      Sets the current record's "TimesheetItem" collection
 * @method Timesheet           setTimesheetActionLog() Sets the current record's "TimesheetActionLog" collection
 * 
 * @package    orangehrm
 * @subpackage model\time\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseExpense extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_expense');
        $this->hasColumn('expense_id as expenseId', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('expense_number as expenseNumber', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('expense_name as expenseName', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('employee_id as employeeId', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('state', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('customer_id as customerId', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('project_id as projectId', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
         $this->hasColumn('date as date', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
    }

 /*   public function setUp()
    {
        parent::setUp();
        $this->hasMany('TimesheetItem', array(
             'local' => 'timesheetId',
             'foreign' => 'timesheetId'));

        $this->hasMany('TimesheetActionLog', array(
             'local' => 'timesheetId',
             'foreign' => 'timesheet_id'));
    }*/
}