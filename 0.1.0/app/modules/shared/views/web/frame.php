<?php
	
	/*
	 * 
	 *	Shared view 'frame' for performing
	 *	global client view tasks specific
	 *	to the application.
	 *	
	 *	@package Ant
	 *	@type Shared
	 *	@since 0.1.0
	 */

	namespace Ant\Web {
		
		use \Ant\Collection as Collection;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Control;
		use \Ant\Database as Database;
		
		// Context Classes //
		use \Ant\User as User;
		
		function frame(){
			
			// Static call //
			Control :: call('User.setCurrent', array(
				'user_id' => 1,
				'friend_id' => 2
			));
			
			$user = Control :: call('User.getCurrent');
			
			$query = Control :: query('User.getUser');
			
			$collection = new Collection(array(
				"me" => "Hey this is me..."
			), "me" );
			
			$object		= new Collection(array(
				"stuff" => "true"
			), "object" );
			
			$object->first()->join( new Collection(array(array(
				"stuff" => "truer"
			), array(
				"stuff" => "truest"
			)), 'multi'), 'stuff' );
			
			// Control :: call('user', 'addFriend');
			$userQuery = Control :: query('user', 'getUser');
			$userQuery->limit(2);
			
			Database :: query( $userQuery )-> output();
			
			return new CollectionSet( $collection, $object );
			
		}
		
	}


?>