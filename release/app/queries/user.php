<?php

	namespace Query;
	
	use \Core\Database as DB;
	use \Core\Query as Query;
	
	class User {
		
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
		static function get(){
			
			$query = Query :: make()
				-> select(
					"user.user_username
					,user.user_id
					,user.user_secret
					,user.user_first_name
					,user.user_last_name
					,user.user_email
					,CONCAT( user.user_first_name, ' ', user.user_last_name ) 
						AS user_full_name", 
					DB :: _() . "user user")
				-> limit(1);
			
			return $query;
		}
		
		/**
		 *	Get the basic user
		 *	query back, and modify
		 *	for login
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
		function getByLogin(){
			
			$query = self :: get();
			
			$query->join( DB :: _() . 'user_password pass ON pass.user_id = user.user_id' );
			
			$query->where('pass.user_password = :password && user.user_email = :email');
			
			$salt = \Core\Configuration :: get('login_salt');
			
			$query->bind(array(
				'password'	=> \Library\String\encodePassword( $args['password'] . $salt ),
				'username'	=> $args['username']
			));
			
			return $query;
		}
		
	}