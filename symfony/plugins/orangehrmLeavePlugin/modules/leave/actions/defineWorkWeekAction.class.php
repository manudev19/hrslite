<?php

class defineWorkWeekAction extends baseCoreLeaveAction {
    private $AuditLogService;
    
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    public function execute($request) {

        //Keep Menu in Leave/Config 
        $request->setParameter('initialActionName', 'defineWorkWeek');

        $this->workWeekPermissions = $this->getDataGroupPermissions('work_week');

        $workWeek = $this->getWorkWeekService()->getWorkWeekOfOperationalCountry(null);

        if (empty($workWeek)) {
            $workWeek = new WorkWeek();
        }

        $this->workWeekForm = new WorkWeekForm(array('workWeekEntity' => $workWeek, 'workWeekPermissions' => $this->workWeekPermissions));

        if ($request->isMethod(sfRequest::POST)) {

            $this->workWeekForm->bind($request->getParameter($this->workWeekForm->getName()));
            if ($this->workWeekPermissions->canUpdate()) {
                if ($this->workWeekForm->isValid()) {
                    try {

                        $workWeek->setMon($this->workWeekForm->getValue('day_length_Monday'));
                        $workWeek->setTue($this->workWeekForm->getValue('day_length_Tuesday'));
                        $workWeek->setWed($this->workWeekForm->getValue('day_length_Wednesday'));
                        $workWeek->setThu($this->workWeekForm->getValue('day_length_Thursday'));
                        $workWeek->setFri($this->workWeekForm->getValue('day_length_Friday'));
                        $workWeek->setSat($this->workWeekForm->getValue('day_length_Saturday'));
                        $workWeek->setSun($this->workWeekForm->getValue('day_length_Sunday'));

                        $this->getWorkWeekService()->saveWorkWeek($workWeek);
                        $this->getAuditLogService()->auditLogQuery('LEAVE','defineWorkWeek','Work Week');
                        $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS), false);
                    } catch (Exception $e) {
                        $this->getUser()->setFlash('failure', __(TopLevelMessages::SAVE_FAILURE), false);
                    }
                } else {
                    $this->getUser()->setFlash('failure', __(TopLevelMessages::SAVE_FAILURE), false);
                }
            }
        }
    }

}
