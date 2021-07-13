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

/*
 * Author : Mohan Basapuri
 * Purpose : To track log records in HRMS 
 */
class AuditLogDao
{

  /**
   * Get screen for given module and action
   * 
   * @param string $module Module Name
   * @param string $actionUrl Action
   * @return Screen object or FALSE if not found
   */


  public function insertDMLData($loginId, $loginRole, $screenUrl, $screenName, $module, $employeeName)
  {
    $loginId = $this->getEmployeeId($loginId);
    try {
      $query = "UPDATE `sunhrm_audit_trail` SET `action_owner`= '$loginRole', `action_owner_id`='$loginId',`action_owner_name`='$employeeName', `screen_name`='$screenName',`screen_url`='$screenUrl', `action_module`='$module' where action_owner is  null AND  action_table_name IS NOT NULL";
      $pdo = Doctrine_Manager::connection()->getDbh();
      $pdo->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

      $prepareQuery = $pdo->prepare($query);
      $prepareQuery->execute();
    } catch (Exception $e) {
      throw new DaoException($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function  checkEmployeeExist($data)
  {
    $q1 = "SELECT DISTINCT(emp_number) FROM `hs_hr_employee`
    WHERE `emp_number` = '" . $data . "' AND termination_id IS Null";
    $pdo = Doctrine_Manager::connection()->getDbh();
    $prepareQuery = $pdo->prepare($q1);
    $prepareQuery->execute();
    $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $res) {
      return $res['emp_number'];
    }
  }

  public function  getEmployeeId($empNumber)
  {
    try {
      $q = "SELECT employee_id from hs_hr_employee  where emp_number='$empNumber'";

      $pdo = Doctrine_Manager::connection()->getDbh();
      $prepareQuery = $pdo->prepare($q);
      $prepareQuery->execute();
      $result = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
      $prepareQuery->closeCursor();
      foreach ($result as $res) {
        return $res['employee_id'];
      }
    } catch (Exception $e) {
      throw new DaoException($e->getMessage());
    }
  }


  public function getAffectedModules()
  {
    try {
      $query = Doctrine_Query::create()->select('a.moduleName')
        ->from('AuditLogModule a')
        ->orderBy('a.moduleName');
      return $query->execute();
    } catch (Exception $e) {
      throw new DaoException($e->getMessage(), $e->getCode(), $e);
    }
  }

  
  public function getSectionList($orderField = 'menu_title', $orderBy = 'ASC')
  {
    try {

      $q = "SELECT `menu_title`,s.action_url AS screen_url FROM `sunhrm_audit_modules`as a left join `ohrm_screen` as s ON
          s.module_id = a.id left join  `ohrm_menu_item` as m ON m.screen_id=s.id
           WHERE  parent_id is not null ";

      if ($orderField) {
        $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
        $q .= " ORDER BY {$orderField} {$orderBy}";
      }

      $pdo = Doctrine_Manager::connection()->getDbh();
      $pdo->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
      $sectionList = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);

      return $sectionList;
      // @codeCoverageIgnoreStart
    } catch (Exception $e) {
      throw new DaoException($e->getMessage(), $e->getCode(), $e);
    }
    // @codeCoverageIgnoreEnd
  }

  public function getAuditDataById($param)
  {
    try {
      $q = "SELECT `action_old_data`, `action_new_data`,`action`  FROM `sunhrm_audit_trail` WHERE audit_id='$param'";

      $pdo = Doctrine_Manager::connection()->getDbh();
      $pdo->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);
      $result = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);

      return $result;

      // @codeCoverageIgnoreStart
    } catch (Exception $e) {
      throw new DaoException($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function getAuditData($selectedModule, $selectedSection, $selectedAction, $selectedActionOwner, $selectedEmployee, $fromDate, $toDate, $limit, $offset, $orderField = 'action_timestamp', $orderBy = 'ASC')
  {
    $query = '';
    $bindParams = array();
    $conditions = array();

    $filters = array('modules' => __($selectedModule), 'sections' => __($selectedSection), 'actions' => __($selectedAction), 'action_owner' => __($selectedActionOwner), 'affected_employee' => __($selectedEmployee), 'from_date' => __($fromDate), 'to_date' => __($toDate));
    try {
      $query = "SELECT `audit_id`,`action_timestamp` , `action_owner`, `action_owner_id`, `action`,`entity_id`,     
              `action_module`, `screen_name`, `action_table_name`, `action_old_data`, `action_new_data`,action_owner_name,entity_name
               FROM sunhrm.sunhrm_audit_trail AS s ";

      foreach ($filters as $searchField => $searchBy) {
        if (!empty($searchField) && !empty($searchBy)) {
          if ($searchField == 'from_date') {
            $conditions[] = 's.action_timestamp>=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'to_date') {
            $conditions[] = 's.action_timestamp<=? ';
            $bindParams[] = $searchBy;
          }
          if ($searchField == 'modules') {
            $conditions[] = 's.action_module=? ';
            $bindParams[] = strtolower($searchBy);
          } elseif ($searchField == 'sections') {
            $conditions[] = 's.screen_name=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'actions') {
            $conditions[] = 's.action=? ';
            $bindParams[] = strtoupper($searchBy);
          } elseif ($searchField == 'action_owner') {
            $conditions[] = 's.action_owner_id=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'affected_employee') {
            $conditions[] = 's.entity_id=? ';
            $bindParams[] = $searchBy;
          }
        }
      }


      $numConditions = 0;

      foreach ($conditions as $condition) {
        $numConditions++;

        if ($numConditions == 1) {
          $query .= ' WHERE ' . $condition;
        } else {
          $query .= ' AND ' . $condition;
        }
      }

      $query .= ' GROUP BY s.audit_id ';
      if ($orderField) {
        $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
        $query .= " ORDER BY {$orderField} {$orderBy}";
      }
      if (!is_null($offset) && !is_null($limit)) {
        $query .= ' LIMIT ' . $offset . ', ' . $limit;
      }
      $conn = Doctrine_Manager::connection();
      $state = $conn->prepare($query);
      $state->execute($bindParams);
      $results = $state->fetchAll(PDO::FETCH_ASSOC);

      return $results;
    } catch (Exception  $e) {
      throw new  DaoException($e->getMessage(), $e->getCode(), $e);
    }
  }

  public function getSearchEmployeeCount($selectedModule, $selectedSection, $selectedAction, $selectedActionOwner, $selectedEmployee, $fromDate, $toDate, $orderField = 'action_timestamp', $orderBy = 'ASC')
  {
    $countQuery = '';
    $bindParams = array();
    $conditions = array();

    $filters = array('modules' => __($selectedModule), 'sections' => __($selectedSection), 'actions' => __($selectedAction), 'action_owner' => __($selectedActionOwner), 'affected_employee' => __($selectedEmployee), 'from_date' => __($fromDate), 'to_date' => __($toDate));
    try {
      $countQuery = "SELECT COUNT(*) FROM `sunhrm_audit_trail` AS s ";
      foreach ($filters as $searchField => $searchBy) {
        if (!empty($searchField) && !empty($searchBy)) {
          if ($searchField == 'from_date') {
            $conditions[] = 's.action_timestamp>=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'to_date') {
            $conditions[] = 's.action_timestamp<=? ';
            $bindParams[] = $searchBy;
          }
          if ($searchField == 'modules') {
            $conditions[] = 's.action_module=? ';
            $bindParams[] = strtolower($searchBy);
          } elseif ($searchField == 'sections') {
            $conditions[] = 's.screen_name=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'actions') {
            $conditions[] = 's.action=? ';
            $bindParams[] = strtoupper($searchBy);
          } elseif ($searchField == 'action_owner') {
            $conditions[] = 's.action_owner_id=? ';

            $bindParams[] = $searchBy;
          } elseif ($searchField == 'affected_employee') {
            $conditions[] = 's.entity_id=? ';

            $bindParams[] = $searchBy;
          }
        }
      }



      $numConditions = 0;
    
      foreach ($conditions as $condition) {
        $numConditions++;

        if ($numConditions == 1) {
          $countQuery .= ' WHERE ' . $condition;
        } else {
          $countQuery .= ' AND ' . $condition;
        }
      }
      if ($orderField) {
        $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
        $countQuery .= " ORDER BY {$orderField} {$orderBy}";
      }

      $conn = Doctrine_Manager::connection();
      $statement = $conn->prepare($countQuery);
      $result = $statement->execute($bindParams);

      $count = 0;
      if ($result) {
        if ($statement->rowCount() > 0) {
          $count = $statement->fetchColumn();
        }
      }

      return $count;
    } catch (Exception  $e) {
      throw new  DaoException($e->getMessage(), $e->getCode(), $e);
    }
  }
  public function getAuditDataDownload($selectedModule, $selectedSection, $selectedAction, $selectedActionOwner, $selectedEmployee, $fromDate, $toDate, $orderField = 'action_timestamp', $orderBy = 'ASC')
  {
    $query = '';
    $bindParams = array();
    $conditions = array();

    $filters = array('modules' => __($selectedModule), 'sections' => __($selectedSection), 'actions' => __($selectedAction), 'action_owner' => __($selectedActionOwner), 'affected_employee' => __($selectedEmployee), 'from_date' => __($fromDate), 'to_date' => __($toDate));
    try {
      $query = "SELECT `audit_id`,`action_timestamp` , `action_owner`, `action_owner_id`, `action`,`entity_id`,     
                `action_module`, `screen_name`, `action_table_name`, `action_old_data`, `action_new_data`,action_owner_name,entity_name
             FROM sunhrm.sunhrm_audit_trail AS s ";

      foreach ($filters as $searchField => $searchBy) {
        if (!empty($searchField) && !empty($searchBy)) {
          if ($searchField == 'from_date') {
            $conditions[] = 's.action_timestamp>=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'to_date') {
            $conditions[] = 's.action_timestamp<=? ';
            $bindParams[] = $searchBy;
          }
          if ($searchField == 'modules') {
            $conditions[] = 's.action_module=? ';
            $bindParams[] = strtolower($searchBy);
          } elseif ($searchField == 'sections') {
            $conditions[] = 's.screen_name=? ';
            $bindParams[] = $searchBy;
          } elseif ($searchField == 'actions') {
            $conditions[] = 's.action=? ';
            $bindParams[] = strtoupper($searchBy);
          } elseif ($searchField == 'action_owner') {
            $conditions[] = 's.action_owner_id=? ';

            $bindParams[] = $searchBy;
          } elseif ($searchField == 'affected_employee') {
            $conditions[] = 's.entity_id=? ';

            $bindParams[] = $searchBy;
          }
        }
      }


      $numConditions = 0;

      foreach ($conditions as $condition) {
        $numConditions++;

        if ($numConditions == 1) {
          $query .= ' WHERE ' . $condition;
        } else {
          $query .= ' AND ' . $condition;
        }
      }

      $query .= ' GROUP BY s.audit_id ';
      if ($orderField) {
        $orderBy = (strcasecmp($orderBy, 'DESC') == 0) ? 'DESC' : 'ASC';
        $query .= " ORDER BY {$orderField} {$orderBy}";
      }

      $conn = Doctrine_Manager::connection();
      $state = $conn->prepare($query);
      $state->execute($bindParams);
      $results = $state->fetchAll(PDO::FETCH_ASSOC);
  
      return $results;
    } catch (Exception  $e) {
      throw new  DaoException($e->getMessage(), $e->getCode(), $e);
    }
  }
  public function  getAttachmentScreen($empNumber, $attachId)
  {
    try {
      $q = Doctrine_Query::create()->select('screen')->from('EmployeeAttachment')
        ->where('emp_number = ?', $empNumber);

      if (is_array($attachId) && count($attachId) > 0) {
        $q->whereIn('attach_id', $attachId);
      }
      
      $var = $q->execute();
      foreach ($var as $v) {
        return $v['screen'];
      }


      // @codeCoverageIgnoreStart
    } catch (Exception $e) {
      throw new DaoException($e->getMessage(), $e->getCode(), $e);
    }
    // @codeCoverageIgnoreEnd      

  }
}
