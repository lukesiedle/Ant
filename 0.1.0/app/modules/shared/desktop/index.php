<?php
	
	/**
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

	 namespace Ant\Desktop {
		
		use \Core\Router as Router;
		use \Core\Application as Application;
		use \Core\Template as Template;
		use \Core\CollectionSet as CollectionSet;
		use \Core\Controller as Controller;
		use \Core\Request as Request;
		use \Core\Session as Session;
		
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
			if( $controllers = Router :: getControllers() ){
				$args = array();
				foreach( $controllers as $controller ){
					
					// Execute the controller to get a result //
					$result = Controller :: call( $controller, $args );
					
					// Chain the arguments //
					if( is_array( $result )){
						$args = $result;
					} else {
						$args = array( $controller => $result );
					}
				}
			}
			
			// Get the CSRF tokens from session and add them to memory
			// @since 0.1.0 //
			Request :: setCSRF( Session :: get('csrf') );
			
			$oldChannel	= Router :: getChannel();
			
			// Load view specific data
			// @since 0.1.0 //
			try {
				$view		= Router :: loadRouteView();
			} catch( \Exception $e ){
				switch( $e->getCode() ){
					case 403 :
						Application :: setError('403', $e->getMessage() );
						break;
					default : 
						throw new \Exception( $e->getMessage() );
						break;
				}
			}
			
			// Check if the channel has changed 
			// from the view, and stop here
			// @since 0.1.0 //
			if( $oldChannel != Router :: getChannel() ){
				return;
			}
			
			// Get the CSRF tokens from memory and store them
			// @since 0.1.0
			Session :: add( 'csrf', Request :: getCSRF() );
			
			// Load the shared view
			// @since 0.1.0
			$shared		= Router :: loadSharedView('frame', $view );
			
			// Load a template from the shared space
			// The shared template specified inside Route
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