<?php

	/*
	 *	Get the basic user
	 *	query back, and modify
	 *	for login
	 * 
	 *	@package Ant
	 *	@subpackage User
	 *	@since 0.1.0
	 */
	 
	namespace Ant\Query\User {
		
		function getUserByLogin( $query, $_ , $args ){
			
			$query = \Ant\Controller :: query('User.getUser');
			
			$query->join($_.'user_password pass ON pass.user_id = user.user_id' );
			
			$query->where('pass.user_password = :password && user.user_username = :username');
			
			$salt = \Ant\Configuration :: get('login_salt');
			
			$query->bind(array(
				'password'	=> \Library\String\encodePassword( $args['password'] . $salt ),
				'username'	=> $args['username']
			));
			
			return $query;
		}
		
	}