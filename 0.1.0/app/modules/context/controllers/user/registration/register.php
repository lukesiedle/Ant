<?php

	/*
	 *	Registers the user
	 *	based on request data
	 * 
	 *	@since 0.1.0
	 *	
	 */

	namespace Ant\Controller\User\Registration;
	
	use \Ant\User as User;
	
	function register(){
		
		if( ! $request = \Ant\Request :: get('post') ){
			return false;
		}
		
		$user = new User( new Collection( $request, 'user' ) );
		
		$user->save();
		
		User :: setCurrentUser( $user );
		
		return $user;
	}