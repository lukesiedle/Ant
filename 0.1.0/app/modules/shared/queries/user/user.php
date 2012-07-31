<?php

	namespace Ant {
		
		Class QueryUser extends Database {
			
			public function getUser( $_, $args ){
				$this->query
					-> select("user_username, user_id", $_."user user")
					-> orderBy("user.user_id DESC")
					-> limit(1);
				
				return $this;
			}
			
		}
		
	}

?>