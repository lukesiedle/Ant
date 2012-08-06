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
	
	include('classes/collection.php');
	
	
?>