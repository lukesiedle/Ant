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
					'request'	=> $request
				);
				
			}
			
			public static function get( $type = 'request' ){
				return self :: $data[ $type ];
			}
			
		}
		
	}