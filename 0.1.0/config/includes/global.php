<?php
	
	/*
	 *	Global includes
	 * 
	 *	@package Ant
	 *	@type Global
	 *	@since 0.1.0
	 */

	// Collection Class 
	// @since 0.1.0 //
	require('app/classes/core/collection.php');
	
	// Query Class
	// @since 0.1.0 //
	require('app/classes/core/query.php');
	
	// Database Class
	// @since 0.1.0 //
	require('app/classes/core/database.php');
	
	// Resource Class
	// @since 0.1.0 //
	require('app/classes/core/data.php');
	
	// Resource Class
	// @since 0.1.0 //
	require('app/classes/core/resource.php');
	
	// Request Class
	// @since 0.1.0 //
	require('app/classes/core/request.php');
	
	// Router Class
	// @since 0.1.0 //
	require('app/classes/core/router.php');
	
	// Controller Class
	// @since 0.1.0 //
	require('app/classes/core/controller.php');
	
	// Model Class
	// @since 0.1.0 //
	require('app/classes/core/model.php');
	
	// Template Class
	// @since 0.1.0 //
	require('app/classes/core/template.php');
	
	// Session Class
	// @since 0.1.0 //
	require('app/classes/core/session.php');
	
	// Cookie Class
	// @since 0.1.0 //
	require('app/classes/core/cookie.php');
	
	// Document Class
	// @since 0.1.0 //
	require('app/classes/core/document.php');
	
	// Exception
	// @since 0.1.0 //
	require('app/classes/core/exception.php');
	
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
			case 'extension' :
				$root .= 'extensions/';	
			break;
			default : 
				$root .= 'classes/' . $type;
		}
		
		require_once( $root . '/' . $file . '.php' );
	}