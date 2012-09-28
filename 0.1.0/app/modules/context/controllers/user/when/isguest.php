<?php

	namespace Ant\Controller\User\When;
	
	use \Ant\User as User;
	
	/**
	 *	Determine if the user 
	 *	is a guest
	 * 
	 *	@since 0.1.0
	 *	@return bool True if guest or false
	 */
	function isGuest( $args ){
		return User :: getCurrentUser()->isGuest();
	}