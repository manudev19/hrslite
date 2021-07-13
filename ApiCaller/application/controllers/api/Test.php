
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Test extends \Restserver\Libraries\REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('select_model');
       // $this->methods['getUserData']['limit'] = 500;

    }

   public function empData_get($id=null)
   {
        if($id)
        {
            $resultArray = $this->select_model->getUserOnEmpId($id);
            if($resultArray)
            {
                $this->response($resultArray,\Restserver\Libraries\REST_Controller::HTTP_OK);
            }
        }
        else
        {
            $resultArray = $this->select_model->getUserData();
            if($resultArray)
            {
                $this->response($resultArray,\Restserver\Libraries\REST_Controller::HTTP_OK);
            }
        }
        
   }

   public function userValidate_get($username,$password)
   {
        $resultObject = $this->validate_model->getUserValidate($username,$password);
   }

}

/* End of file Test.php */
