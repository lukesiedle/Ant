<?php
	
	/*
	 *	The home page view
	 * 
	 *	@package Ant
	 *	@type Client
	 *	@since 0.1.0
	 */
	
	namespace Ant\Test\Home;
	
	use \Ant\Application as App;
	use \Ant\Query as Query;
	use \Ant\Collection as Collection;
	use \Ant\CollectionSet as CollectionSet;
	use \Ant\Database as Database;
	use \Ant\Authentication as Auth;
	use \Ant\Router as Router;

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