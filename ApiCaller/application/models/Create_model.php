<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Create_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function create_att_day_to_day()
	{
			$this->forge = $this->load->dbforge($this->load->database('apicalls',TRUE),TRUE);

		 $field_data = array();

        $field_data['id'] = array(
			'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE
        );

        $field_data['monthyear'] = array(
        	'type' => 'varchar',
        	'constraint' => 225,
        	'unsigned' => TRUE
        );

        $field_data['employee_id'] = array(
        	'type' => 'VARCHAR',
        	'constraint' => 255
        );

        $field_data['employee_name'] = array(
        	'type' => 'VARCHAR',
        	'constraint' => 255
        );

         $field_data['employee_department'] = array(
        	'type' => 'VARCHAR',
        	'constraint' => 255
        );

        //TODO : INSTEAD OF HARD CODING '31' GET DATE DIFFRENCE FROM 22-M-Y TO 22-(M+1)-Y
        
        for($i = 1; $i <= 31; $i++)
        {
			$coloumn_name = "DAY".$i;
            $field_data[$coloumn_name] = array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'default' => 'P'
			);
        }

		$this->forge->add_field($field_data);
        $this->forge->add_key('id',TRUE);
        $create = $this->forge->create_table("att_day_to_day",FALSE);
        return $create;
	}
}