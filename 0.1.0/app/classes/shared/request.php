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
			
			/*
			 *	Process the request data
			 *	and store in memory
			 *	
			 *	@since 0.1.0
			 */
			
			public static function initialize( $getRequest ){
				
				$post		= $_POST;
				$get		= $getRequest;
				
				// Remove auto-escaping //
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