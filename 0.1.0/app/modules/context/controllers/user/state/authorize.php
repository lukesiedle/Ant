<?php

	namespace Ant\Controller\User\State;
	
	/**
	 *	Authorize the user by type
	 *	with return url to home.	
	 * 
	 *	@param array $vars The
	 *	arguments, including the type
	 *	of authorization 'google' 'facebook'
	 *	
	 *	@since 0.1.0
	 */
	function authorize( $vars ){
		
		if( !isset($vars['type']) ){
			throw new \Exception('You must specify a type of authorization, e.g. \'google\'');
		}
		
		\Ant\Authentication :: authorize( 
			$vars['type'] , 
			\Ant\Router :: getPublicRoot() . 'home'
		);
	}
