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
		
		use \Core\Collection as Collection;
		use \Core\CollectionSet as CollectionSet;
		use \Core\Controller as Control;
		use \Core\Database as Database;
		use \Core\Authentication as Auth;
		use \Core\Router as Router;
		
		// Context Classes //
		use \Model\User as User;
		
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