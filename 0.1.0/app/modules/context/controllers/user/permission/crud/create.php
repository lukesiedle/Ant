<?php

	/*
	 *	Get permissions of task
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Controller\User\Permission\CRUD;
	
	function create( $args ){
		
		switch( $args['intention'] ){
			// Guest has permission to register //
			case 'User.registration.register' :
				return true;
				break;
		}
		return false;
	}