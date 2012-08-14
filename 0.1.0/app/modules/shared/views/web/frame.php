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
	 * 
	 */

	namespace Ant\Web {
		
		use \Ant\Collection as Collection;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Control;
		use \Ant\Database as Database;
		use \Ant\Authentication as Auth;
		use \Ant\Router as Router;
		use \Ant\Template as Template;
		
		// Context Classes //
		use \Ant\User as User;
		
		function frame(){
			
			// Always attempt to determine the current user //
			$theUser = Control :: call('User.initialize');
			
			return new CollectionSet(
				
				// Document globals //
				document(), 
					
				// Login form //
				login( $theUser->getData() ), 
					
				// Store request for pre-filling forms //
				request(), 
					
				// Store logout button
				logout()
			);
		}
		
		// Store the login //
		function login( $data ){
			$login = new Collection( $data , 'user.login' );
			if( $request = \Ant\Request :: get('post')){
				if( $request['username'] ){
					$login->join( new Collection(array(
						'message' => 'Failed to log you in.'
					), 'errors' ));
				}
			}
			return $login;
		}
		
		function logout(){
			return new Collection( 1, 'user.logout' );
		}
		
		// Store document globals for replacement
		// @since 0.1.0 //
		function document(){
			return new Collection(array(
				'title' => Router :: getDocTitle(),
				'root'	=> Router :: getPublicRoot()
			), 'document' );
		}
		
		function request(){
			if( $request = \Ant\Request :: get('post')){
				return new Collection( $request, 'request' );
			}
			return new Collection;
		}
		
	}