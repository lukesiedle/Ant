<?php
	
	/**
	 *	Context : user
	 *	@since 0.2.1
	 */
	
	namespace Route\User;
	
	function register( $vars ){
		
		return array(
			'module'		=> 'register',
			'template'		=> 'registration/register'
		);
	}
	
	function editProfile( $vars ){
		
		return array(
			'module'		=> 'editprofile',
			'template'		=> 'registration/register'
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
				
				// Apply a module //
				$return['module'] = 'user';
				
				// Apply a controller //
				$return['controllers'] = 'User.logout';
				break;
			default :
			return false;
		}
		return $return;
	}
	