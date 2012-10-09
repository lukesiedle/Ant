<?php
	
	/**
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
	 *	@since 0.1.0
	 */
	namespace Ant {
		
		Class User extends Store {
			
			public static $me;
			
			/**
			 *	Sets the current user
			 * 
			 *	@param array $data The data to apply
			 *	to the user store.
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
			
			/**
			 *	Gets the current user, 
			 *	or creates a guest if none
			 *	is specified.
			 * 
			 *	@since 0.1.0
			 *	@return User The user store object
			 */
			public static function getCurrentUser(){				
				if( ! self :: $me ){
					
					// Default to guest //
					self :: setCurrentUser(array(
						'guest' => 1
					));
					
					// Tries to load from session //
					self :: loadUser();
					
				}
				return self :: $me;
			}
			
			/**
			 *	Store the user in the
			 *	session.
			 * 
			 *	@since 0.1.0
			 */
			public static function storeUser(){
				Session :: add( 'User', self :: $me->getData() );
			}
			
			/**
			 *	Load the user from the
			 *	session and sets the
			 *	user as current
			 *	
			 *	@since 0.1.0
			 *	@return User The user store object
			 */
			public static function loadUser(){
				$data = Session :: get( 'User' );
				if( $data ){
					self :: setCurrentUser( $data );
					return self :: getCurrentUser();
				}
			}
			
			/**
			 *	Check if the user is a
			 *	guest
			 * 
			 *	@since 0.1.0
			 *	@return bool Whether user is guest
			 */
			public function isGuest(){
				$data = $this->getData();
				return $data['guest'];
			}
			
		}
	}