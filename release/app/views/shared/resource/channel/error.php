<?php
	
	/*
	 *	The error channel hosts
	 *	a different view of the 
	 *	same route for handling errors
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */

	namespace View\Resource\Channel\Error {
		
		use \Core\Template as Template;
		use \Core\Application as App;
		use \Core\Document as Doc;
		
		function index( $request ){
			
			Doc :: addHeader( 'Content-type: Application/Json' );
			
			App :: setHeaders();
			
			echo json_encode(array(
				'success'	=> false,
				'errors'	=> \Core\Error :: getErrors()
			));
		}
	
	}