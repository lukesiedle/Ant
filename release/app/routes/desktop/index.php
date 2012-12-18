<?php
	
	/**
	 *	All the possible routes,
	 *	easy to read and identify,
	 *	also forms a "whitelist" of
	 *	acceptable URIs.
	 *	
	 *	@since 0.2.1
	 */

	return array(
		
		// Specific routing  //
		'/user/register'		=> '\Route\User\register',
		'/user/register/edit'	=> '\Route\User\editProfile',
		
		'/user/login/:string'	=> '\Route\User\login',
		'/user/:string'			=> '\Route\User\task',
		
		// Generic routing //
		'/'					=> '\Route\Main\index',
		'/home'				=> '\Route\Main\index'
		
	);