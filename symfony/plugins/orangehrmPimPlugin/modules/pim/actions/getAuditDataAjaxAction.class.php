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

/**
 * Description of getting AuditData for popup
 */
class getAuditDataAjaxAction  extends sfAction {

    protected $AuditLog;
    protected $entitlementService ;
    
    public function getAuditLogDao() {

        if (is_null($this->AuditLog)) {
            $this->AuditLog = new AuditLogDao();
        }
        return $this->AuditLog;
    }

    public function setAuditLogDao($AuditLog) {
        $this->AuditLog = $AuditLog;
    }
    
    protected function getAuditDataForId($parameters) {

        $value = $this->getAuditLogDao()->getAuditDataById( $parameters['id']);
  
        $old= json_decode($value[0]['action_old_data'],true);
        $new=json_decode($value[0]['action_new_data'],true);

        foreach($old as $key=>$row){
          $key1[]=$key;
          $str1[] = array(json_encode(ucfirst($key).' : '.($row==''?'null':$row)));
        }

        foreach($new as $key=>$row){
          $key2[]=$key;
          $str2[] = array(json_encode(ucfirst($key).' : '.($row==''?'null':$row)));
        }

        $action[]=$value[0]['action'];
        $data = array(
            'old' =>$str1,
            'new' =>$str2,
            'action' => $action,
         );
        return $data;
    }
    
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $data = $this->getAuditDataForId($request->getGetParameters());
        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        
        return $this->renderText(json_encode($data)) ; 
               
    }
}
