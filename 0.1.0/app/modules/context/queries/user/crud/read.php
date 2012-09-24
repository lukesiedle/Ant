<?php

	
	/*
	 *	Read query for 
	 *	a user
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Query\User\CRUD;
	
	function read( $query, $_, $args ){
		
		return \Ant\Controller :: query('User.getUser')
			-> where( 'user.user_id = :id' , array(
				'id' => $args['resource']->data['id']
			));
		
	}