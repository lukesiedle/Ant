<?php

	/*
	 *	Ant Unit testing suit
	 *	
	 *	@since 0.1.0
	 *	@require PHPUnit
	 */
	
	require_once('PHPUnit/Autoload.php');
	
	error_reporting( E_ALL ^ E_NOTICE );
	
	define( 'PROJECT_ROOT', dirname(__DIR__) . '/0.1.0' );
	
	// Change this to your local public directory //
	define( 'PUBLIC_ROOT', 'http://127.0.0.1/lukesiedle/ant' );
	
	include('classes/collection.php');
	
	include('library/mysql.php');
	
	include('classes/database.php');
	
	// Application behaviour //
	include('application/routing.php');
	
	include('application/templating.php');