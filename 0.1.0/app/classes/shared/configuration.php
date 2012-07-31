<?php
	
	namespace Ant {
	
		Class Configuration {

			public static $data = array();

			public static function set( $arr ){
				self :: $data = array_merge(
					self :: $data, $arr	
				);
			} 

			public static function get( $key = null ){
				if( $key ){
					return (object) self :: $data[ $key ];
				}
				return self :: $data;
			}
		}
	
	}

?>