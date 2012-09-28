<?php

	namespace Ant\Query\User\CRUD;
	
	/**
	 *	Read query for 
	 *	a user
	 * 
	 *	@param Query $query A new query object
	 *	@param string $_ The table prefix
	 *	@param array $args Arguments including 
	 *	'request'
	 * 
	 *	@since 0.1.0
	 */
	function read( $query, $_, $args ){
		
		return \Ant\Controller :: query('User.getUser')
			-> where( 'user.user_id = :id' , array(
				'id' => $args['resource']->getId()
			));
		
	}