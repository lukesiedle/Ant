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
	
	function register( $args ){
		return false;
	}
	
	/*
	 *	Filter the request
	 * 
	 */
	
	function registerFilter( $request ){
		return array(
			'user_first_name'	=> 'Luke',
			'user_last_name'	=> 'Siedle',
			'user_email'		=> 'ljsiedle@gmail.com',
		);
	}
	