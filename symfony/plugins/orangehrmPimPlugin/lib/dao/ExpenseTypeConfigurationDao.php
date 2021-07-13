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
 */
class ExpenseTypeConfigurationDao extends BaseDao {

    public function saveExpenseType(ExpenseType $expenseType) {
        
        try {
            $expenseType->save();
            return $expenseType;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        
    }
    
    public function getExpenseType($id) {
        
        try {
            return Doctrine::getTable('ExpenseType')->find($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        
    }
    
    public function getExpenseTypeByName($name) {
        
        try {
            
            $q = Doctrine_Query::create()
                                ->from('ExpenseType')
                                ->where('name = ?', trim($name));
            
            return $q->fetchOne();
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        
    }    
    
    public function getExpenseTypeList() {
        
        try {
            
            $q = Doctrine_Query::create()
                                ->from('ExpenseType')
                                ->orderBy('name');
            
            return $q->execute(); 
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
        
    }
    
    public function deleteExpenseType($toDeleteIds) {
        
        try {
            
            $q = Doctrine_Query::create()->delete('ExpenseType')
                            ->whereIn('id', $toDeleteIds);

            return $q->execute();            
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
        
    }
    
    public function isExistingExpenseTypeName($expenseTypeName) {
        
        try {
            
            $q = Doctrine_Query:: create()->from('ExpenseType rm')
                            ->where('rm.name = ?', trim($expenseTypeName));

            if ($q->count() > 0) {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }       
        
    }


    
    public function isReasonInUse($idArray) {
        
        $q = Doctrine_Query::create()->from('Employee em')
                                     ->leftJoin('em.EmployeeTerminationRecord et')
                                     ->leftJoin('et.TerminationReason tr')
                                     ->whereIn('tr.id', $idArray);        
        
        $result = $q->fetchOne();
        
        if ($result instanceof Employee) {
            return true;
        }
        
        return false;
        
    }

}