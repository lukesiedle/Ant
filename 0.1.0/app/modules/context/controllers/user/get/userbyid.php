<?php
	
	namespace Ant\Controller\User\Get;
	
	use \Ant\Controller as Controller;
	
	/**
	 *	Get the user (or set of users) 
	 *	by Id
	 * 
	 *	@param array $vars Data passed to
	 *	the controller. Requires an id is
	 *	passed as an int or array.
	 * 
	 *	@since 0.1.0
	 *	@return Collection The user(s)
	 */
	function userById( $vars ){
		
		if( ! isset($vars['id'])){
			throw new Exception( 'User Id must be specified' );
		}
		
		if( ! is_array($vars['id'])){
			(array) $vars['id'];
		}
		
		$query = Controller :: query('User.getUser')
			->where('user.user_id IN( :ids )', array(
				'ids' => implode( ',', $ids )
			));
		
		return \Ant\Database :: query( $query );
	}
	
