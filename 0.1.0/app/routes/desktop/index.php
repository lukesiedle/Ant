<?php
	
	/**
	 *	All the possible routes,
	 *	i.e. a whitelist.
	 *	
	 *	@since 0.2.1
	 */

	return array(
		
		// Setup //
		'/setup'				=> '\Route\Setup\index',
		
		// Home //
		
		'/'						=> '\Route\Home\index',
		
		'/home'					=> '\Route\Home\index',
		
		// User //
		
		'/user/register'		=> '\Route\User\register',
		'/user/login/:alpha'	=> '\Route\User\login',
		'/user/:alpha'			=> '\Route\User\task',
		
		// Resource //
		
		'/resource/:alpha'		=> '\Route\User\register',
	);