<?php

class getAttendanceMonthlyForm extends sfForm 
{
    public $formWidgets = array();
    public $subunit;

    public function getCompanyStructureService() {

        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    private function _setSubunitWidget() 
    {

        $subUnitList = array(0 => __("All"));
        $treeObject = $this->getCompanyStructureService()->getSubunitTreeObject();
        $tree = $treeObject->fetchTree();
        foreach ($tree as $node) {
            if ($node->getId() != 1) {
                $subUnitList[$node->getId()] = str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'];
            }
        }
      return $subUnitList;
    }

    public function configure() 
    {

        $this->subunit = $this->_setSubunitWidget();

        $this->setWidgets(
            [
                'from_date' => new ohrmWidgetDatePicker(array(), array('id' => 'from_date', 'class' => 'from_date')),
                'to_date' => new ohrmWidgetDatePicker(array(), array('id' => 'to_date', 'class' => 'to_date')),
                'department' => new sfWidgetFormChoice(array('choices' => $this->subunit),array('id' => 'department','class' => 'department'))
            ] 
        );
    }
}


?> 
