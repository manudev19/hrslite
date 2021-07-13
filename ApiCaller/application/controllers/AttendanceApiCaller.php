<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//@TODO : CREATE TRAIT TO READ FILE THAT CONTAINS EXCLUDED EMPLOYEE DATA

trait TraitExcludeEmployees
{
    public function excludeEmployeesReader()
    {
        $file = new SplFileObject("E:\\wamp\\www\\ApiCaller\\application\\third_party\\excludeEmployees.txt");
        $excludeEmployeeArray = array();
        
        $counter = 0;
        
        while (!$file->eof()) 
        {
            $excludeEmployeeArray[$counter] = $file->current();
            $file->next();
            $counter++;
        }  

        return $excludeEmployeeArray;  
    }
}



class AttendanceApiCaller extends CI_Controller
{

    //@TODO : INCLUDE TRAIT FOR EXCLUDING EMPLOYEES
    use TraitExcludeEmployees; 

    private $date;
    private $attendanceResult;
    private $excludeEmployees = NULL;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('AttendanceApi');

        $this->load->model('select_model');
        $this->load->model('insert_model');
        $this->load->model('update_model');
    }

    public function getTimesheetData($dateParameter)
    {

        $this->date = $dateParameter;
        $this->attendanceResult = json_decode($this->attendanceapi->templateData($this->date),TRUE);
        if($this->attendanceResult['data'] == null)
        {
            header($_SERVER["SERVER_PROTOCOL"]." 409 Conflict") and die();
        }
        else
        {
            $this->setTimesheetData($this->attendanceResult['data']['template-data'],$this->date);
        }
    }

    private function setTimesheetData($result,$date)
    {
        $date = str_replace("-","/",$date);

        //@TODO : GET EXCLUDED EMPLOYEE LIST
        //@TODO : SEND THE LIST TO THE MODELS

        $getExcludedEmployees = str_replace("\r\n","",implode(",",$this->excludeEmployeesReader()));

        if($getExcludedEmployees != NULL )
        {
              $this->excludeEmployees = $getExcludedEmployees;
        }
        else
        {
            //Refactor : 30092019
            $this->excludeEmployees = NULL;
        }

        if($this->select_model->dateValidater($date))
        {
            $flag = $this->update_model->updateMatrixTimesheet($result);
            if($flag)
            {
                $updateAttendance= $this->update_model->updateHrmAttendance($date,$this->excludeEmployees);
                return  header($_SERVER["SERVER_PROTOCOL"]." 200 OK") and die("Updated into matrix database and hrm attendance");
            }

        }
        else
        {
           $return_flag = $this->insert_model->insertMatrixTimesheet($result);
            if($return_flag >= 0)
            {
                    $get_selected_data = $this->insert_model->insertHrmAttendance($date,$this->excludeEmployees);
                    return  header($_SERVER["SERVER_PROTOCOL"]." 200 OK") and die("Inserted into matrix database and hrm attendance");

            }
        }

    }

}
