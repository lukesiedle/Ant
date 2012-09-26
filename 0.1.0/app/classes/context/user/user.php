<?php
	
	/*
	 *	Every context has methods
	 *	which extend from the store.
	 * 
	 *	These methods usually shouldn't
	 *	interact directly with other
	 *	parts of the application, apart
	 *	from Session/Cookie which are
	 *	wrapper classes.
	 * 
	 *	@package Ant
	 *	@subpackage User
	 *	@type Context
	 *	@since 0.1.0
	 */
	 
	namespace Ant {
		
		Class User extends Store {
			
			public static $me;
			
			/*
			 *	Sets the current user
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setCurrentUser( $data ){
				if( $data['user_id'] ){
					$data['guest'] = false;
				}
				self :: $me = new self( $data, 'user' );
				self :: storeUser();
			}
			
			/*
			 *	Gets the current user, 
			 *	or creates a guest if none
			 *	is specified.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function getCurrentUser(){
				self :: loadUser();
				if( ! self :: $me ){
					self :: setCurrentUser(array(
						'guest' => 1
					));
				}
				return self :: $me;
			}
			
			/*
			 *	Store the user in the
			 *	session
			 * 
			 *	@since 0.1.0
			 */
			
			public static function storeUser(){
				Session :: add( 'Ant.User', self :: $me->getData() );
			}
			
			/*
			 *	Load the user from the
			 *	session
			 * 
			 *	@since 0.1.0
			 */
			
			public static function loadUser(){
				$data = Session :: get( 'Ant.User' );
				if( $data ){
					self :: setCurrentUser( $data );
				}
			}
			
			/*
			 *	Check if the user is a
			 *	guest
			 * 
			 *	@since 0.1.0
			 */
			
			public function isGuest(){
				$data = $this->getData();
				return $data['guest'];
			}
			
		}
	}