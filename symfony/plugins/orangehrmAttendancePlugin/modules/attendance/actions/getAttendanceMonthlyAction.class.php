<?php
class getAttendanceMonthlyAction extends sfAction 
{

    public function execute($request)
    {
        $this->form = new getAttendanceMonthlyForm();
    }
	
}

?> 
