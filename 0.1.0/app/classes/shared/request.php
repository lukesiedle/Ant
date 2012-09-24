<?php
	
	/*
	 *	Wrapper class for 
	 *	handling requests
	 *	
	 *	@package Ant
	 *	@subpackage Request
	 *	@since 0.1.0
	 */
	
	namespace Ant {
		
		Class Request {
			
			public static $data = array();
			public static $csrf;
			
			/*
			 *	Process the request data
			 *	and store in memory
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
			
			/*
			 *	Get or set a CSRF token
			 *	for a particular resource
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
			
			/*
			 *	Set the CSRF tokens
			 *	e.g. via the session
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setCSRF( $array ){
				self :: $csrf = $array;
			}

			/*
			 *	Get all the CSRF tokens
			 * 
			 *	@since 0.1.0
			 *	@return array The tokens
			 */
			
			public static function getCSRF(){
				return self :: $csrf;
			}
			
			/*
			 *	Get the request by type
			 *	and optional key.
			 * 
			 *	@since 0.1.0
			 *	@return array The request data
			 */
			
			public static function get( $type = 'request', $key = null ){
				$data = self :: $data[ $type ];
				if( $key ){
					return $data[ $key ];
				}
				return $data;
			}
			
		}
		
	}