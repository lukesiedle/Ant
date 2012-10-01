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
	require('app/classes/shared/collection.php');
	
	// Query Class
	// @since 0.1.0 //
	require('app/classes/shared/query.php');
	
	// Database Class
	// @since 0.1.0 //
	require('app/classes/shared/database.php');
	
	// Resource Class
	// @since 0.1.0 //
	require('app/classes/shared/resource.php');
	
	// Request Class
	// @since 0.1.0 //
	require('app/classes/shared/request.php');
	
	// Router Class
	// @since 0.1.0 //
	require('app/classes/shared/router.php');
	
	// Controller Class
	// @since 0.1.0 //
	require('app/classes/shared/controller.php');
	
	// Store Class
	// @since 0.1.0 //
	require('app/classes/shared/store.php');
	
	// Template Class
	// @since 0.1.0 //
	require('app/classes/shared/template.php');
	
	// Session Class
	// @since 0.1.0 //
	require('app/classes/shared/session.php');
	
	// Cookie Class
	// @since 0.1.0 //
	require('app/classes/shared/cookie.php');
	
	// Document Class
	// @since 0.1.0 //
	require('app/classes/shared/document.php');
	
	// Authentication
	// @since 0.1.0 //
	require('app/classes/shared/authentication.php');
	
	// Exception
	// @since 0.1.0 //
	require('app/classes/shared/exception.php');
	
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
		require_once('app/classes/context/' . $file . '.php' );
	}