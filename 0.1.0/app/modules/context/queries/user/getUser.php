<?php

	namespace Ant {
		
		Class QueryGetUser extends QueryUser {
			
			public function getUser( $query, String $_, Array $args = array() ){
				
				$query	-> select("user_username, user_id", $_."user user")
						-> join($_."user_permissions perm ON perm.user_id = user.user_id", "LEFT JOIN")
						-> orderBy("user_id DESC")
						-> limit(1);
				
				return $this;
			}
			
		} 
		
	}

?>