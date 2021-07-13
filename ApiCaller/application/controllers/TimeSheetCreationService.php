<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
define('KEY_TIMESHEET_PERIOD_AND_START_DATE', 'timesheet_period_and_start_date');

class TimeSheetCreationService extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('select_model', 'select');
    }
    /**
     * Creation of Time frame for all the weeks 
     */
    public function createWeeklyTimeSheet()
    { 
        /* CurrentToday's Date */
        $currentDate = date('Y-m-d'); 
         /* Last week timesheet*/
        $previousWeekDate = date("Y-m-d", strtotime("last Sunday"));
        /* Getting time frame of dates creation */
        $datesInTheCurrenTimesheetPeriod = $this->getTheCurrentTimeSheetPeriod();
        /* Date creation for timesheet */
        $calculatedPeriodForTimesheetCreation = $this->calculateDaysInTheTimesheetPeriod($currentDate, $datesInTheCurrenTimesheetPeriod);

        /* Find the recent joiners from lastweek to todays date*/
        $recently_joined_employees = $this->select->get_recent_joiners($previousWeekDate, $currentDate);

        if (count($recently_joined_employees) > 0) {
            echo "Recently joined employee to the organization between " . $previousWeekDate . " and " . $currentDate . " are " . count($recently_joined_employees) . "<br/>";
            $count = 0;
            foreach ($recently_joined_employees as $employees) {
                $newEmployeeTimePeriodTimeSheetData = $this->calculateDaysInTheTimesheetPeriod($employees->joined_date, $datesInTheCurrenTimesheetPeriod);
                $newEmployeeStartDate = $newEmployeeTimePeriodTimeSheetData[0];
                $newEmployeeEndDate = end($newEmployeeTimePeriodTimeSheetData);
                $newEmployeeData = $this->getEmployeeTimeSheetDetailsOnStartDate($employees->emp_number, $newEmployeeStartDate, $newEmployeeEndDate);
                if ($newEmployeeData == null) {
                    try {
                        /* Create Timesheet for newly joined employees*/
                        $insert_id = $this->createTimesheet($newEmployeeStartDate, $newEmployeeEndDate, $employees->emp_number);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                    if (!$insert_id) {
                        $count++;
                    }
                }
            }
            echo "Created timesheet for newly joined employees for the period of " . $newEmployeeStartDate . "  and " . $newEmployeeEndDate . " for " . $count . " employees <br/>";
        }

        /* 
         * For all the active employes we are creating timesheet 
         * */
        $active_Employees = $this->select->get_active_employees();
        if (count($active_Employees) > 0) {
            echo "Active Employees are " . count($active_Employees) . "<br/>";
            $counter = 0;
            foreach ($active_Employees as $employees) {
                $employeeStartDate = $calculatedPeriodForTimesheetCreation[0];
                $employeeEndDate = end($calculatedPeriodForTimesheetCreation);
                $isTimesheetPresent = $this->getEmployeeTimeSheetDetailsOnStartDate($employees->emp_number, $employeeStartDate, $employeeEndDate);
                if ($isTimesheetPresent == null) {
                    try {
                        /* Creating Timesheet for All Active Employees */
                        $insert_id = $this->createTimesheet($employeeStartDate, $employeeEndDate, $employees->emp_number);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }

                    if (!$insert_id) {
                        $counter++;
                    }
                }
            }
            echo "Created timesheet for active employees for the period of " . $employeeStartDate . "  and " . $employeeEndDate . " for " . $counter . " employees <br/>";
        }
        try {
            /* Updating the Unique Id after creation of Timesheet */
            $updatedUniqueId = $this->select->updateUniqueId();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function createTimesheet($sdate, $edate, $emp_id)
    {
        return $this->select->insertTimeSheet($sdate, $edate, $emp_id);
    }

    public function getEmployeeTimeSheetDetailsOnStartDate($empId, $startdate, $endDate)
    {
        return $this->select->getTimeSheetValueWithEmpAndDate($empId, $startdate, $endDate);
    }

    public function getTheCurrentTimeSheetPeriod()
    {
        return $this->select->getConfigCurrentTimeSheet(KEY_TIMESHEET_PERIOD_AND_START_DATE);
    }

    /**
     * Calculation of Timeframe
     */
    public function calculateDaysInTheTimesheetPeriod($currentDate, $xml)
    {
        $this->startDate = $xml->StartDate;
        $day = date('N', strtotime($currentDate));

        $diff = $this->startDate - $day;
        if ($diff > 0) {
            $diff -= 7;
        }

        $sign = ($diff < 0) ? "" : "+";

        $r = mktime('0', '0', '0', date('m', strtotime("{$sign}{$diff} day", strtotime($currentDate))), date('d', strtotime("{$sign}{$diff} day", strtotime($currentDate))), date('Y', strtotime("{$sign}{$diff} day", strtotime($currentDate))));

        for ($i = 0; $i < 7; $i++) {
            $dates[$i] = date("Y-m-d", strtotime("+" . $i . " day", $r));
        }

        return $dates;
    }
}