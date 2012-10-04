<?php
	
	namespace Ant\Controller\User\Registration;
	
	use \Ant\Request as Request;
	use \Ant\Controller as Controller;
	use \Ant\Database as Database;
	use \Ant\Cookie as Cookie;
	use \Ant\Configuration as Config;
	use \Ant\User as User;
	use \Ant\Authentication as Auth;
	use \Ant\Router as Router;
	use \Ant\Application as App;
	
	/**
	 *	Log the user in with 3rd-party
	 *	APIs, Google or Facebook.
	 *	
	 *	@param array $vars The args
	 *	
	 *	@since 0.1.0
	 *	@todo Stuff
	 */
	function auth( $vars ){
		
		$redirPath = Router :: getAppPath() 
					. '?return=' 
						. urldecode( $_GET['return'] );
		
		// See if a user is logged in //
		$user = User :: getCurrentUser();
		
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
			initUser( $vars['request']->auth_type, $userData );
		}
		
		// Redirect to the specified path //
		if( isset($_GET['return'] )){
			\Ant\Application :: redirect( $_GET['return'] );
		}
		
	}
	
	function initUser( $type, $userData ){
		
		$userData['last_update_ut'] = date('U');
		
		// Init both resources from the data //
		$fbUser = new \Ant\Resource( 'user_account_facebook_user' , $userData );
		$gUser	= new \Ant\Resource( 'user_account_google_user' , $userData );
		
		// Try read the Facebook resource //
		try {
			$fbData = $fbUser->read();
		} catch( \Exception $e ){
			// Does not exist //
			if( $e->getCode() == 404 ){
				$fbData = null;
			} else {
				die( $e->getMessage() );
			}
		}
		
		// Try read the Google resource //
		try {
			$gData = $gUser->read();
		} catch( \Exception $e ){
			// Does not exist //
			if( $e->getCode() == 404 ){
				$gData = null;
			} else {
				die( $e->getMessage() );
			}
		}
		
		/*	
		 *	A new auth does not necessarily
		 *	mean a new user. A check to see
		 *	if the email of this auth matches	
		 *	the others.
		 */
		switch( $type ){
			case 'facebook' :
				if( $gData ){
					$userId = $gData['user_id'];
				}
				
				$newAuth = is_null( $fbData );
				$rs = $fbUser;
				break;
			case 'google' :
				
				if( $fbData ){
					$userId = $fbData['user_id'];
				}
				
				$newAuth = is_null( $gData );
				$rs = $gUser;
				
				break;
		}
		
		// Proceed with a new auth ... //
		if( $newAuth ){
			
			$data = $rs->create()->read();
			
			// Init the use resource //
			$userRs = new \Ant\Resource( 'user' , filterFields( $data, 'create' ) );
			
			if( ! $userId ){
				// It's a new user 
				// Create and read to get the Id //
				$data = $userRs->create()->read();
				$userId = $data['user_id'];
			}
			
			$data = $userRs->read();
			
			$rs->setData( array(
				'user_id' => $userId
			));
			
			$rs->update();

			User :: setCurrentUser( $data );
			
		} else {
			
			// Check if exists //
			$data = $rs->read();
			
			// Updates Auth details to latest //
			$data = $rs->update()->read();
			
			// Update the user account //
			$userRs = new \Ant\Resource( 'user' );
			
			$userRs->setData( filterFields( $data, 'update') );
			
			// Need to set the user first to allow update permission //
			User :: setCurrentUser( $userRs->read() );
			
			// Update the resource //
			$userRs->update();
			
		}
	}
	
	function filterFields( $data, $task ){
		
		$result = array(
			'user_first_name'	=> $data['field_first_name'],
			'user_last_name'	=> $data['field_last_name'],
			'user_email'		=> $data['field_email'],
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
	}