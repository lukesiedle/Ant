<?php
	
	/*
	 *	Registration view
	 *	Passes CSRF token
	 *	and form action to
	 *	view.
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Web\User;
	
	function register( $request ){
		
		// Resource for the registration form //
		$resource = 'resource/user';
		
		$tpl = array();
		$userData = array();
		
		switch( true ){
			case isset( $request->id ) :
				
				// throw new \Exception('', 403 );
				
				$tpl['title']		= 'Edit Profile';
				$tpl['task']		= 'update';
				$tpl['intention']	= 'User.registration.editProfile';
				$tpl['resource']	= 'resource/user/' . $request->id;
				$tpl['save']		= 'Update';
				
				// Create the user resource //
				$rs		= new \Ant\Resource( 'user', array(
					'id' => $request->id
				));
				
				// Read the user data //
				$userData	= $rs->read();
				
				break;
			default : 
				$tpl['title']		= 'Sign Up';
				$tpl['task']		= 'create';
				$tpl['intention']	= 'User.registration.register';
				$tpl['resource']	= 'resource/user';
				$tpl['save']		= 'Register';
				break;
		}
		
		// Create or get csrf token for this form's resource //
		$tpl['token'] = \Ant\Request :: CSRFtoken( $tpl['resource'] );
		
		// Pass the user if available (for edit profile) //
		$user = new \Ant\Collection( $userData, 'user' );
		
		// Pass to the template via collection //
		$register = new \Ant\Collection( $tpl, 'register' );
		
		return new \Ant\CollectionSet( $register, $user );
		
	}