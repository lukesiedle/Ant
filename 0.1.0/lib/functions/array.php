<?php
	
	/**
	 *	Generic array functions
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	namespace Library\Arr;
	
	/**
	 *	Outputs an array or object
	 *	in pre tags
	 *	
	 *	@param mixed $data The data to 
	 *	output to the screen
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	function out( $data ){
		echo '<pre>' . print_r( $data, true ) . '</pre>';
	}
	
	/**
	 *	Strips slashes within strings in 
	 *	an array.
	 *	
	 *	@param array $data 
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 *	@return array The stripped strings
	 */
	function stripslashes( & $data ){
		foreach( $data as $i => $str ){
			if(is_array($str)){
				stripslashes( $data );
				continue;
			}
			
			$data[ $i ] = stripslashes( $str );
		}
		return $data;
	}