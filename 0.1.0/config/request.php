<?php
	
	/**
	 *	Request configuration to handle
	 *	client detection and routing.
	 * 
	 *	@package Core
	 *	@subpackage Configuration
	 *	@since 0.1.0	 
	 */	
	
	namespace Core;
	
	// Hostnames for the given client
	// @since 0.1.0 //
	
	$config['hosts']['desktop'][] = 'myappname.com';
	$config['hosts']['desktop'][] = 'localhost';
	$config['hosts']['desktop'][] = '127.0.0.1';
	
	$config['hosts']['mobi'][] = 'm.localhost';
	$config['hosts']['mobi'][] = 'm.myappname.com';
	
	$config['hosts']['tablet'][] = 'tablet.localhost';
	$config['hosts']['tablet'][] = 'tablet.myappname.com';

	$config['hosts']['api'][] = 'api.localhost';
	$config['hosts']['api'][] = 'api.myappname.com';
	
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
	$config['clients']['api']		= 'api';
	$config['clients']['plugin']	= 'plugin';
	
	// For unit testing //
	$config['clients']['test']		= 'test';
	
	// Apply //
	Configuration :: set( $config );