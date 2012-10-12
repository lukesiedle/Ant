<?php
	
	/*
	 *	The resource view
	 * 
	 *	@package Ant
	 *	@type Client
	 *	@since 0.1.0
	 */
	 
	namespace Ant\Desktop\Resource;
	
	use \Core\Application as App;
	use \Core\Query as Query;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	use \Core\Database as Database;
	use \Core\Authentication as Auth;
	use \Core\Router as Router;
	use \Core\Request as Request;

	/*
	 *	The function to create the view
	 * 
	 *	@since 0.1.0
	 */
	
	function index( $request ){
		
		// Look for "resource" in request //
		$requestVars = Router :: getRequestVars();
		
		// Show a help page if local //
		if( App :: get()->local ){
			if( !isset( $requestVars->resource )){
				return;
			}
		}
		
		// Hard set the channel to simplify URLs to resource view //
		Router :: resetChannel( 'resource' );
		
	}