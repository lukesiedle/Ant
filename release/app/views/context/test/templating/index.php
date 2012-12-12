<?php
	
	/*
	 *	Test templating
	 * 
	 *	@package Ant
	 *	@type Client
	 *	@since 0.1.0
	 */
	
	namespace View\Test\Templating;
	
	use \Core\Application as App;
	use \Core\Query as Query;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	use \Core\Database as Database;
	use \Extension\Authentication as Auth;
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
				'test_value'	=> 'Level1 : 1',
				'test_margin'	=> '10px'
			),
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Level1 : 2',
				'test_margin'	=> '10px'
			),
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Level1 : 3',
				'test_margin'	=> '10px'
			)
		), 'level' );
		
		$level1Collection = new Collection(array(
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Level2 : 1',
				'test_margin'	=> '20px'
			),
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Level2 : 2',
				'test_margin'	=> '20px'
			)), 'level' );
		
		$level2Collection = new Collection(array(
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Level3 : 1',
				'test_margin'	=> '30px'
			),
			array(
				'test_id'		=> 1,
				'test_value'	=> 'Level3 : 2',
				'test_margin'	=> '30px'
			)), 'level' );

		$level1Collection->first()->join( $level2Collection );
		
		$collection->join( $level1Collection );
		
		$set = new CollectionSet( 
			$collection
		);
		
		return $set;
	}