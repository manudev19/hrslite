<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Validate_model extends CI_Model 
{

    /**
     * REST Queries
     */

     public function getUserValidate($username,$password)
     {
        $sql =  "SELECT * FROM ohrm_user AS ou 
                LEFT JOIN hs_hr_employee AS emp
                ON ou.id = emp.employee_id 
                LEFT JOIN ohrm_login AS ol 
                ON ou.user_role_id = ol.id
                WHERE ou.user_name = '".$username."' AND user_password = '".$password."'";
        $query = $this->db->query($sql);
                
     }

}

/* End of file Validate_model.php */
