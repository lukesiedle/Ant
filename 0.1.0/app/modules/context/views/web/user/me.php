<?php
	
	
	/*
	 *	The self-user's 'profile' page
	 * 
	 *	@since 0.1.0
	 */
	 
	namespace Ant\Web\User;
	
	use \Ant\Application as App;
	use \Ant\Controller as Control;
	
	function me( $request ){
		
		$user = Control :: call('User.getCurrentUser');
		
	}