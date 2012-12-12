<?php
	
	/**
	 *	Global configuration that exists
	 *	across all clients.
	 * 
	 *	@package Core
	 *	@subpackage Configuration
	 *	@since 0.1.0
	 */
	namespace Core;
	
	// Context when none is specified in the request
	// @since 0.1.0 // 
	$config['default_context']['web']	= 'home';
	
	// Hostnames for local environment detection
	// @since 0.1.0 //
	$config['local_server_name'][]	= 'localhost';
	$config['local_server_name'][]	= 'm.localhost';
	$config['local_server_name'][]	= 'tablet.localhost';
	$config['local_server_name'][]	= 'api.localhost';
	$config['local_server_name'][]	= '127.0.0.1';
	
	// Hostnames for staging environment detection 
	// @since 0.1.0 //
	$config['staging_server_name'][] = 'staging.*';
	
	// Age for the login cookie to be set, 
	// @usage User.login
	// @since 0.1.0 //
	$config['login_cookie_age']		= date('U') + 60*60*24*90;
	
	// Salt for login password
	// @usage User.login
	// @since 0.1.0 //
	$config['login_salt']			= 'a12b7bb75e6fa7d1d178c2d2a37242e32a3d2e1aa95d66dd029335adf10d0a14';
	
	// Store the config //
	Configuration :: set( $config );
	
	// Custom Error Handler //
	