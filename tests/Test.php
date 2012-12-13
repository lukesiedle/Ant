<?php

	/*
	 *	Ant Unit testing suit
	 *	
	 *	@since 0.1.0
	 *	@require PHPUnit
	 */
	
	require_once('PHPUnit/Autoload.php');
	
	error_reporting( 
		E_ERROR | 
			E_WARNING | 
				E_RECOVERABLE_ERROR 
	);
	
	define( 'PROJECT_ROOT', dirname(__DIR__) . '/release' );
	
	// Change this to your local public directory //
	define( 'PUBLIC_ROOT', 'http://127.0.0.1/lukesiedle/ant' );
	
	// include('classes/collection.php');
	
	// include('library/mysql.php');
	
	// include('classes/database.php');
	
	// include('classes/configuration.php');
	
	// include('classes/session.php');
	
	include('classes/resource.php');
	
	// Application behaviour //
	// include('application/routing.php');
	
	// include('application/templating.php');