<?php

	/*
	 *	Determine if the user 
	 *	is a guest
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Controller\User\When;
	
	use \Ant\User as User;
	
	function isGuest( $args ){
		return User :: getCurrentUser()->isGuest();
	}