<?php
	
	/*
	 *	Wrapper class for session
	 *	handling
	 * 
	 *	@package Ant
	 *	@subpackage Session
	 *	@since 0.1.0
	 * 
	 */

	namespace Ant {
		
		Class Session {
			
			public static $keychain = 'ant';
			public static $started	= false;
			
			/*
			 *	Start the session
			 *	
			 *	@since 0.1.0
			 */
			
			public static function init(){
				session_start();
				self :: $started = true;
				if( ! isset($_SESSION[self::$keychain]) ){
					$_SESSION[self::$keychain] = array();
				}
			}
			
			/*
			 *	Extend the session
			 *	
			 *	@since 0.1.0
			 */
			
			public static function add( $key, $arr ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( isset($_SESSION[self::$keychain][$key] )){
					$_SESSION[self::$keychain][$key] = array_merge(
						$_SESSION[self::$keychain], $arr
					);
				} else {
					$_SESSION[self::$keychain] = $arr;
				}
			}
			
			/*
			 *	Get a session value
			 *	
			 *	@since 0.1.0
			 */
			
			public static function get( $key = null ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( is_null($key) ){
					return $_SESSION[self::$keychain];
				}
				return $_SESSION[self::$keychain][ $key ];
			}
			
			/*
			 *	Clear a session value
			 *	
			 *	@since 0.1.0
			 */
			
			public static function clear( $key = null ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( !$key ){
					$_SESSION[self::$keychain] = array();
				} else {
					unset( $_SESSION[self::$keychain][$key] );
				}
			}
			
			/*
			 *	Get or set the session Id
			 *	
			 *	@since 0.1.0
			 */
			
			public static function id( $set = false ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( ! $set ){
					return session_id();
				}
				session_id( $set );
			}
			
		}
		
	}