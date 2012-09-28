<?php

	namespace Ant\Controller\User;
	
	/**
	 *	Controls permissions for the 
	 *	resource 'User'
	 *	
	 *	@param array $args Args include 
	 *	'request' object and Resource object
	 *	
	 *	@return array Permissions and readable 
	 *	fields
	 */
	function permission( $args ){
		
		$user = \Ant\User :: getCurrentUser();
		$isOwner = $user->getId() == $args['resource']->getId();
		
		$perms['allow'] = $isOwner;
		
		switch ( $args['task'] ){
			
			// Perform a create //
			case 'create' :
				$perms['allow'] = true;
			
			case 'read' :
				$perms['allow'] = true;
				$perms['owner'] = $isOwner;
				$perms['read'] = array(
					'user_id',
					'user_first_name',
					'user_last_name'
				);
				break;
				
			// User tasks //
			case 'update' :
			case 'delete' :
				break;
			
		}
		
		return $perms;
		
	}