<?php

	/*
	 *	@description	
	 *	Handles authentication
	 *	using either the Facebook
	 *	or Google API.
	 */

	namespace Ant {
		
		use \apiClient as GoogleApiClient;
		
		Class Authentication {
			
			// Stores all authorizations and their tokens //
			public static $authorizations = array();
			public static $facebook;
			public static $data = array();
			
			public $authType;
			
			function __construct( $type ){
				
			}
			
			public static function isAuthorized( $type ) {
				return isset( self :: $authorizations[$type] );
			}
			
			public static function authorize( $type, $postReturnUrl = null ){
				if( self :: isAuthorized($type)){
					return self :: $authorizations[ $type ];
				}
				switch( $type ){
					case 'facebook' :
						self :: authFacebook( $postReturnUrl );
						return self :: $authorizations['facebook'];
						break;
					case 'google' : 
						self :: authGoogle( $postReturnUrl );
						return self :: $authorizations['google'];
						break;
				}
			}
			
			public static function getAuthStatus(){
				Application :: out( self :: $authorizations );
			}
			
			public static function authFacebook( $postReturnUrl = null ){
				
				if( !$postReturnUrl ){
					$postReturnUrl = Router :: getPublicRoot();
				}
				
				$config = Configuration :: get('facebook_app');
				
				self :: $facebook = $facebook = new \Facebook(array(
					'appId'		=> $config->app_id,
					'secret'	=> $config->app_secret
				));
				
				if( $auth = Session :: get('authentication')->facebook ){
					self :: $authorizations['facebook'] = $auth;
					return $auth;
				} else {
					if( $user = $facebook->getUser() ){
						Session :: add('authentication', array(
							'facebook' => $user
						));
						self :: $authorizations['facebook'] = $user;
						// self :: cleanUrl('facebook');
						return $user;
					}
					
					header( 'Location:' . $facebook->getLoginUrl(array(
						// Pass the redirect to a javascript file to remove the hash //
						'redirect_uri'	=> Router :: getPublicRoot() . '?channel=auth&redir=' . urlencode($postReturnUrl),
						'scope'			=> implode( ', ', $config->scope )
					)));
					
					exit;
					
				}
				
			}
			
			public static function authGoogle( $postReturnUrl = null ){
				
				if( ! $postReturnUrl ){
					$postReturnUrl = Router :: getPublicRoot();
				}
				
				$config = Configuration :: get('google_app');
				
				$client = new GoogleApiClient();
				
				$client->setClientId( $config->client_id );
				$client->setClientSecret( $config->client_secret );
				
				Session :: add('application', array(
					'post_return_url' => $postReturnUrl
				));
				
				$client->setRedirectUri( Router :: getPublicRoot() );
				
				$client->setScopes( $config->scopes );
				
				if (isset($_GET['code'])) {
					$client->authenticate();
					Session :: add( 'authentication', array(
						'google'	=> array(
							'token' =>	$client->getAccessToken()
						)
					));
					Application :: redirect( Session :: get('application')->post_return_url );
					return;
				} else {
					if( $google = Session :: get('authentication')->google ){
						$client->setAccessToken( $google['token'] );
						self :: $authorizations['google'] = $google;
						return;
					}
				}
				
				Application :: redirect( $client->createAuthUrl() );
				
			}
			
			// If it's an authorization, need to redirect to clean up //
			public static function cleanUrl( $authType, $redir = null ){
				// Facebook //
				switch( $authType ){
					case 'facebook' :
					Application :: redirect( Router :: getAppPath() );
						exit;
					case 'google' :
					Application :: redirect(Router :: getAppPath());
						exit;
				}
			}
			
			// API request // 
			public static function api( $type, $request, $store = false ){
				switch( $type ){
					case 'facebook' :
						if( ! self :: isAuthorized('facebook') ){
							throw 'Facebook is not yet authorized';
						}
						if( $store ){
							$data = Session :: get('facebook')->data;
							if( $data && $data[ $request ] ){
								return $data[ $request ];
							}
						}
						
						$data = self :: $facebook->api($request);
						
						if( $store ){
							Session :: add('facebook', array(
								'data' => array(
									$request => $data
								)
							));
						}
						return $data;
						break;
					case 'google' :
						break;
				}
			}
			
		}
		 
		 
	}


?>