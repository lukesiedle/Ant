<?php
	
	/**
	 *	The route is called and returns
	 *	a set of vars which help the 
	 *	app define it's current purpose.
	 * 
	 *	@since 0.2.1
	 */
	
	namespace Route\User;
	
	function register( $vars ){
		return array(
			'module'		=> 'register',
			'template'		=> 'registration/register',
			'frame'			=> 'frame'
		);
	}
	
	function login( $args ){
		switch( $args[1] ){
			case 'facebook' :
			case 'google' : 
			return array(
				'module'		=> 'index',
				'controllers'	=> 'User.auth',
				'template'		=> 'index',
				'frame'			=> 'frame',
				'auth_type'		=> $args[1]
			);
		}
		return false;
	}
	
	function task( $args ){
		$return;
		switch( $args[1] ){
			case 'logout' :
				$return['module'] = 'user';
				$return['controllers'] = 'User.logout';
				break;
			default :
			return false;
		}
		return $return;
	}
	