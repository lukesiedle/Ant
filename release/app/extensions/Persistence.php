<?php

	/**
	 *	Allows for persistent data to be 
	 *	passed between pages using
	 *	the session. But destroys it 
	 *	thereafter for memory conservation.
	 * 
	 *	@package Persistence
	 *	@since 0.1.0
	 */
	namespace Extension {
		
		Class Persistence {
			
			public static $persists = array(),
					$isLoaded = false;
			
			public static function save( $path, $data ) {
				self :: $persists[ $path ] = $data;
				self :: store();
			}
			
			public static function clear( $path = null ){
				if( is_null($path) ){
					self :: $persists = array();
				} else {
					unset( self :: $persists[ $path ] );
				}
				
				self :: store();
			}
			
			public static function store(){
				\Core\Session :: clear( 'extension.persists' );
				\Core\Session :: add( 'extension.persists', self :: $persists );
			}
			
			public static function get( $path = null ){
				
				if( ! $isLoaded ){
					self :: $persists = \Core\Session :: get('extension.persists');
				}
				
				if( is_null($path) ){
					return self :: $persists;
				}
				
				$persists = self :: $persists[ $path ];
				
				// Destroy history for the sake of memory //
				self :: clear( $path );
				
				return $persists;
			}
		}
	}