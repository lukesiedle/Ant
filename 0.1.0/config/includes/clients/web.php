<?php
	
	/*
	 *	Specific configuration for web client
	 *	only. "Web" is used to refer to a web
	 *	browser.
	 * 
	 *	@package Ant
	 *	@type Client
	 *	@since 0.1.0
	 * 
	 */	

	// Mysql Class and configuration
	// @since 0.1.0
	require('lib/mysql.class.php');
	require('config/mysql.php');
	
	// Facebook Connect PHP SDK and configuration
	// @since 0.1.0
	include( LIB_PATH . '/php/facebook/facebook.php' );
	include( 'config/libraries/facebook.php');
	
	// Google API Client + Plus API and configuration
	// @since 0.1.0
	include( LIB_PATH . '/php/google/apiClient.php' );
	include( LIB_PATH . '/php/google/contrib/apiPlusService.php' );
	include( 'config/libraries/google.php');