<?php
	
	/*
	 *	Test client channel
	 * 
	 *	@package Ant
	 *	@type Test Suite
	 *	@since 0.1.0
	 */
	
	namespace Ant\Test\Channel\Ajax {
		
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