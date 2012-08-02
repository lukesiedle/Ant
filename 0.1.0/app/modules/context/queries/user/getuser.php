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
					"user_username
					,user_id
					,user_full_name", 
					$_."user user")
				-> limit(1);
			
			return $query;
		}
		
	}