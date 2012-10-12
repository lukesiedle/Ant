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
			
			$output			= array();
			
			// Ajax channel is whitelisted //
			if ( Router :: getRouteVars()->allowAjax ){
				
				try {

					$view		= \Ant\Router :: loadRouteView();

					if( $view instanceof \Ant\CollectionSet ){
						$output['data'] = $view->toArray();
					}

				// Add the error output to JSON //
				} catch( Exception $e ){
					$output['error']['message'] = $e->getMessage();
					$output['error']['trace']	= $e->getTrace();
				}
			
			} else {
				$output['error']['message'] = '404. Page not found.';
				\Ant\Document :: addHeader('HTTP/1.0 404 Not Found');
			}
			
			echo json_encode( $output );
			
			\Ant\Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}