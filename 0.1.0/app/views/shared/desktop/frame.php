<?php
	
	/**
	 *	Shared view 'frame' for performing
	 *	global client view tasks specific
	 *	to the application.
	 *	
	 *	@package Ant
	 *	@since 0.1.0
	 */
	namespace View\Desktop {
		
		// Core //
		use \Core\Application as App;
		use \Core\Collection as Collection;
		use \Core\CollectionSet as CollectionSet;
		use \Core\Database as Database;
		use \Core\Authentication as Auth;
		use \Core\Router as Router;
		use \Core\Template as Template;
		use \Core\Request as Request;
		
		// Context //
		use \Controller\User as UserControl;
		
		function frame(){
			
			// First time installation of framework (REMOVE THIS) //
			if( ! file_exists( $file = APPLICATION_ROOT . '/config/installed' )){
				if( Router :: getContext() != 'setup' ){
					file_put_contents( $file , 1 );
					App :: redirect('setup');
				}
			}
			
			// Always attempt to determine the current user //
			$theUser = UserControl :: initialize();
			
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
			
			$data['return'] = urlencode( Router :: getAppPath() );
			$login = new Collection( $data , 'user.login' );
			
			if( $request = Request :: get('post') ){
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
			if( $request = Request :: get('post')){
				return new Collection( $request, 'request' );
			}
			return new Collection;
		}
		
	}