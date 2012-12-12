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
		'/user/register'	=> '\Route\User\register',
		
		
		// Generic routing //
		'/'					=> '\Route\Main\index',
		'/:string'			=> '\Route\Main\index',
		'/:string/:number'	=> '\Route\Main\index',
		'/:string/:string'	=> '\Route\Main\index',
		
	);