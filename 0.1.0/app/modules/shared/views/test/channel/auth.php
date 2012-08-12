<?php
	
	/*
	 *	Test client channel 
	 * 
	 *	@package Ant
	 *	@type Test Suite
	 *	@since 0.1.0
	 */

	namespace Ant\Test\Channel\Auth {
		
		use \Ant\Authentication as Auth;
		use \Ant\Template as Template;
		
		function index( $request ){
			
			// Load a template from the shared space //
			$frame		= Template :: loadSharedTemplate('auth');
			
			// Check current authorization //
			Auth :: authFacebook();
			
			// Buffer the template for output //
			Template :: setBuffer( $frame );
			
			// Channel is always responsible for output //
			echo Template :: output();
			
		}
		
	}