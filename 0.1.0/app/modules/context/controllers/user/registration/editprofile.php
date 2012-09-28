<?php

	namespace Ant\Controller\User\Registration;
	
	use \Ant\User as User;
	use \Ant\Application as App;
	
	/**
	 *	An intention controller, used
	 *	after a user resource is 
	 *	updated.	
	 *	
	 *	If the resource was not created using
	 *	Ajax the application issues a redirect
	 *	to specify the profile was saved.	
	 * 
	 *	@since 0.1.0	
	 */
	function editProfile( $args ){
		if( ! $args['is_ajax'] ){
			App :: redirect( 'user/edit/?saved' );
		}
	}
	