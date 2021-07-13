<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewMyAttendanceRecordAction
 *
 * @author orangehrm
 */
class viewMyAttendanceRecordAction extends sfAction {
    
    
    public function execute($request) {

        $this->employeeId = $this->getContext()->getUser()->getEmployeeNumber();
        $this->month = $this->request->getParameter('month');
        $this->year  = $this->request->getParameter('year');
        $this->trigger = $request->getParameter('trigger');
        $this->actionRecorder="viewMy";
        $values = array('month' => $this->month, 'year' => $this->year, 'employeeId' => $this->employeeId, 'trigger' => $this->trigger);
        $this->form = new AttendanceRecordSearchForm(array(),$values);

        if (!($this->trigger)) {

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter("attendance"));
                if ($this->form->isValid()) {
                }
            }
        }
    }
    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {

            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
    }

    public function setAttendanceService(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }
}
?>
