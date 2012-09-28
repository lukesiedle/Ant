<?php

	/*	
	 *	Handles authentication
	 *	using either the Facebook
	 *	or Google API.
	 * 
	 *	@package Ant
	 *	@subpackage Authentication
	 *	@since 0.1.0
	 */

	namespace Ant {
		
		use \apiClient as GoogleApiClient;
		
		Class Authentication {
			
			// Stores all authorizations and their tokens //
			public static $authorizations = array();
			public static $facebook;
			public static $data = array();
			
			public $authType;
			
			function __construct( $type ){ }
			
			/**
			 *	Check if authorization
			 *	of the type has already
			 *	occurred.
			 *	
			 *	@param string $type The type of auth 'facebook', 'google'
			 * 
			 *	@since 0.1.0
			 */
			public static function isAuthorized( $type ) {
				return isset( self :: $authorizations[$type] );
			}
			
			/**
			 *	Authorize the type
			 *	and an optional post
			 *	return url.
			 *	
			 *	@param string $type The type of auth 'facebook', 'google'
			 *	@param string $postReturnUrl The url to return to 
			 * 
			 *	@since 0.1.0
			 *	@return array Basic auth data
			 */
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
			
			/**
			 *	Shortcut URL to output
			 *	current authorizations.	
			 * 
			 *	@since 0.1.0
			 */
			public static function getAuthStatus(){
				Application :: out( self :: $authorizations );
			}
			
			/**
			 *	Authorize Facebook with
			 *	optional return url
			 * 
			 *	@param string $postReturnUrl The url to return to
			 * 
			 *	@since 0.1.0
			 *	@return array Basic auth data
			 */
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
			
			/**
			 *	Authorize Google with
			 *	optional return url
			 * 
			 *	@param string $postReturnUrl The url to return to
			 * 
			 *	@since 0.1.0
			 *	@return array Basic auth data
			 */
			public static function authGoogle( $postReturnUrl = null ){
				
				if( ! $postReturnUrl ){
					$postReturnUrl = Router :: getPublicRoot();
				}
				
				$config = (object)Configuration :: get('google_app');
				
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
					$app = Session :: get('application');
					Application :: redirect( $app['post_return_url'] );
					return;
				} else {
					if( $auth = Session :: get('authentication') ){
						if( $google = $auth['google'] ){
							$client->setAccessToken( $google['token'] );
							self :: $authorizations['google'] = $google;
							return;
						}
					}
				}
				
				Application :: redirect( $client->createAuthUrl() );
				
			}
			
			
			/**
			 *	Perform an API request 
			 *	based on type. Optionally
			 *	store it in the session.
			 * 
			 *	@param string $type Auth type 'facebook', 'google'
			 *	@param string $request The api string
			 *	@param bool $store Store the request in the session
			 *	
			 *	@since 0.1.0
			 *	@return array API response data
			 */
			public static function api( $type, $request, $store = false ){
				switch( $type ){
					case 'facebook' :
						
						if( ! self :: isAuthorized('facebook') ){
							throw 'Facebook is not yet authorized';
						}
						
						// If being stored, check if it exists already //
						if( $store ){
							$facebook = Session :: get('facebook');
							$data = $facebook['data'];
							if( $data && $data[ $request ] ){
								return $data[ $request ];
							}
						}
						
						// Do API request //
						$data = self :: $facebook->api($request);
						
						// Store in the session //
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