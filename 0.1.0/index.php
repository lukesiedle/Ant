<?php
	
	/*
	 *	
	 *	All requests regardless
	 *	of client arrive here
	 * 
	 *	@package Ant
	 *  @require PHP 5.3+
	 *	@since 0.1.0
	 */	
	
	// Errors //
	error_reporting( E_ALL ^ E_NOTICE );
	// error_reporting( E_ALL ^ E_NOTICE ^ E_STRICT );
	
	// Version // 
	define( 'VERSION', '1.0' );
	
	// Application root //
	define( 'APPLICATION_ROOT', __DIR__ );
	
	// Document root //
	define( 'DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] );
	
	// Public root (http, https) //
	// @note Replace this with your own //
	define( 'PUBLIC_ROOT', '/lukesiedle/ant/' );
	
	// Library path //
	define('LIB_PATH', '../lib');
	
	// Initial requires //
	require( 'app/classes/shared/application.php' );
	require( 'app/classes/shared/configuration.php');
	
	use Ant\Application as App;
	
	// Perform basic initialization/configuration //
	App :: initialize();
	
	// Determine if environment is local/remote //
	App :: setEnvironment();
	
	// Determine the client based on variables, like request, headers //
	App :: setClient();
	
	// Set client specific settings //
	App :: setClientSettings();
	
	// Set the current language, based on user's session, cookies //
	App :: setLanguage();
	
	// Detect region from Ip Address, set the timezone and date //
	App :: setLocale();
	
	// Set useful paths to be used in the application //
	App :: setPaths();
	
	// Allocate the resources as required by the client and current context //
	App :: allocateResources();
	
	// Perform router tasks (set current channel, shared and contextual view)  //
	App :: route();
	
	// Set the headers, set within the current route //
	App :: setHeaders();
	
	// Generate output and release any resources //
	App :: flush();