<?php

	/*
	 *	Generic string functions
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */

	namespace Library\String;
	
	/**
	 *	Generate hash from 
	 *	string for passwords
	 *	
	 *	@param string $str
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	function encodePassword( $str ){
		return hash( 'sha256', $str );
	}