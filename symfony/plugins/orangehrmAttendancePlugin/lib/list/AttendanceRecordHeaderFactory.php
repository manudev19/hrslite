<?php

class AttendanceRecordHeaderFactory extends ohrmListConfigurationFactory {

    protected function init() {

        $header1 = new RawLabelCellHeader();
        $header2 = new ListHeader();
        $header3 = new RawLabelCellHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new ListHeader();
        $header8 = new ListHeader();
        $header9 = new ListHeader();
        $header10 = new ListHeader();
        $header11 = new ListHeader();
        $header12 = new ListHeader();
        
        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '10%',
            'elementType' => 'rawLabel',
            'elementProperty' => array('getter' => 'getLoginDate'),
        ));
        
        $header2->populateFromArray(array(
            'name' => 'Shift',
            'width' => '10%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getShift'),
        ));
        
        $header3->populateFromArray(array(
            'name' => 'In Time',
            'width' => '15%',
            'elementType' => 'rawLabel',
            'elementProperty' => array('getter' => 'getPunchInUserTime'),
        ));
        
        $header4->populateFromArray(array(
            'name' => 'Out Time',
            'width' => '15%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getPunchOutUserTime'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Working Hours',
            'width' => '10%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getWorkingHours'),
        ));

        $header6->populateFromArray(array(
            'name' => 'Over Time',
            'width' => '10%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getOverTime'),
        ));

        $header7->populateFromArray(array(
            'name' => 'Break Time',
            'width' => '10%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getBreakTime'),
        ));

        $header8->populateFromArray(array(
            'name' => 'Actual Working Hours',
            'width' => '10%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getActualWorkingHours'),
        ));

        $header9->populateFromArray(array(
            'name' => 'Status',
            'width' => '10%',
            'elementType' => 'label',
            'elementProperty' => array('getter' => 'getStatus'),
        ));

        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6, $header7, $header8, $header9);
    }

    public function getClassName() {
        return 'AttendanceRecordList';
    }

}