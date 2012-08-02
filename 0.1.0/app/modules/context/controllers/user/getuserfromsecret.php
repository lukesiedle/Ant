<?php

	/*
	 *	Get the user based
	 *	on a secret token
	 *	
	 *	@package Ant
	 *	@subpackage User
	 *	@since 0.1.0 
	 */

	namespace Ant\Controller\User;
	
	use \Ant\Query as Query;
	use \Ant\Controller as Control;
	use \Ant\Database as Database;
	
	function getUserFromSecret( $vars ){
		
		// Load the base query //
		$query = Control :: query('User.getUser');
		
		$query->where( 'user.user_secret = :secret', array(
			'secret' => $vars['secret']
		));
		
		if( $collection = Database :: query($query) ){
			if( $collection->length() > 0 ){
				return $collection;
			}
			return false;
		}
		
		// No user was found //
		return false;
	}