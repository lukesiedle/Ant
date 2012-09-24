<?php

	/*
	 *	Registration view
	 *	Passes CSRF token
	 *	and form action to
	 *	view.
	 * 
	 *	@since 0.1.0
	 *	
	 */

	namespace Ant\Web\User;

	function register(){
		
		// Resource for the registration form //
		$resource = 'resource/user';
		
		// Create a or get csrf token for this form's resource //
		$token = \Ant\Request :: CSRFtoken( $resource );
		
		// Pass to the template via collection //
		$register = new \Ant\Collection(array(
			'resource'	=> $resource,
			'token'		=> $token
		), 'register' );
		
		return new \Ant\CollectionSet( $register );
		
	}