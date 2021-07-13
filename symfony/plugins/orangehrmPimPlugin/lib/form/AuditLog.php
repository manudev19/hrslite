<?php

	class AuditLog {
		
		private $timestamp;
		private $actionOwnerId;
		private $actions;
		private $actionTableName;
        private $actionNewData;
        private $actionOldData;
        private $actionOwnerName;
        private $entityId;
        private $entity;
        private $actionAffectedOldData;
        private $actionAffectedNewData;
       
       	private $index=0; 

		public function getEmployeeService() {
	        if(is_null($this->employeeService)) {
	            $this->employeeService = new EmployeeService();
	            $this->employeeService->setEmployeeDao(new EmployeeDao());
	        }
        	return $this->employeeService;
    	}

		public function loadData($data,$index)
		{
			$this->index = $index;
			$this->timestamp = $data['action_timestamp'];
            $this->actionOwnerId = $data['action_owner_id'];
            $this->actionOwnerName = $data['action_owner_name'];
            $this->entityId = $data['entity_id'];
            $this->entity = $data['screen_name'];
			$this->actions = $data['action'];
            $this->actionTableName = $data['action_table_name']; 
            $this->actionAffectedOldData = $data['old_data'];
            $this->actionAffectedNewData = $data['updated_data'];
            $this->actionOldData = $data['action_old_data'];
            $this->actionNewData = $data['action_new_data'];
       }
     
       public function getTimeStamp(){
        return $this->timestamp;
       }

        public function getActionOwnerName(){
			return $this->actionOwnerName;
        }
        
		public function getActionOwnerId(){
			return $this->actionOwnerId;
		}

		public function getActions(){
			return $this->actions;
		}
        public function getEntityId(){
			return $this->entityId;
		}
		
        public function getEntity(){
			return $this->entity;
        }
        
		public function getActionTableName(){
			return $this->actionTableName;
		}

		public function getCountDetails(){
			return $this->index;
		}

		public function getActionOldData(){
           return $this->actionOldData;
        }
        public function getActionNewData(){
           return $this->actionNewData;
        }
        
        public function getActionAffectedNewData(){
            return $this->actionAffectedNewData;
         }

         public function getActionAffectedOldData(){
            return $this->actionAffectedOldData;
         }
        
	}
?>
