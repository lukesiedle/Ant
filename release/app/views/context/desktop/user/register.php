<?php
	
	/*
	 *	Registration view
	 *	Passes CSRF token
	 *	and form action to
	 *	view.
	 * 
	 *	@since 0.1.0
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
	
	function register( $request ){
		
		// Resource for the registration form //
		$resourceName	= 'resource/user';
		
		// Collection for template //
		$register		= Collection :: make('register');
		$errors			= Collection :: make('errors');
		
		switch( true ){
			// The signup view //
			default : 
				$register->add(array(
					'title'			=> 'Sign Up',
					'task'			=> 'create',
					'intention'		=> 'User.intentRegister',
					'resource'		=> $resourceName,
					'action'		=> $resourceName,
					'save'			=> 'Register'
				));
				break;
				
			// The edit view //
			case isset( $request->edit ) :
				
				$user = UserModel :: getCurrentUser();
				
				$resourceName  .= '/' . $user->getId();
				
				$register->add(array(
					'title'			=> 'Edit Profile',
					'task'			=> 'update',
					'intention'		=> 'User.intentEditProfile',
					'resource'		=> $resourceName,
					'save'			=> 'Update'
				));
				
				// Kill the page if it's a guest //
				if( $user->isGuest() ){
					throw new \Exception('You need to be logged in to access this page.', 403 );
				}
				
				// Create the user resource //
				$rs	= new Resource( 'user', array(
					'id' => $user->getId()
				));
				
				// Read the user data //
				$userData	= $rs->read();
				
				if( isset($_GET['saved'] )){
					$tpl['success']		= '<span style="color:green">Profile Updated!</span>';
				}
				
				break;
		}
		
		// Create or get csrf token for this form's resource //
		$register->add(array(
			'token' => Request :: CSRFtoken( $resourceName )
		));
		
		if( $err = \Extension\Persistence :: get(
			Router :: getRequestURI())){
			$errors->add( $err );
		}
		
		// Pass the user if available (for edit profile) //
		$user = new Collection( $userData, 'user' );
		
		return new CollectionSet( $register, $user, $errors );
		
	}