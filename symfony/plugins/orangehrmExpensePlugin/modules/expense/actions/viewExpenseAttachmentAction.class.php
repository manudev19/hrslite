<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewCandidateAttachmentAction
 *
 * @author orangehrm
 */
class viewExpenseAttachmentAction extends sfAction {

    /**
     *
     * @return <type> 
     */
     public function getExpenseService() {

       if (is_null($this->expenseService)) {
            $this->expenseService = new ExpenseService();
        }

        return $this->expenseService;
    }
    
    /**
     *
 

    /**
     *
     * @param <type> $request
     * @return <type> 
     */
    public function execute($request) {
// echo "<pre>";
// var_dump($request); exit;
        // this should probably be kept in session?
        $attachId = $request->getParameter('attachId');
        // echo "<pre>";
        // var_dump("$attachId"); exit;
        $expenseService = $this->getExpenseService();
        $attachment = $expenseService->getExpenseAttachmentById($attachId);

        $response = $this->getResponse();

        if (!empty($attachment)) {
            $contents = $attachment->getFileContent();
            $contentType = $attachment->getFileType();
            $fileName = $attachment->getFileName();
            $fileLength = $attachment->getFileSize();

            $response->setHttpHeader('Pragma', 'public');

            $response->setHttpHeader('Expires', '0');
            $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
            $response->setHttpHeader("Cache-Control", "private", false);
            $response->setHttpHeader("Content-Type", $contentType);
            $response->setHttpHeader("Content-Disposition", 'attachment; filename="' . $fileName . '";');
            $response->setHttpHeader("Content-Transfer-Encoding", "binary");
            $response->setHttpHeader("Content-Length", $fileLength);

            $response->setContent($contents);
            $response->send();
        } else {
            $response->setStatusCode(404, 'This attachment does not exist');
        }

        return sfView::NONE;
    }

}

