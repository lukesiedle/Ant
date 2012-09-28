<?php

	namespace Ant\Controller\User\Get;
	
	use \Ant\Query as Query;
	use \Ant\Controller as Control;
	use \Ant\Database as Database;
	
	/**
	 *	Get the user based
	 *	on a secret token
	 *	
	 *	@param array $vars The data
	 *	which requires 'secret' be
	 *	passed inside.
	 *	
	 *	@package Ant
	 *	@subpackage User
	 *	@since 0.1.0
	 *	@return bool false | Collection The user data
	 */
	function userFromSecret( $vars ){
		
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