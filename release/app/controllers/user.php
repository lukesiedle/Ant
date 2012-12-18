<?php

	namespace Controller;
	
	// Core //
	use \Core\Database as Database;
	use \Core\Application as App;
	use \Core\Router as Router;
	use \Core\Resource as Resource;
	use \Core\Configuration as Config;
	
	// Context //
	use \Model\User as Model;
	use \Data\User as Data;
	use \Query\User as Query;
	
	// Extension //
	use \Extension\Authentication as Auth;
	
	/**
	 * 	The User controller class.
	 * 
	 * 	@since 0.1.1
	 */
	Class User {
		
		/**
		 *	Get the user (or set of users) 
		 *	by Id
		 * 
		 *	@param array $vars Data passed to
		 *	the controller. Requires an id is
		 *	passed as an int or array.
		 *	
		 *	@since 0.1.0
		 *	@return Collection The user(s)
		 */
		static function getById($id) {

			if (!is_array($id)) {
				(array) $id;
			}

			$query = Query :: get()
					->where('user.user_id IN( :ids )', array(
				'ids' => implode(',', $ids)
			));
			
			return Database :: query($query);
		}
		
		/**
		 *	Get the user based
		 *	on a secret token
		 *	
		 *	@param array $vars The data
		 *	which requires 'secret' be
		 *	passed inside.
		 *	
		 *	@since 0.1.0
		 *	@return bool false | Collection The user data
	 	 */
		static function getBySecret() {

			// Load the base query and extend it //
			$query = Query :: get()
				->where('user.user_secret = :secret', array(
				'secret' => $vars['secret']
			));
			
			if ($collection = Database :: query($query)) {
				if ($collection->length() > 0) {
					return $collection;
				}
				return false;
			}
			
			// No user was found //
			return false;
		}
		
		static function intentRegister( array $opts 
		/*
			resource, 
			result,
			errors
		 */
		){	
			$resolvePath = 'user/register/complete/' . $opts['result']['data']['user_id'];
			
			if( ! $opts['result']['success'] ){
				$resolvePath = 'user/register';
			}
			
			// Save the data once for reload //
			\Extension\Persistence :: save( 
				$resolvePath, 
				array(
					'errors' => $opts['errors'],
					'data'	=> $opts['resource']->handler->getPreparedData()
				)
			);
			
			App :: redirect( $resolvePath );
		}
		
		static function intentEditProfile( array $opts 
		/*
			resource, 
			result,
			errors
		 */
		){	
			$resolvePath = 'user/register/edit';
			
			// Save the data once for reload //
			\Extension\Persistence :: save( 
				$resolvePath, 
				array(
					'errors' => $opts['errors'],
					'data'	=> $opts['resource']->handler->getPreparedData(),
					'success' => $opts['result']['success']
				)
			);
			
			App :: redirect( $resolvePath );
		}
		
		/**
		 *	A post-resource create hook.
		 *	
		 *	If the resource was not created using
		 *	Ajax the application issues a redirect
		 *	to specify the profile was saved.
		 * 
		 *	@since 0.1.0	
		 */
		static function editProfile( $args ){
			if( ! $args['is_ajax'] ){
				App :: redirect( 'user/edit/?saved' );
			}
		}

		/**
		 *	Log the user out, destroying
		 *	their current session, and cookie.
		 *	
		 *	Redirects the user home
		 *	
		 *	@since 0.1.0
		 */
		static function logout(){
			
			// Clear session and cookie //
			\Core\Session :: clear( 'User' );
			\Core\Cookie :: delete( 'User' );
			\Extension\Authentication :: clearSession();
			
			// Redirect home //
			\Core\Application :: redirect();
		}
		
		
		/**
		 *	Log the user in based 
		 *	on username/password combination
		 *	
		 *	This is different to authorization,
		 *	logging the user in via an OAuth
		 *	protocol like Facebook Connect	
		 *	
		 *	@param array $data
		 * 
		 *	@since 0.1.0
		 */
		static function login( $data = null ) {
			
			if( is_null( $data ) ){
				// Must be a post //
				if( ! $data = Request :: get('post') ){
					return;
				}
			}
			
			// Skip login if the user is already logged in //
			if( $user = Model :: getCurrentUser() ){
				if( ! $user->isGuest() ){
					return false;
				}
			}
			
			$query	= Query :: getByLogin(array(
				'email'		=> $data['email'],
				'password'	=> $data['password']
			));
			
			$collection = Database :: query( $query );		
			
			if( $collection->length() == 1 ){

				$data = $collection->first()->toArray();

				if( $request['remember_me'] ){
					Cookie :: set('Ant.User', array(
						'user_secret'	=> $data['user_secret']
					), Config :: get('login_cookie_age') );
				}

				// Set the current user in memory //
				Model :: setCurrentUser( $data );
			}
			
		}

		/**
		 *	Determine if the user 
		 *	is a guest
		 * 
		 *	@since 0.1.0
		 *	@return bool True if guest or false
		 */
		static function isGuest(){
			return Model :: getCurrentUser()->isGuest();
		}
		
		/**
		 *	Log the user in with 3rd-party
		 *	APIs, Google or Facebook.
		 *	
		 *	@param array $vars The args
		 *	
		 *	@since 0.1.0
		 */
		static function auth( $vars ){
			
			// Anonymous functions //
			/**
			 *	Handles resource creation
			 *	of a Google or Facebook user.
			 *	
			 *	@param string $type The type of auth 'google' 'facebook'
			 *	@param array $userData The data of the user returned
			 *	from the Api request
			 *	
			 *	@since 0.1.0
			 */
			
			
			
			$initAuthUser = function( $type, $userData ){
				
				$userData['last_update_ut'] = date('U');
				
				$userData['auth_type'] = $type;
				
				// Init user resource //
				$user		= new Resource( 'user', array(
					'user_email' => $userData['email']
				));
				
				// Init the account resource //
				$account	= new Resource( 'account' , $userData );
				
				// Check if the user exists //
				try {
					$regData = $user->read();
					$accountData = $account->read();
				} catch( \Exception $e ){
					// Does not exist //
					if( $e->getCode() == 404 ){
					} else {
						die( $e->getMessage() );
					}
				}
				
				// At least one account exists under this email //
				$newAuth = false;
				if( $regData ){
					if( ! $accountData ){
						$newAuth = true;
					}
				} else {
					$newAuth = true;
				}

				// For filtering fields //
				$filterFields = function( $data, $task ){
					
					$result = array(
						'user_first_name'	=> $data['first_name'],
						'user_last_name'	=> $data['last_name'],
						'user_email'		=> $data['email'],
						'user_login_ut'		=> date('U'),
						'user_status'		=> 1
					);
					switch( $task ){
						case 'create' :
							$result['user_register_ut'] = date('U');
							break;
						case 'update' :
							$result['user_id'] = $data['user_id'];
							break;
					}

					return $result;
				};
				
				// Proceed with a new auth ... //
				if( $newAuth ){
					
					
					
					// Filter the data for the resource //
					$user->handler->setData( $filterFields( $userData, 'update') );
					
					if( ! $regData['user_id'] ){
						// It's a new user 
						// Create and read to get the Id //
						$regData	= $user->create()->read();
					} 
					
					$account->handler->setData(array(
						'user_id' => $regData['user_id']
					));
					
					$accountData = $account->create()->read();
					
					Model :: setCurrentUser( $regData );
					
				} else {
					
					// Set the user Id //
					$userData['user_id'] = $regData['user_id'];
					
					// Update the user //
					$user->handler->setData( $filterFields( $userData, 'update') );
					
					// Need to set the user first to allow update permission //
					Model :: setCurrentUser( $regData );
					
					// Update the resource //
					$user->update();
				}
				
			};
			
			$redirPath = Router :: getAppPath() 
						. '?return=' 
							. urldecode( $_GET['return'] );
			
			// See if a user is logged in //
			$user = Model :: getCurrentUser();
			
			
			
			if( ! $user->isGuest() ){
				// return true;
				$userData = $user->getData();
				if( $userData[ $vars['request']->auth_type ]){
					return true;
				}
			}			

			// Must be a valid auth type //
			switch( $vars['request']->auth_type ){
				case 'google' :
					$auth = Auth :: authorize( 'google', $redirPath );
					if( $auth ){
						$oauth = Auth :: api( 'google', 'oauth2' );
						$userData = $oauth->userinfo->get();
					}
					break;
				case 'facebook' :
					$auth = Auth :: authorize( 'facebook', $redirPath );					
					if( $auth ){
						$userData = Auth :: api( 'facebook', 'me', 'store' );
					}
					break;
				default :
					throw new \Exception( 'Invalid auth type', 422 );
					break;
			}
			
			// Setup the user //
			if( $auth ){ 
				$initAuthUser( $vars['request']->auth_type, $userData );
			}
			
			// Redirect to the specified path //
			if( isset($_GET['return'] )){
				App :: redirect( $_GET['return'] );
			}
	
		}
		
		
		/**
		 * 	Initialize the user
		 * 	based on the existing
		 *	session/cookie.
		 *	
		 *	@param array $arguments
		 * 
		 * 	@since 0.1.0
		 *	@return User The user store object, 
		 *	guest or logged in
		 */
		static function initialize() {
			
			// Check if the user exists in browser cookie or session //
			switch ( App :: getClient() ) {
				case 'web' :
					
					// User is in cookie if they opted for "remember me" //
					if ( $cookie = Cookie :: get('Ant.User') ) {

						$user = self :: getUserFromSecret( $cookie['user_secret'] );

						if ( $user ) {
							Model :: setCurrentUser( $user );
						} else {
							// That cookie is invalid, delete it //
							Cookie :: delete('Ant.User');
						}
					}
					break;
			}
			
			// Try get the current user or return a blank user //
			return Model :: getCurrentUser();
		}

	}