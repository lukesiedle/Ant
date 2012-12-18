<?php
	
	/**
	 *	The Main route
	 *	@since 0.2.1
	 */
	
	namespace Route\Main;
	
	// Home / Default //
	function index( $vars = array() ){
		return array(
			'module'		=> 'index',
			'template'		=> 'index',
			'frame'			=> 'frame',
		);
	}