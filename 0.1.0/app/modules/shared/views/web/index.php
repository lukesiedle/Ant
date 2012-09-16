<?php
	
	/*
	 *	This is the index 
	 *	of the client view,
	 *	shared between this 
	 *	client's requests.
	 *	
	 *	@note This is non-specific to
	 *	the application. Perform specific
	 *	global operations inside a shared
	 *	view like 'frame'
	 * 
	 *	@package Ant
	 *	@type Shared
	 *	@since 0.1.0
	 */
	 
	 // Namespace must always specify the client //
	 // @since 0.1.0

	 namespace Ant\Web {
		
		use \Ant\Router as Router;
		use \Ant\Application as Application;
		use \Ant\Template as Template;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Controller;
		
		/*
		 *	The index function is automatically
		 *	called from within Route
		 *	
		 *	@since 0.1.0
		 */
		
		function index( $request ){
			
			// Run any specified controllers, chaining them
			// where possible
			// 
			// @note Why not use the "actions" channel? Well we
			// don't necessarily want to redirect the user.
			// Maybe we want to show the same URL after
			// the controller runs/fails.
			// 
			// @since 0.1.0 //
			if( $controllers = \Ant\Router :: getControllers() ){
				$args = array();
				foreach( $controllers as $controller ){
					
					$result = \Ant\Controller :: call( $controller, $args );
					
					// Chain the arguments //
					if( is_array( $result )){
						$args = $result;
					} else {
						$args = array( $controller => $result );
					}
				}
			}
			
			// Load view specific data
			// @since 0.1.0 //
			$view		= Router :: loadRouteView();
			$shared		= Router :: loadSharedView('frame', $view );
			
			// Load a template from the shared space
			// @since 0.1.0 //
			$frame		= Template :: loadSharedTemplate( Router :: getRouteVars()->frame );
			
			// Load the template for the view 
			// @since 0.1.0 //
			$page		= Template :: loadViewTemplate();
			
			// Map any data to the templates if they
			// are CollectionSet objects
			// @since 0.1.0 // 
			if( $shared  instanceof CollectionSet ){
				$frame -> map( $shared );
			}
			
			if( $view instanceof CollectionSet ){
				$page -> map( $view );
			}
			
			// Load the page into the frame
			// @since 0.1.0 //
			$page ->loadInto( $frame , '__CONTENT__' );
			
			// Buffer the template for output
			// @since 0.1.0 //
			Template :: setBuffer( $frame );
			
		}
	 
	 }