<?php

	/**
	 *	Allows for persistent data to be 
	 *	passed between pages using
	 *	the session. But destroys it 
	 *	thereafter for memory conservation.
	 * 
	 *	@package Persistence
	 *	@since 0.2.1
	 */
	namespace Extension {
		
		Class Persistence {
			
			public static $persists = array(),
					$isLoaded = false;
			
			/**
			 *	Save path in memory and associate	
			 *	data
			 * 
			 *	@param $path The requestURI
			 *	@param $data The data to save in memory
			 *			 
			 *	@since 0.2.1
			 */
			public static function save( $path, $data ) {
				self :: $persists[ $path ] = $data;
				self :: store();
			}
			
			/**
			 *	Clear path data or all persistent data
			 * 
			 *	@param $path The requestURI
			 *			 
			 *	@since 0.2.1
			 */
			public static function clear( $path = null ){
				if( is_null($path) ){
					self :: $persists = array();
				} else {
					unset( self :: $persists[ $path ] );
				}
				
				self :: store();
			}
			
			/**
			 *	Save path data to the session
			 *		
			 *	@since 0.2.1
			 */
			public static function store(){
				\Core\Session :: clear( 'extension.persistance' );
				\Core\Session :: add( 'extension.persistance', self :: $persists );
			}
			
			/**
			 *	Get path data or all persistent data
			 *	from the session/memory
			 *
			 *	@param $path The request URI
			 *
			 *	@return array The data
			 * 
			 *	@since 0.2.1
			 */
			public static function get( $path = null ){
				
				if( ! $isLoaded ){
					self :: $persists = \Core\Session :: get('extension.persistance');
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