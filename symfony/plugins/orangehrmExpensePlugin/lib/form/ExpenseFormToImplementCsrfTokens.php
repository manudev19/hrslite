<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TimesheetFormToImplementCsrfTokens
 *
 * @author orangehrm
 */
class ExpenseFormToImplementCsrfTokens extends sfForm {

    public function configure() {

        $this->setWidgets(array());

        $this->widgetSchema->setNameFormat('expense[%s]');
    }

}

?>
