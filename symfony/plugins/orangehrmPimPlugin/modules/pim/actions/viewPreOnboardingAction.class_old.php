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
class viewPreOnboardingAction extends sfAction {

    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    public function getExpenseService()
    {
        if(is_null($this->expenseService)){
            $this->expenseService = new ExpenseService();
        }
        return $this->expenseService;
    }
    public function getEmployeeService() {
      if(is_null( $this->EmployeeService))
      {
        $this->EmployeeService = new EmployeeService();

      }
      return $this->EmployeeService;
  }
    public function execute($request)
    {
       
        $userRoleManager = $this->getContext()->getUserRoleManager();
        $user = $userRoleManager->getUser();
        $this->candidateId = $request->getParameter(candidate_no);
        $this->employeeId = $this->getUser()->getEmployeeNumber();
        /**employee id who logged in */
    
        if ($this->candidateId!=NULL) {
              $this->form = new ViewPreOnboardingEmployeeForm(
                  array(), 
                  array('candidateId' =>  $this->candidateId)
              );
            $employeeService = new EmployeeService();
          
            $existingItems=$this->form->getEmployeeItems($this->candidateId);
      
            $this->employeeValuesArray= $existingItems;
            $totalrows = count($this->employeeValuesArray);
           
        }else{
            if($request->getParameter('btnView'))
            {
             $this->redirect('pim/viewpreonboardingemployeetable');
            }else{
              $this->form = new ViewPreOnboardingEmployeeForm(
                array(), 
                array('candidateId' =>  $this->candidateId)
            );
            $employeeService = new EmployeeService();

            }
        }
        $this->messageData = array($request->getParameter('messageData[0]'),$request->getParameter('messageData[1]'));
        if ($request->isMethod('post') ) {
            $candidateId= $request->getParameter(candidate_no);
                    if ($candidateId != null) {
                 $inputEmployeeItem = $request->getParameter('initialRows'); 
                 if($inputEmployeeItem['candidate_number']!=null) {
                  $WorkstationUI=$inputEmployeeItem['workstation'];
                  $CandidateNumberUI=$inputEmployeeItem['candidate_number'];
                  $ValidWorkstation=$this->validationWorkStation($WorkstationUI,$CandidateNumberUI);
                            
                             
                                if($ValidWorkstation==true){
                                 $this->getEmployeeService()->updatePreonboardEmployeeForManager(
                                    $inputEmployeeItem);
                                    $this->getUser()->setFlash('PreOnboarding.success', __(TopLevelMessages::SAVE_SUCCESS));
                                  $this->redirect('pim/viewpreonboardingemployeetable');
                               
                                 }else{
                                  $this->getUser()->setFlash('PreOnboarding.warning', __("Failed To Save: Work Station Number already exist"));
                                
                             }
                            }else{
                           
                            // $Id=array($this->employeeValuesArray[0]['candidate_number']);
                               $managerId=$inputEmployeeItem['reporting_manager']['empId'];
                               $managerName=$inputEmployeeItem['reporting_manager']['empName'];
                                $ValidManager=$this->validationManager($managerId,$managerName);
        
                               if($ValidManager==true)
                               {
                                  
                                $this->getEmployeeService()->updatePreonboardEmployeeForAdmin($this->employeeValuesArray[0]['candidate_number'],
                                   $inputEmployeeItem,$managerId);
                                   $this->getUser()->setFlash('PreOnboarding.success', __(TopLevelMessages::SAVE_SUCCESS));
                        $this->redirect('pim/viewpreonboardingemployeetable');
                               }
                               else{
                                 $this->getUser()->setFlash('PreOnboarding.warning', __("Failed To Save: Manager ID doesn't exist"));
                               
                            } }}
                           
                            else {
                           
                            $manager=$request->getParameter('reporting_manager');
                           $MyManagerId=$manager['empId'];
                           $MyManagerName=$manager['empName'];
                           $data = array(
                                'issuing_date'  => $request->getParameter('issuing_date'),
                                'joined_Date'  => $request->getParameter('joined_Date'),
                                'firstname'   => $request->getParameter('firstname'),
                                'middlename' => $request->getParameter('middlename'),
                                'lastname' =>$request->getParameter('lastname'),
                                'designation'     => $request->getParameter('designation'),
                                'department'  =>$request->getParameter('department'),
                                'reporting_manager' => $manager['empId'],
                            );
     
                   $ValidManager=$this->validationManager($MyManagerId,$MyManagerName);
     
                      if( $ValidManager==true)  
                      {
                        $this->getEmployeeService()->savePreonboardEmployee($data);
                        $this->getUser()->setFlash('PreOnboarding.success', __(TopLevelMessages::SAVE_SUCCESS));
                        $this->redirect('pim/viewMyPreOnboarding');
                       
                      }
                      else{
    
                        $this->getUser()->setFlash('PreOnboarding.warning', __("Failed To Save: Manager ID doesn't exist"));
                        $this->redirect('pim/viewMyPreOnboarding');
                      }
                     

                        }
                
                }

    
    
        
        
    }   
    public function validationManager($M_Id,$M_Name)
    {
      $ValidManagerStatus=$this->getEmployeeService()-> checkEmployeeStatus($M_Id);
 
      if($ValidManagerStatus==$M_Id)
      {
        $ValidManagerValid=$this->getEmployeeService()-> checkInvalidManagerName($M_Id);
    
        $valid1DB=preg_replace('# {2,}#', ' ', $ValidManagerValid);
     
        if(strcasecmp($valid1DB,$M_Name)==0)
        {
          return true;
        }
      
        else  
        {
          return false;
        }
        
      }
     

    }
    public function validationWorkStation($W_UI,$C_UI)
    {
    $workStationNumber= $this->getEmployeeService()->checkWorkstationNumber($W_UI);
    if(strcasecmp($workStationNumber[0]['candidate_number'],$C_UI)==0||$workStationNumber[0]['candidate_number']==null){
      if(strcasecmp($workStationNumber[0]['workstation'],$W_UI)==0||$workStationNumber[0]['workstation']==null)
      {
      return true;
    }
    else{
      return false;
    }

    
}else{
  if(strcasecmp($workStationNumber[0]['workstation'],$W_UI)!=0)
  {
    return true;
  }
  else
  {
    return false;
  }

}
}

}
