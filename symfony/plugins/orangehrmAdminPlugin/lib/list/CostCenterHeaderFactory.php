<?php

class CostCenterHeaderFactory extends ohrmListConfigurationFactory {
    protected function init() {

        $header1 = new ListHeader();

        $header1->populateFromArray(array(
            'name' => 'Cost Center',
            'elementType' => 'link',
            'filters' => array('I18nCellFilter' => array()
                              ),
            'elementProperty' => array(
                'labelGetter' => 'getName',
                'urlPattern' => 'javascript:'),
        ));

        $this->headers = array($header1);
    }

    public function getClassName() {
        return 'CostCenter'; // check this! should this be Cost Center or Nationality
    }
}

