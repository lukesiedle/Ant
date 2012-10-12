<?php
	
	/*
	 *	The error channel hosts
	 *	a different view of the 
	 *	same route for handling errors
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */

	namespace Ant\Desktop\Channel\Error {
		
		use \Core\Template as Template;
		use \Core\Application as App;
		function index( $request ){
			
			// Load a template from the shared space //
			$frame		= Template :: loadSharedTemplate('frame');
			
			Template :: loadSharedTemplate('error')->loadInto( $frame, '__CONTENT__' );
			
			// Buffer the template for output //
			Template :: setBuffer( $frame );
			
			App :: setHeaders();
			
			// Channel is always responsible for output //
			echo Template :: output();
		}
	
	}