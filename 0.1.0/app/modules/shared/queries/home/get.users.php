<?php

	namespace Ant\Queries\Home {
		
		use \Ant\Database as Database;
		
		function getUsers(){
			return Database :: query( function($query, $_ ){
				return $query 
					-> select("user_username, user_id", $_."user user")
					-> join($_."user_permissions perm ON perm.user_id = user.user_id", "LEFT JOIN")
					-> orderBy("user_id DESC")
					-> limit(1);
			}, false );
		}
		
	}


?>