<?php
	
	/*
	 *	Global includes
	 * 
	 *	@package Ant
	 *	@type Global
	 *	@since 0.1.0
	 */

	// Most classes are autoloaded //
	
	// General Functions
	// @since 0.1.0 //
	require('lib/functions/array.php');
	require('lib/functions/string.php');
	
	// Underscore Library
	// @since 0.1.0 //
	require( LIB_PATH . '/php/underscore.php' );
	
	/**
	 *	Autoload application-specific
	 *	classes
	 *	
	 *	@since 0.1.0
	 */
	function __autoload( $class ){
		$opts = explode( '\\', $class );
		$file = strtolower( $opts[ count( $opts )-1 ] );
		$type = strtolower( $opts[0] );
		$root = 'app/';	
		
		switch( $type ){
			case 'core' : 
				$root .= 'classes/';
				break;
			case 'extension' :
				$root .= 'extensions/';	
			break;
			case 'model' :
				$root .= 'models/';	
			break;
			case 'controller' : 
				$root .= 'controllers/';
				break;
			case 'query' : 
				$root .= 'queries/';
				break;
			default : 
				$root .= $type . '/';
		}
		
		require_once( $root . '/' . $file . '.php' );
	}