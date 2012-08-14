<?php
	
	/*
	 *	Generic array functions
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	
	namespace Library\Arr;
	
	function out( $data ){
		echo '<pre>' . print_r( $data, true ) . '</pre>';
	}
	
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

?>