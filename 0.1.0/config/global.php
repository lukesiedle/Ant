<?php
	
	/*
	 *	Global configuration that exists
	 *	across all clients.
	 * 
	 *	@package Ant
	 *	@subpackage Configuration
	 *	@type Global
	 *	@since 0.1.0
	 * 
	 */		 
	
	Ant\Configuration :: set(array(
		
		// Context when none is specified in the request //
		// @since 0.1.0 // 
		
		'default_context'	=> array(
			'web'	=> 'home'
		),
		
		// Local hostnames for environment detection //
		// @since 0.1.0 //
		
		'local_server_name' => array(
			'localhost',
			'm.localhost',
			'tablet.localhost',
			'api.localhost',
			'127.0.0.1'
		)
		
	));