<?php

	/*
	 *	Registers the user
	 *	based on request data
	 * 
	 *	@since 0.1.0
	 *	
	 */

	namespace Ant\Controller\User\Registration;
	
	use \Ant\User as User;
	use \Ant\Application as App;
	
	function editProfile( $args ){
		if( ! $args['is_ajax'] ){
			App :: redirect( 'user/edit/?saved' );
		}
	}
	