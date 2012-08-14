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
	
	namespace Ant;
	
	// Hostnames for the given client
	// @since 0.1.0 //
	
	$config['hosts']['web'][] = 'myappname.com';
	$config['hosts']['web'][] = 'localhost';
	$config['hosts']['web'][] = '127.0.0.1';
	
	$config['hosts']['mobile'][] = 'm.localhost';
	$config['hosts']['mobile'][] = 'm.myappname.com';
	
	$config['hosts']['mobile'][] = 'tablet.localhost';
	$config['hosts']['mobile'][] = 'tablet.myappname.com';

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