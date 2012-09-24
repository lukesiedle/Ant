<?php
	
	/*
	 *	The resource view
	 * 
	 *	@package Ant
	 *	@type Client
	 *	@since 0.1.0
	 */
	 
	namespace Ant\Web\Resource;
	
	use \Ant\Application as App;
	use \Ant\Query as Query;
	use \Ant\Collection as Collection;
	use \Ant\CollectionSet as CollectionSet;
	use \Ant\Database as Database;
	use \Ant\Authentication as Auth;
	use \Ant\Router as Router;
	use \Ant\Request as Request;

	/*
	 *	The function to create the view
	 * 
	 *	@since 0.1.0
	 */
	
	function index( $request ){
		
		// Look for "resource" in request //
		$requestVars = Router :: getRequestVars();
		
		// Don't want a standard HTML view //
		if( App :: get()->local ){
			if( !isset( $requestVars->resource )){
				return;
			}
		}
		
		// Hard set the channel to simplify URLs to resource view //
		Router :: resetChannel( 'resource' );
		
	}