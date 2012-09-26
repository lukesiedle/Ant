<?php

	/*
	 *	Very basic user query
	 *	allows for further
	 *	manipulation in the
	 *	different contexts
	 * 
	 *	@package Ant
	 *	@subpackage User
	 *	@since 0.1.0
	 */
	 
	namespace Ant\Query\User {
		
		function getUser( $query, $_ , $args ){
			$query
				-> select(
					"user.user_username
					,user.user_id
					,user.user_secret
					,user.user_first_name
					,user.user_last_name
					,user.user_email
					,CONCAT( user.user_first_name, ' ', user.user_last_name ) 
						AS user_full_name", 
					$_ . "user user")
				-> limit(1);
			
			return $query;
		}
		
	}