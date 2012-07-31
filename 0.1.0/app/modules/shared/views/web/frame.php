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
		
		// Context Classes //
		use \Ant\User as User;
		
		function frame(){
			
			// Static call //
			Control :: call('User', 'setCurrent', array(
				'user_id' => 1,
				'friend_id' => 2
			));
			
			$user = Control :: call('User', 'getCurrent');
			
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
			$data = Control :: query('user', 'getUser')->edit(function( $query ){
				$query->limit( 2 );
			})->execute();
			
			$data->output();
			
			return new CollectionSet( $collection, $object );
			
		}
		
	}


?>