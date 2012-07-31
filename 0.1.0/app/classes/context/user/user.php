<?php
	
	/*
	 *	@description
	 *	Every context has methods
	 *	which extend from the store.
	 * 
	 *	These methods are used 
	 *	within public controllers
	 *	as well as directly
	 * 
	 */

	namespace Ant {
		
		Class User extends Store {
			
			public static $me = false;
			
			public function __construct( $userData = null ){
				// parent :: __construct();
				if( $userData ){
					$this->data = $userData;
				}
				
			}
			
			/*
			 *	@todo
			 *	If the user has logged in 
			 *	try get them from 
			 *	session/cookie
			 * 
			 *	Remember me, etc.
			 */
			
			public static function setCurrent( $data ){
				self :: $me = new self( $data );
			}
			
			public static function getCurrent(){
				return self :: $me;
			}
			
			/*
			 *	@todo
			 *	Somehow form data or 
			 *	data from authentication
			 *	must be routed here...
			 * 
			 */
			
			// Register the current user //
			public static function register(){
				
			}
			
			// Login the current user //
			public static function login(){
				
			}
			
		}
	
	}


?>