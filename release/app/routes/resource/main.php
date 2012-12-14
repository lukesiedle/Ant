<?php
	
	/**
	 *	The Main route
	 *	@since 0.2.1
	 */
	
	namespace Route\Main;
	
	function index( $vars = array() ){
		
		$return = array(
			'module'		=> 'index',
			'template'		=> 'index',
			'frame'			=> 'frame',
		);
		
		if( isset($vars[1]) ){
			$return['resource'] = $vars[1];
		}
		
		if( isset($vars[2]) ){
			$return['id'] = $vars[2];
		}
		
		if( isset($vars[3]) ){
			$return['sub_resource'] = $vars[3];
		}
		
		if( isset($vars[4]) ){
			$return['sub_resource_id'] = $vars[4];
		}
		return $return;
	}