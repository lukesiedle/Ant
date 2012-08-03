<?php
	
	/*
	 * 
	 *	Shared view 'frame' for performing
	 *	global client view tasks specific
	 *	to the application.
	 *	
	 *	@package Ant
	 *	@type Shared
	 *	@since 0.1.0
	 * 
	 */

	namespace Ant\Web {
		
		use \Ant\Collection as Collection;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Control;
		use \Ant\Database as Database;
		use \Ant\Authentication as Auth;
		
		// Context Classes //
		use \Ant\User as User;
		
		function frame(){
			
			Control :: call('User.initialize');
			
			/*
			Control :: call('User.authorize', array(
				'type' => 'google'
			));
			*/
		}
		
	}