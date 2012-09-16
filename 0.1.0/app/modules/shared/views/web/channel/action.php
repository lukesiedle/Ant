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
	
	namespace Ant\Web\Channel\Action {
		
		function index(){
			
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
			
			// Output JSON with header //
			echo json_encode(array(
				'data' => $results
			));
			
			\Ant\Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}