<?php
	
	/*
	 *	The Ajax channel
	 *	allows for Ajax requests
	 *	to be made to the same	
	 *	route, and outputs a 
	 *	JSON-encoded array.
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	
	namespace Ant\Web\Channel\Ajax {
		
		use \Ant\Router as Router;
		use \Ant\Application as Application;
		use \Ant\Template as Template;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Controller;
		
		function index( $request ){
			
			$output		= array();
			
			try {
			
				$view		= \Ant\Router :: loadRouteView();
				
				if( $view instanceof \Ant\CollectionSet ){
					$output = $view->toArray();
				}
			
			// Add the error output to JSON //
			} catch( Exception $e ){
				$output['error']['message'] = $e->getMessage();
				$output['error']['trace']	= $e->getTrace();
			}
			
			echo json_encode(array(
				'data' => $output
			));
			
			\Ant\Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}