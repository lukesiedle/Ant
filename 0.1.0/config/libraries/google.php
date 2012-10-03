<?php
	
	/*
	 *	Example configuration for Google API
	 * 
	 *	@package Ant
	 *	@subpackage Configuration
	 *	@type Global
	 *	@since 0.1.0
	 */
	 
	namespace Ant;
	
	$config['google_app']['api_key'] = "AIzaSyBwVUjpSU5Mcd8TEXmSPumG2SM1hXAyx9U";
	$config['google_app']['client_id'] = "241073679965.apps.googleusercontent.com";
	$config['google_app']['client_email'] = "241073679965@developer.gserviceaccount.com";
	$config['google_app']['client_secret'] = "9T6NiG032mKNmSunkKKP0BG5";
	
	// Scopes //
	$config['google_app']['scopes'][] = 'https://www.googleapis.com/auth/userinfo.profile';
	$config['google_app']['scopes'][] = 'https://www.googleapis.com/auth/userinfo.email';
	
	Configuration :: set( $config );