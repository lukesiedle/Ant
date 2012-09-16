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
		
		use \Ant\Application as App;
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
			
			// First time installation of Ant (REMOVE THIS) //
			if( ! file_exists( $file = APPLICATION_ROOT . '/config/installed' )){
				if( Router :: getContext() != 'setup' ){
					file_put_contents( $file , 1 );
					App :: redirect('setup');
				}
			}
			
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
			if( $request = \Ant\Request :: get('post') ){
				if( isset($request['username'] )){
					$login->join( new Collection(array(
						'message' => Template :: phrase('USER_LOGIN_FAILED_MSG')
					), 'errors' ));
				}
			}
			return $login;
		}
		
		// Condition the logout form //
		function logout(){
			return Collection :: create( 'user.logout' );
		}
		
		// Store document globals for replacement
		// @since 0.1.0 //
		function document(){
			return new Collection(array(
				'title'		=> Router :: getDocTitle(),
				'root'		=> Router :: getPublicRoot(),
				'context'	=> Router :: getContext()
			), 'document' );
		}
		
		function request(){
			if( $request = \Ant\Request :: get('post')){
				return new Collection( $request, 'request' );
			}
			return new Collection;
		}
		
	}