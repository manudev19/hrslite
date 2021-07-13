<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteProjectAction
 *
 * @author orangehrm
 */
class deleteCostCenterMappingAction extends baseAdminAction {

    private $projectService;

    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
            $this->projectService->setProjectDao(new ProjectDao());
        }
        return $this->projectService;
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {
        //var_dump('here');
        $form = new DefaultListForm();
        //var_dump('test');exit;
        $form->bind($request->getParameter($form->getName()));
        $toBeDeletedIds = $request->getParameter('chkSelectRow');
        //var_dump($toBeDeletedIds); exit;
        if ($form->isValid()) {
            $this->getProjectService()->deleteCostCenterMapping($toBeDeletedIds);
            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
        }
        $this->redirect('admin/viewCostCenterMapping');
    }

}

?>
