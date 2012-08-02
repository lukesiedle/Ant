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
			
			public static $session = array();
			
			/*
			 *	Start the session
			 *	and load in memory
			 *	
			 *	@since 0.1.0
			 */
			
			public static function init(){
				session_start();
				if( isset($_SESSION['ant'])){
					self :: $session = $_SESSION['ant'];
				}
			}
			
			/*
			 *	Extend the session
			 *	
			 *	@since 0.1.0
			 */
			
			public static function add( $key, $obj ){
				if( isset(self :: $session[$key] )){
					self :: $session[ $key ] = array_merge(
						self :: $session[$key], $obj
					);
				} else {
					self :: $session[ $key ] = $obj;
				}
				
				self :: write();
			}
			
			/*
			 *	Get a session value
			 *	
			 *	@since 0.1.0
			 */
			
			public static function get( $key = null ){
				if( is_null($key) ){
					return self :: $session;
				}
				return self :: $session[ $key ];
			}
			
			/*
			 *	Clear a session value
			 *	
			 *	@since 0.1.0
			 */
			
			public static function clear( $key = null ){
				if( !$key ){
					self :: $session = array();
				} else {
					unset( self :: $session[$key] );
				}
				self :: write();
			}
			
			/*
			 *	Write the value in memory
			 *	to the session	
			 * 
			 *	@since 0.1.0
			 */
			
			public static function write(){
				
				if( session_id() == "" ){
					throw new Exception("Cannot write session. Session has not been started.");
				}
				
				foreach( self :: $session as $key => $obj ){
					$_SESSION['ant'][ $key ] = $obj;
				}
				
			}
			
			/*
			 *	Get or set the session Id
			 *	
			 *	@since 0.1.0
			 */
			
			public static function id( $set = false ){
				if( ! $set ){
					return session_id();
				}
				session_id( $set );
			}
			
		}
		
		
	}