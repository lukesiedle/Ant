<?php
	
	/*
	 *	Log the user in based 
	 *	on username/password combination
	 *	
	 *	This is different to authorization,
	 *	logging the user in via an OAuth
	 *	protocol like Facebook Connect	
	 *	
	 *	@since 0.1.0
	 */

	namespace Ant\Controller\User;
	
	use \Ant\Request as Request;
	use \Ant\Controller as Controller;
	use \Ant\Database as Database;
	use \Ant\Cookie as Cookie;
	use \Ant\Configuration as Config;
	use \Ant\User as User;
		
	function login( $vars ){
		
		if( ! $request = Request :: get('post') ){
			return false;
		}
		
		// Skip login if the user is already logged in //
		if( $user = User :: getCurrentUser() ){
			if( ! $user->isGuest() ){
				return false;
			}
		}
		
		$query	= Controller :: query('User.getUserByLogin', array(
			'username'	=> $request['username'],
			'password'	=> $request['password']
		));
		
		$collection = Database :: query( $query );		
		
		if( $collection->length() == 1 ){
			
			$data = $collection->first()->toArray();
			
			if( $request['remember_me'] ){
				Cookie :: set('Ant.User', array(
					'user_secret'	=> $data['user_secret']
				), Config :: get('login_cookie_age') );
			}
			
			// Set the current user in memory //
			User :: setCurrentUser( $data );
			
			return true;
		}
		
	}