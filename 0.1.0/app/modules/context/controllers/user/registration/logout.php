<?php

	/*
	 *	Log the user out, destroying
	 *	their current session, and cookie.
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Controller\User\Registration;
	
	function logout(){
		
		// Clear session and cookie //
		\Ant\Session :: clear( 'Ant.User' );
		\Ant\Cookie :: delete( 'Ant.User' );
		
		// Redirect home //
		\Ant\Application :: redirect();
	}
	