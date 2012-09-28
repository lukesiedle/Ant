<?php

	namespace Ant\Query\User {
		
		/**
		 *	Very basic user query
		 *	allows for further
		 *	manipulation in the
		 *	different contexts
		 * 
		 *	@param Query $query A new query object
		 *	@param string $_ The table prefix
		 *	@param array $args Arguments including 
		 *	'request'
		 *	
		 *	@package Ant
		 *	@subpackage User
		 *	@since 0.1.0
		 */
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