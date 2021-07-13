<?php

class deleteLeaveTypeAction extends baseLeaveAction {

    protected $leaveTypeService;
    private $AuditLogService;
    
    public function getAuditLogService() {

        if (is_null($this->AuditLogService)) {
            $this->AuditLogService = new AuditLogService();
        }
        return $this->AuditLogService;
    }
    /**
     * Executes deleteLeaveType action
     *
     * @param sfRequest $request A request object
     */
    public function execute($request) {
        $this->leaveTypePermissions = $this->getDataGroupPermissions('leave_types');

        if ($request->isMethod('post')) {

                if (count($request->getParameter('chkSelectRow')) == 0) {
                    $this->getUser()->setFlash('notice', __(TopLevelMessages::SELECT_RECORDS));
                } else {
                    if ($this->leaveTypePermissions->canDelete()) {
                        $form = new DefaultListForm();
                        $form->bind($request->getParameter($form->getName()));
                        if ($form->isValid()) {
                            $leaveTypeService = $this->getLeaveTypeService();

                            $leaveTypeIds = $request->getParameter('chkSelectRow');
                            $leaveTypeService->deleteLeaveType($leaveTypeIds);
                            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                        }
                    }
                }
                $this->getAuditLogService()->auditLogQuery('LEAVE','leaveTypeList','Leave Types');
                $this->redirect('leave/leaveTypeList');
            }
        }

    protected function getLeaveTypeService() {

        if (is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;
    }

}
