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
	 */
	function auth( $vars ){
		
		$thisPath = Router :: getAppPath();
		
		// Must be a valid auth type //
		switch( $vars['request']->auth_type ){
			case 'google' :
				$auth = Auth :: authorize( 'google', $thisPath );
				if( $auth ){
					
				}
				break;
			case 'facebook' :
				$auth = Auth :: authorize( 'facebook', $thisPath );
				if( $auth ){
					$userData = Auth :: api( 'facebook', 'me', 'store' );
				}
				break;
			default :
				throw new \Exception( 'Invalid auth type', 422 );
				break;
		}
		
		if( $auth ){
			
			$rs = new \Ant\Resource('user_account_facebook_user', array(
				'id'				=> $userData['id'],
				'field_email'		=> $userData['email'],
				'field_first_name'	=> $userData['first_name'],
				'field_last_name'	=> $userData['last_name'],
				'field_full_name'	=> $userData['first_name'] . ' ' . $userData['last_name'],
				'last_fetch_ut'		=> date('U')
			));
			
			$rs->create();
		}
		
		// Skip login if the user is already logged in //
		if( $user = User :: getCurrentUser() ){
			if( ! $user->isGuest() ){
				return false;
			}
		}
		
		$query	= Controller :: query('User.getUserByLogin', array(
			'username'	=> $request['username'],
			'password'	=> $request['password']
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
			User :: setCurrentUser( $data );
		}
		
	}