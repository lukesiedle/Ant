<?php

	namespace Ant\Controller\User {
		
		use \Ant\User as User;
		
		function addFriend( $friendId ){
			
			$me = User :: getCurrent();
			
			$collection = new Collection(array(
				'user_id'	=> $me->user_id,
				'friend_id'	=> $friendId
			), 'user_friends' );
			
			$collection->save('_id');
			
			return $collection;
		}
	}

?>