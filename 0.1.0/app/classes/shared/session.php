<?php

	namespace Ant {
		
		Class Session {
			
			public static $session = array();
			
			public static function init(){
				session_start();
				if( isset($_SESSION['ning'])){
					self :: $session = $_SESSION['ning'];
				}
			}
			
			public static function add( $key, $obj ){
				if( isset(self :: $session[$key] )){
					self :: $session[ $key ] = array_merge_recursive(
						self :: $session[$key], $obj
					);
				} else {
					self :: $session[ $key ] = $obj;
				}
				
				self :: write();
			}
			
			public static function get( $key, $object = true ){
				if( $object ){
					return (object) self :: $session[ $key ];
				}
				return self :: $session[ $key ];
			}
			
			public static function clear( $key = null ){
				if( !$key ){
					self :: $session = array();
				} else {
					unset( self :: $session[$key] );
				}
				self :: write();
			}
			
			public static function write(){
				
				if(session_id() == ""){
					throw new Exception("Cannot write session. Session has not been started.");
				}
				
				foreach( self :: $session as $key => $obj ){
					$_SESSION['ning'][ $key ] = $obj;
				}
				
			}
			
			public static function id( $set = false ){
				if( ! $set ){
					return session_id();
				}
				session_id( $set );
			}
			
		}
		
		
	}


?>