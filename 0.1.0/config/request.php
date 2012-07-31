<?php
	
	/*
	 *	Request configuration to handle
	 *	client detection and routing.
	 * 
	 *	@package Ant
	 *	@subpackage Configuration
	 *	@type Shared
	 *	@since 0.1.0
	 * 
	 */	
	 
	Ant\Configuration :: set(array(
		
		// These hostnames will make their key the client
		// e.g. "web" will be the client when hostname is "localhost"
		// @since 0.1.0 //
		'hosts'		=> array(
			'web'	=> array(
				'myappname.com',
				'localhost',
				'127.0.0.1',
			),
			'mobile' => array(
				'm.localhost',
				'm.myappname.com'
			),
			'tablet' => array(
				'tablet.localhost',
				'tablet.myappname.com'
			),
			'api'	=> array(
				'api.localhost',
				'api.myappname.com'
			)
		),
		
		// These are overriding request variables to set the client
		// e.g. "api" will be set as the client during a request like 
		// http://myappname.com/api/getUser?id=4
		// 
		// @note 
		// Use channel and client in the right contexts. 
		// Channelling allows the same route to be made 
		// but using a different means of output, like JSON.
		// Client is for a different arrangement of data altogether.
		// 
		// @since 0.1.0 //
		
		'clients'	=> array(
			'api'			=> 'api',
			'plugin'		=> 'plugin'
		)
		
	));