<?php

class expenseActions extends sfActions {

    private $timesheetService;

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }


    public function executeAddRow($request) {
      // var_dump("here: executeAddRow ");exit;
      /*  $i = 0;
      $values = array('i' => $i);*/

      $form = new ApplyExpenseForm();
      $form->addRow($request->getParameter("num"));
        // var_dump("here");exit;
      return $this->renderPartial('addRow', array('form' => $form, 'num' => $request->getParameter("num")));
  }

  public function executeDeleteRows(sfWebRequest $request) {
    // $form = new DefaultListForm();
    // var_dump("here in executeDeleteRows");exit;
    $expenseItemId = $request->getParameter("expenseItemId");
    $ExpenseService = new ExpenseService();
        $this->state = $ExpenseService->deleteExpenseItems($expenseItemId);

}

public function executeGetProjectLinkAjax(sfWebRequest $request) 
{
    $clientId = $request->getParameter('clientId');
        // var_dump($clientId);exit;
    $expenseDao = new ExpenseDao();
    $this->activityList = $expenseDao->getProjectAjax($clientId);
        // var_dump($x);
        // var_dump($x);exit;          
        // return "sucess";
}

/*The is an ajax call from demoSuccessPage
To remove an attachment
*/
public function executeDeleteAttachment(sfWebRequest $request){

  $expenseItemId = $request->getParameter('expenseItemId');
  $ExpenseService = new ExpenseService();
  $this->state = $ExpenseService->removeExpenseItemsAttachment($expenseItemId);
  
}


}