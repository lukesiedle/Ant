<?php

	namespace Ant {
		
		Class UserAddFriend extends User {
			
			public function initialize( $request ){
				$this->me = self :: getCurrent();
				if( !isset($request['user_id'])){
					throw new \Exception( 'No User Id found.' );
				}
				// return $this->addFriend( $request['user_id'] );
				return $this;
			}
			
			// @returns Collection
			public function addFriend( $friendId ){
				
				$collection = new Collection(array(
					'user_id'	=> $this->me->user_id,
					'friend_id'	=> $friendId
				), 'user_friends' );
				
				$collection->save('_id');
				
				return $collection;
			}
			
		}
		
	}

?>