<?php

	namespace Ant\Controller\User\Registration;
	
	/**
	 *	Log the user out, destroying
	 *	their current session, and cookie.
	 *	
	 *	Redirects the user home
	 *	
	 *	@since 0.1.0
	 */
	function logout(){
		
		// Clear session and cookie //
		\Ant\Session :: clear( 'User' );
		\Ant\Cookie :: delete( 'User' );
		\Ant\Authentication :: clearSession();
		
		// Redirect home //
		\Ant\Application :: redirect();
	}
	