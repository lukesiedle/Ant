<?php
	
	/*
	 *	The home page view
	 * 
	 *	@package Ant
	 *	@type Client
	 *	@since 0.1.0
	 */
	
	namespace Ant\Test\Home;
	
	use \Core\Application as App;
	use \Core\Query as Query;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	use \Core\Database as Database;
	use \Core\Authentication as Auth;
	use \Core\Router as Router;

	/*
	 *	The function to create the view
	 * 
	 *	@since 0.1.0
	 */
	
	function index( $request ){
		
		$collection = new Collection(array(
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Test1'
			),
			array(
			'test_id'		=> 2,
			'test_value'	=> 'Test1'
		)), 'testloopinframe' );
		
		return new CollectionSet( $collection );
	}