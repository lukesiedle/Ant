<?php

	/**
	 *	Cookie handling class
	 * 
	 *	@package Core
	 *	@subpackage Cookie
	 *	@since 0.1.0
	 */
	namespace Core {
		 
		Class Cookie {
			
			/**
			 *	Get a cookie by name,
			 *	decode json and return
			 *	
			 *	@since 0.1.0
			 *	@return mixed The cookie data
			 */
			public static function get( $name = null ){
				if( is_null($name) ){
					return $_COOKIE;
				} else {
					if( ! isset($_COOKIE[ $name ])){
						return false;
					}
					$cookie = json_decode( $_COOKIE[ $name ], true );
					return $cookie[ 'data' ];
				}
			}
			
			/**
			 *	Set a cookie by name
			 *	and encode it with json	
			 * 
			 *	@param string $name The cookie name
			 *	@param mixed $data The data to encode
			 *	@param int $expires The unix timestamp of expiry
			 * 
			 *	@since 0.1.0
			 */
			public static function set( $name, $data = '', $expires = null ){
				
				$data = json_encode(array( 'data' => $data ));
				
				if( ! $expires ){
					// Expires in two days //
					$expires = date('U') + (2*24*60*60);
				}
				
				\setcookie($name, $data, $expires );
			}
			
			/**
			 *	Delete a cookie (expire)
			 *
			 *	@param string $name The cookie name	
			 * 
			 *	@since 0.1.0
			 */
			public static function delete( $name ){
				\setcookie( $name, '', date('U') - (7*24*60*60));
			}
			
		}
	}
	