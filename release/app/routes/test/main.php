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
	
	function deepest(){
		return array(
			'module'		=> 'deepest',
			'template'		=> 'deepest',
			'frame'			=> 'frame',
		);
	}
	
	function testframe(){
		return array(
			'module'		=> 'index',
			'template'		=> 'index',
			'frame'			=> 'testframe',
		);
	}
	
	function loopframe(){
		return array(
			'module'		=> 'index',
			'template'		=> 'index',
			'frame'			=> 'frame',
		);
	}