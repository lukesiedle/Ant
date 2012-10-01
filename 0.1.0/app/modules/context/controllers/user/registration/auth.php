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
						. $_GET['return'];
		
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
				if( $auth ){}
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
		
		if( $auth ){
			
			$userData['last_update_ut'] = date('U');
			
			// Init resource from this data //
			$rs = new \Ant\Resource( 'user_account_facebook_user', $userData );
			
			try {
				
				// Check if exists //
				$data = $rs->read();
				
				// Updates Auth details to latest //
				$data = $rs->update()->read();
				
				// Update the user account //
				$userRs = new \Ant\Resource( 'user' );
				
				$userRs->setData( filterUserFields( $data, 'update' ) );
				
				// Need to set the user first to allow update permission //
				User :: setCurrentUser( $userRs->read() );
				
				// Update the resource //
				$userRs->update();
				
				
			} catch( \Exception $e ){ 
				
				// 404 does not exist //
				if( $e->getCode() == 404 ){
					
					$data = $rs->create()->read();
					
					// Create the new user account //
					$userRs = new \Ant\Resource( 'user' , filterUserFields( $data, 'create' ) );
					
					// Read the new user data //
					$data = $userRs->create()->read();
					
					$rs->setData( array(
						'user_id' => $data['user_id']
					));
					
					$rs->update();
					
					User :: setCurrentUser( $data );
					
				} else {
					echo $e->getMessage();
				}
			}
			
		}
		
		// Redirect to the specified path //
		if( isset($_GET['return'] )){
			\Ant\Application :: redirect( $_GET['return'] );
		}
		
	}
	
	function filterUserFields( $data, $type ){
		
		$result = array(
			'user_first_name'	=> $data['field_first_name'],
			'user_last_name'	=> $data['field_last_name'],
			'user_email'		=> $data['field_email'],
			'user_login_ut'		=> date('U'),
			'user_status'		=> 1
		);
		
		switch( $type ){
			case 'create' :
				$result['user_register_ut'] = date('U');
				break;
			case 'update' :
				$result['user_id'] = $data['user_id'];
				break;
		}
		
		return $result;
	}