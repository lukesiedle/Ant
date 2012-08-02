<?php
	
	/*
	 *	The Ajax channel
	 *	allows for Ajax requests
	 *	to be made to the same	
	 *	route.
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	
	namespace Ant\Web\Channel\Ajax {
		
		function index( $request ){
			
			$view		= \Ant\Router :: loadRouteView();
			
			$output		= array();
			
			if( $view instanceof \Ant\CollectionSet ){
				$output = $view->toArray( );
			}
			
			echo json_encode(array(
				'data' => $output
			));
			
			\Ant\Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}