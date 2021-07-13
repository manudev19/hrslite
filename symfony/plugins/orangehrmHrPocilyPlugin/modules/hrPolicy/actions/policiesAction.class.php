<?php
/*
* This is page is for saving and editing the expense
*/
class policiesAction extends baseTimeAction {


    /**
    * This is the entry function for the request that is hitting the servers
    */
    public function execute($request) {
        $this->setTemplate('policies');
    }

} // Execute Check
//} // End of Class
?>