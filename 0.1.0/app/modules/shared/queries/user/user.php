<?php

	namespace Ant\Query\User {
		
		function getUser( $query, $_, $args ){
			$query
				-> select("user_username, user_id", $_."user user")
				-> orderBy("user.user_id DESC")
				-> limit(1);
			
			return $query;
		}
		
	}