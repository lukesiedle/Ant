<?php
	
	/*
	 *	Configuration
	 * 
	 *	@package Ant
	 *	@subpackage Configuration
	 *	@since 0.1.0	
	 */

	namespace Ant {
	
		Class Configuration {

			public static $data = array();

			/*
			 *	Extend the configuration
			 *	data
			 * 
			 *	@since 0.1.0
			 */
			
			public static function set( $arr ){
				self :: $data = array_merge(
					self :: $data, $arr	
				);
			} 

			/*
			 *	Get the configuration
			 *	data
			 * 
			 *	@since 0.1.0
			 *	@return array The configuration data
			 */
			
			public static function get( $key = null ){
				if( $key ){
					return (object) self :: $data[ $key ];
				}
				return self :: $data;
			}
		}
	
	}