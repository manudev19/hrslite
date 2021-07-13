<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

class ExpenseTypeForm extends BaseForm {
    
    private $expenseTypeConfigurationService;
    
    public function getExpenseTypeConfigurationService() {
        
        if (!($this->expenseTypeConfigurationService instanceof ExpenseTypeConfigurationService)) {
            $this->expenseTypeConfigurationService = new ExpenseTypeConfigurationService();
        }
        
        return $this->expenseTypeConfigurationService;
    }

    public function setExpenseTypeConfigurationService($expenseTypeConfigurationService) {
        $this->expenseTypeConfigurationService = $expenseTypeConfigurationService;
    }

    public function configure() {

        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText()
        ));

        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 100))
        ));

        $this->widgetSchema->setNameFormat('expenseType[%s]');

        $this->setDefault('id', '');
	}
    
    public function save() {
        
        $id = $this->getValue('id');
        
        if (empty($id)) {
            $expenseType = new ExpenseType();
            $message = array('messageType' => 'success', 'message' => __(TopLevelMessages::SAVE_SUCCESS));
        } else {
            $expenseType = $this->getExpenseTypeConfigurationService()->getExpenseType($id);
            $message = array('messageType' => 'success', 'message' => __(TopLevelMessages::UPDATE_SUCCESS));
        }
        
        $expenseType->setName($this->getValue('name'));
        //var_dump($this->getValue('name'));exit;
        $this->getExpenseTypeConfigurationService()->saveExpenseType($expenseType);        
        
        return $message;
        
    }

}
