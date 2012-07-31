<?php

	namespace Ant\Web {
		
		use \Ant\Application as App;
		use \Ant\Query as Query;
		use \Ant\Collection as Collection;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Database as Database;
		use \Ant\Authentication as Auth;
		use \Ant\Router as Router;
		
		function home( $request ){
			
			// auth();
			
			$users = new Collection(array(
				array(
					"user_id" => 1,
					"user_username" => "Luke"
				),
				array(
					"user_id" => 2,
					"user_username"	=> "Cody"
				))
			, 'users' );
			
			$friends = new Collection(
				array(
					"user_id"		=> 2,
					"user_username"	=> "Cody"	
				)
			, 'friends' );
			
			$friends->join( new Collection(array(
				"user_id" => 99,
				"user_username" => "Universe"
			),'creator' ), 'user_id');
			
			// $users->join( $friends );
			
			$users->join( $friends, 'user_id' );
			
			$parents = new Collection(array(
				array(
					"user_id" => 3,
					"user_username" => "Kim"
				),
				array(
					"user_id" => 4,
					"user_username"	=> "Wyn"
				)
			), 'parents' );
			
			$parents->join( $friends, 'user_id' );
			
			return new CollectionSet( array($users, $parents) );
			
		}
		
		function auth(){
			
			// Auth :: authorize('facebook', Router :: getPublicRoot() . 'welcome' );
			
			// Auth :: api( 'facebook' , '/me/events', true );
			
			Auth :: authorize('google');
			
		}
		
	}
	
?>