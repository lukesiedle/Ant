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
	
	namespace View\Desktop\Channel\Ajax {
		
		use \Core\Router as Router;
		use \Core\Application as Application;
		use \Core\Template as Template;
		use \Core\CollectionSet as CollectionSet;
		use \Core\Controller as Controller;
		use \Core\Document as Document;
		
		function index( $request ){
			
			$output			= array();
			
			// Ajax channel is whitelisted //
			if ( Router :: getRouteVars()->allowAjax ){
				
				try {

					$view		= Router :: loadRouteView();

					if( $view instanceof CollectionSet ){
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
			
			Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}