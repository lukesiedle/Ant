<?php
	
	/*
	 *	The error channel hosts
	 *	a different view of the 
	 *	same route for handling errors
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */

	namespace View\Test\Channel\Error {
		
		use \Core\Template as Template;
		use \Core\Application as App;
		
		function index( $request ){
			
			App :: setHeaders();
			
			echo 'An error occurred.';
		}
	
	}