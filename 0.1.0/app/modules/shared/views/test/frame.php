<?php
	
	/*
	 * 
	 *	Test client
	 *	
	 *	@package Ant
	 *	@type Test Suite
	 *	@since 0.1.0
	 * 
	 */

	namespace Ant\Test {
		
		use \Ant\Collection as Collection;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Control;
		use \Ant\Database as Database;
		use \Ant\Authentication as Auth;
		use \Ant\Router as Router;
		
		// Context Classes //
		use \Ant\User as User;
		
		function frame(){
			
			$collection = new Collection(array(
				array(
					'test_id'		=> 1,
					'test_value'	=> 'Loop1'
				),
				array(
				'test_id'		=> 2,
				'test_value'	=> 'Loop2'
			)), 'testloopinframe' );
			
			return new CollectionSet( 
				$collection
			);
		}
		
	}