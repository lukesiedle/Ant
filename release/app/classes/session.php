<?php
	
	/**
	 *	Wrapper class for session
	 *	handling, for consistency
	 *	and to avoid conflicts with
	 *	other libraries.
	 * 
	 *	@package Core
	 *	@subpackage Session
	 *	@since 0.1.0
	 */
	namespace Core {
		
		Class Session {
			
			public static $keychain = 'ant';
			public static $started	= false;
			
			/**
			 *	Start the session
			 *	and create the keychain.
			 * 
			 *	The keychain stops session values
			 *	for the application conflicting
			 *	with session values that other
			 *	libraries included may use.
			 *	
			 *	@since 0.1.0
			 */
			public static function init(){
				session_start();
				self :: $started = true;
				
				if( ! isset($_SESSION[ self::$keychain ]) ){
					$_SESSION[ self::$keychain ] = array();
				}
			}
			
			/**
			 *	Extend the session
			 *	
			 *	@param string $key A parent key, e.g. 'user'
			 *	@param array $arr The data associated
			 *	to the key
			 * 
			 *	@since 0.1.0
			 */
			public static function add( $key, $arr ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( isset($_SESSION[ self::$keychain ][ $key ] )){
					$_SESSION[ self::$keychain ][ $key ] = array_merge(
						$_SESSION[ self::$keychain ][ $key ], $arr
					);
				} else {
					$_SESSION[self::$keychain][ $key ] = $arr;
				}
			}
			
			/**
			 *	Get a session value
			 *	
			 *	@param string $key Get by specific key
			 *	
			 *	@since 0.1.0
			 */
			public static function get( $key = null ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( is_null($key) ){
					return $_SESSION[ self::$keychain ];
				}
				
				return $_SESSION[ self::$keychain ][ $key ];
				
			}
			
			/**
			 *	Clear a session value
			 *	
			 *	@param string $key Clear the session
			 *	by key
			 *	
			 *	@since 0.1.0
			 */
			public static function clear( $key = null ){
				
				if( !self :: $started ){
					throw 'Session has not been started yet.';
				}
				
				if( !$key ){
					$_SESSION[ self::$keychain ] = array();
				} else {
					unset( $_SESSION[ self::$keychain ][$key] );
				}
			}
			
			/**
			 *	Get or set the session Id
			 *	
			 *	@param int $set The Id to set
			 *	
			 *	@since 0.1.0
			 *	@return int The Id
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