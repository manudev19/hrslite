<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Author : Mohan Basapuri
 * Purpose : To track log records in HRMS 
 */
class AuditLogService extends BaseService{
    private $AuditLog;

    public function getAuditLogDao() {

        if (is_null($this->AuditLog)) {
            $this->AuditLog = new AuditLogDao();
        }
        return $this->AuditLog;
    }

    public function getEmployeeDao() {

        if (is_null($this->AuditLog)) {
            $this->AuditLog = new EmployeeDao();
        }
        return $this->AuditLog;
    }

function auditLogQuery($module,$screenUrl,$screenName)
{
    $user = UserRoleManagerFactory::getUserRoleManager()->getUser();
    $employeeId=  $_SESSION['empNumber'];
    $employeeName=$_SESSION['fname'];
    $employeeRole= $user->getUserRole()->getName();
    $this->getAuditLogDao()->insertDMLData($employeeId,$employeeRole,$screenUrl,$screenName,$module,$employeeName);
}

public function getAffectedModules()
{
   return $this->getAuditLogDao()->getAffectedModules();
}

public function _getEmployeeId($empNumber)
{
    return $this->getAuditLogDao()->_getEmployeeId($empNumber);
}

public function checkEmployeeExist($empNumber)
{
    return $this->getAuditLogDao()->checkEmployeeExist($empNumber);
}
public function getEmployeeData($empNumber){
  return $this->getAuditLogDao()->getEmployee($empNumber);
}
    
public  function getSectionList()
{
 return $this->getAuditLogDao()->getSectionList();
} 


public function getAuditData($selectedModule,$selectedSection,$selectedAction,$selectedActionOwner,$selectedEmployee,$fromDate, $toDate,$limit, $offset)
{
  $result=$this->getAuditLogDao()->getAuditData($selectedModule,$selectedSection,$selectedAction,$selectedActionOwner,$selectedEmployee,$fromDate, $toDate,$limit, $offset);
   
// Inserting new column to result as 'updated fields'
$result1=[];
$result2=[];

foreach($result as $res)
{
    if($res['action']=='UPDATE') {

        $updatedData=$this->_getOnlyChangedFields($res['action_old_data'],$res['action_new_data']);
        $oldData = $this->_getOnlyOldFields($res['action_old_data'],$res['action_new_data']);
        
        $res['updated_data']=$updatedData;
        $res['old_data']=$oldData;
        
       }
       else{
        $res['updated_data']='Not Updated';
        $res['old_data']='Action is '.$res['action'];
       }
    
     $result2[]=$res;
}
 return $result2;
}

public function getSearchEmployeeCount($selectedModule,$selectedSection,$selectedAction,$selectedActionOwner,$selectedEmployee,$fromDate, $toDate) {
       return  $this->getAuditLogDao()->getSearchEmployeeCount($selectedModule,$selectedSection,$selectedAction,$selectedActionOwner,$selectedEmployee,$fromDate, $toDate);
}

/**
 * Written logic  to only get Updated fields
 */
public function _getOnlyChangedFields($old,$new){

    $oldValues= json_decode($old, true);
    $newValues= json_decode($new, true);
   
    $str1 = [];
    $str2 = [];
    $keys=[];
    $result=[];
   
    foreach($oldValues as $key=>$row){
        $keys[]=$key;
        $str1[] = json_encode($row==''?'null':$row);
    }
   
    foreach($newValues as $key=>$row){
        $keys[]=$key;
        $str2[] = json_encode($row==''?'null':$row);
    }
    foreach($keys as $key=>$row){
      foreach(array_diff_assoc($str2, $str1) as $str=>$data)
      { 
         if((string)$str==((string)$key)){
             $data=trim($data,'"');
             $result[]= __(ucfirst(strtolower($row))).' : '.$data;
         }
      }
    }
      return json_decode(json_encode($result),true);
}
   
 /**
 * Written logic  to only get Old fields Affected
 */  
public function _getOnlyOldFields($old,$new){
    $oldValues= json_decode($old, true);
    $newValues= json_decode($new, true);
   
    $str1 = [];
    $str2 = [];
    $keys=[];
    $result=[];
   
    foreach($oldValues as $key=>$row){
        $keys[]=$key;
        $str1[] = json_encode($row==''?'null':$row);
    }

    foreach($newValues as $key=>$row){
        $keys[]=$key;
        $str2[] = json_encode($row==''?'null':$row);
    }
    
    foreach($keys as $key=>$row)
    {  
      foreach(array_diff_assoc($str1, $str2) as $str=>$data)
      {
         if((string)$str==((string)$key)){
            $data=trim($data,'"');
           $result[]= __(ucfirst(strtolower($row))).' : '.$data;
         }
      }
   }
      return json_decode(json_encode($result),true);
}

   
   
public function getAuditDataDownload($selectedModule,$selectedSection,$selectedAction,$selectedActionOwner,$selectedEmployee,$fromDate, $toDate)
{
    $result=$this->getAuditLogDao()->getAuditDataDownload($selectedModule,$selectedSection,$selectedAction,$selectedActionOwner,$selectedEmployee,$fromDate, $toDate,$limit, $offset);
   
   $result1=[];
   $result2=[];

foreach($result as $res)
{
    if($res['action']=='UPDATE') {

        $updatedData=$this->_getOnlyChangedFields($res['action_old_data'],$res['action_new_data']);
        $oldData = $this->_getOnlyOldFields($res['action_old_data'],$res['action_new_data']);
        
        $res['updated_data']=$updatedData;
        $res['old_data']=$oldData;
         
       }
       else{
        $res['updated_data']='Not Updated';
        $res['old_data']='Action is '.$res['action'];
       }
    
     $result2[]=$res;
}
return $result2;
}

public function getAttachmentScreen($empNumber,$attachId) {        
    return $this->getAuditLogDao()->getAttachmentScreen($empNumber,$attachId);        
}
}
