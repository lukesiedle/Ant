<?php
	
	/**
	 *	HTTP Requests wrapper
	 *	for consistency.
	 *	
	 *	@package Core
	 *	@subpackage Request
	 *	@since 0.1.0
	 */
	namespace Core {
		
		Class Request {
			
			public static $data = array();
			public static $csrf;
			
			/**
			 *	Process the request data
			 *	and store in memory
			 *	
			 *	@param array $getRequest The key value 
			 *	pairs of the get request
			 * 
			 *	@since 0.1.0
			 */
			public static function initialize( $getRequest ){
				
				$post		= $_POST;
				$get		= $getRequest;
				
				// Remove PHP auto-escaping//
				if( get_magic_quotes_gpc() ){
					\Library\Arr\stripslashes( $post );
					\Library\Arr\stripslashes( $get );
				}
				
				$request	= array_merge( $get, $post );
				
				self :: $data = array(
					'post'		=> $post,
					'get'		=> $get,
					'request'	=> $request,
					'file'		=> $_FILES
				);
				
			}
			
			/**
			 *	Get or set a CSRF token
			 *	for a particular resource
			 *	
			 *	@param string $resource The resource, 
			 *	'resource/article/22', 'resource/user/34'
			 *	
			 *	@since 0.1.0
			 *	@return string The token
			 */
			public static function CSRFtoken( $resource ){
				if( !isset( self :: $csrf[ $resource ] )){
					self :: $csrf[ $resource ] = md5( date('U') . rand(99, 199) );
				}
				return self :: $csrf [ $resource ];
			}
			
			/**
			 *	Set the CSRF tokens
			 *	e.g. via the session
			 * 
			 *	@param array $array The array of csrf tokens, 
			 *	usually set via the session.
			 * 
			 *	@since 0.1.0
			 */
			public static function setCSRF( $array ){
				self :: $csrf = $array;
			}

			/**
			 *	Get all the CSRF tokens
			 * 
			 *	@since 0.1.0
			 *	@return array The tokens
			 */
			public static function getCSRF(){
				return self :: $csrf;
			}
			
			/**
			 *	Get the request by type
			 *	and optional key.
			 * 
			 *	@param string $type The request type,
			 *	'POST', 'GET', 'REQUEST'
			 *	@param string $key The specific item to 
			 *	return.	
			 *	
			 *	@since 0.1.0
			 *	@return array The request data
			 */
			public static function get( $type = 'request', $key = null ){
				$data = self :: $data[ strtolower($type) ];
				if( $key ){
					return $data[ $key ];
				}
				return $data;
			}
			
		}
		
	}