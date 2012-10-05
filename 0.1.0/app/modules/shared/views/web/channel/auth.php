<?php
	
	/*
	 *	The authentication channel
	 *	is a specific view acting 
	 *	as a gateway after authentication
	 *	completes. Specifically, it 
	 *	allows a JavaScript redirect
	 *	to take place to clean the URL.
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */

	namespace Ant\Web\Channel\Auth {
		
		use \Ant\Application as App;
		use \Ant\Authentication as Auth;
		use \Ant\Template as Template;
		use \Ant\Collection as Collection;
		use \Ant\Router as Router;
		use \Ant\Document as Document;
		use \Ant\Session as Session;
		
		function index( $request ){
			
			// Load a template from the shared space //
			$frame		= Template :: loadSharedTemplate('auth');
			
			$app = Session :: get('application');
			
			Template :: addGlobals( new Collection(array(
				'redirect'			=> Router :: getPublicRoot(),
				'post_return_url'	=> $app['post_return_url']
			), 'auth' ));
			
			// Buffer the template for output //
			Template :: setBuffer( $frame );
			
			// Prepare the document (stylesheets, javascripts) //
			Document :: prepare();
			
			// Channel is always responsible for output //
			echo Template :: output();
			
			// Flush the buffer so the user sees a "Logging in..." message //
			flush();
			
			switch( $_GET['type'] ){
				case 'facebook' :
				Auth :: authFacebook();
					break;
				case 'google' :
				Auth :: authGoogle();
					break;
			}
			
			exit;
		}
		
	}