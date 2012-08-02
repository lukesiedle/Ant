<?php
	
	/*
	 *	The authentication channel
	 *	is a specific view acting 
	 *	as a gateway after authentication
	 *	completes. Specifically, it 
	 *	allows a JavaScript redirect
	 *	to take place to clean the URL.
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */

	namespace Ant\Web\Channel\Auth {
		
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