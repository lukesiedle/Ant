<?php
	
	/**
	 *	Wrapper for storing configuration,
	 *	for consistency.
	 *	
	 *	@package Core
	 *	@subpackage Configuration
	 *	@since 0.1.0	
	 */
	namespace Core {
		
		Class Configuration {

			public static $data = array();

			/**
			 *	Extend the configuration
			 *	data
			 * 
			 *	@params array $arr Key-Value pairs
			 * 
			 *	@since 0.1.0
			 */
			public static function set( $arr ){
				self :: $data = array_merge(
					self :: $data, $arr	
				);
			} 

			/**
			 *	Get the configuration
			 *	data, as an array
			 * 
			 *	@params string $key Filter the result by key
			 * 
			 *	@since 0.1.0
			 *	@return array The configuration data
			 */
			public static function get( $key = null ){
				if( $key ){
					return self :: $data[ $key ];
				}
				return self :: $data;
			}
		}
	
	}