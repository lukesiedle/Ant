<?php
	
	/*
	 *	Edit profile example view.
	 * 
	 *	@since 0.2.1
	 */

	namespace View\Desktop\User;
	
	use \Core\Controller as Controller;
	use \Core\Resource as Resource;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	use \Core\Request as Request;
	use \Core\Router as Router;
	
	// Context //
	use \Model\User as UserModel;
	
	function editProfile( $request ){
		
		$user = UserModel :: getCurrentUser();	
		
		// Kill the page if it's a guest //
		if( $user->isGuest() ){
			new \Core\Error('403', 'user_login_required');
		}
		
		// On retry //
		$errors			= Collection :: make('errors');
		$previous		= Collection :: make('previous');
		
		// Resource for the registration form //
		$resourceName	= 'resource/user/' . $user->getId();
		
		$register = new Collection(array(
			'title'			=> 'Edit Profile',
			'task'			=> 'update',
			'intention'		=> 'User.intentEditProfile',
			'resource'		=> $resourceName,
			'save'			=> 'Update',
			'token'			=> Request :: CSRFtoken( $resourceName )
		), 'register' );
		
		// Create the user resource //
		$rs	= new Resource( 'user', array(
			'id' => $user->getId()
		));
		
		// Read the user data //
		$userData	= $rs->read();
		
		// Pass the user if available //
		$user = new Collection( $userData, 'current' );
		
		// If there was persistent data //
		if( $persists = \Extension\Persistence :: get(
			Router :: getRequestURI())){
			
			// Show errors //
			if( is_array($persists['errors'] )){
				$errors->add( $persists['errors'] );
				
				// Show the last values entered //
				$previous->add( $persists['data'] );
				
				// Clear the user values //
				$user = Collection :: make('current');
			}
			
			// Show success //
			if( $persists['success'] ){
				$register->add(array(
					'success' => '<span style="color:green">Profile Updated!</span>'
				));
			}
		}
		
		return new CollectionSet( $register, $user, $errors, $previous );
		
	}