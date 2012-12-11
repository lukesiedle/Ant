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
	
	namespace View\Web\Channel\Action {
		
		function index(){
			
			// Check if the channel is allowed //
			if( \Ant\Router :: getRouteVars()->allowAction ){
				
				// Load view specific data
				// @since 0.1.0 //
				$view		= \Ant\Router :: loadRouteView();

				// Run any specified controllers, chaining them
				// where possible
				// @since 0.1.0 //
				if( $controllers = \Ant\Router :: getControllers() ){
					$args = array();
					foreach( $controllers as $controller ){

						// Catch errors to output to browser cleanly //
						try {
							$result = \Ant\Controller :: call( $controller, $args );
						} catch( Exception $e ){
							$results['errors'][ $controller ] = $e->getMessage();
						}
						
						// Chain the arguments //
						if( is_array( $result )){
							$args = $result;
						} else {
							$args = array( $controller => $result );
							$results[$controller] = $result;
						}
					}
				}
				
				$output['data'] = $results;
			} else {
				
				// Not allowed to be on this channel //
				\Ant\Document :: addHeader('HTTP/1.0 404 Not Found');
				$output['error']['message'] = '404 Not Found.';
			}
			
			// Output JSON with header //
			echo json_encode( $output );
			\Ant\Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}