<?php
	
	/*
	 *	Get the user (or set of users) 
	 *	by Id
	 * 
	 *	@since 0.1.0
	 *	@return Collection The user(s)
	 */

	namespace Ant\Controller\User;
	
	use \Ant\Controller as Control;
	
	function getUserById( $vars ){
		
		if( ! isset($vars['id'])){
			throw new Exception( 'User Id must be specified' );
		}
		
		if( ! is_array($vars['id'])){
			(array) $vars['id'];
		}
		
		$query = Control :: query('User.getUser')
			->where('user.user_id IN( :ids )', array(
				'ids' => implode( ',', $ids )
			));
		
		return Database :: query( $query );
	}
	
