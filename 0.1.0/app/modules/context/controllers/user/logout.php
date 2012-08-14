<?php

	/*
	 *	Log the user out, destroying
	 *	their current session.
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Controller\User;
	
	function logout(){
		
		\Ant\Session :: clear( 'Ant.User' );
		\Ant\Cookie :: delete( 'Ant.User' );
		
		\Ant\Application :: redirect();
	}
	