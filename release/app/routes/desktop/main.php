<?php
	
	/**
	 *	Context : home
	 *	@since 0.2.1
	 */
	
	namespace Route\Main;
	
	function index( $vars = array() ){
		return array(
			'module'		=> 'index',
			'template'		=> 'index',
			'frame'			=> 'frame',
		);
	}