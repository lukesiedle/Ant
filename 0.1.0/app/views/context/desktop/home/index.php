<?php
	
	/**
	 *	The home page view
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	namespace View\Desktop\Home;
	
	use \Core\Application as App;
	use \Core\Query as Query;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	use \Core\Database as Database;
	use \Core\Authentication as Auth;
	use \Core\Router as Router;
	use \Core\Resource as Resource;

	/**
	 *	The function to create the view
	 * 
	 *	@since 0.1.0
	 */
	function index( $request ){
		
		$data = array( 'user_id' => 1 );
		
		$user = new Resource('user', $data );
		
		$fbAccount = new Resource('user/1/account/1');
		
	}
	