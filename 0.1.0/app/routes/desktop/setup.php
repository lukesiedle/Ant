<?php
	
	/**
	 *	The route is called and returns
	 *	a set of vars which help the 
	 *	app define it's current purpose.
	 * 
	 *	@since 0.2.1
	 */
	
	namespace Route\Setup;
	
	function index( $vars ){
		return array(
			'module'		=> 'index',
			'template'		=> 'index',
			'frame'			=> 'frame',
		);
	}