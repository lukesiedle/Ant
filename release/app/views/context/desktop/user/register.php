<?php
	
	/*
	 *	Registration example view
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
		
		// On retry //
		$errors			= Collection :: make('errors');
		$previous		= Collection :: make('previous');
		
		$register->add(array(
			'title'			=> 'Sign Up',
			'task'			=> 'create',
			'intention'		=> 'User.intentRegister',
			'resource'		=> $resourceName,
			'action'		=> $resourceName,
			'save'			=> 'Register'
		));
		
		// Create or get csrf token for this form's resource //
		$register->add(array(
			'token' => Request :: CSRFtoken( $resourceName )
		));
		
		if( $persists = \Extension\Persistence :: get(
			Router :: getRequestURI())){
			
			// Show errors //
			if( is_array($persists['errors'] )){
				$errors->add( $persists['errors'] );
			}
			
			// Load previous data into page //
			if( is_array($persists['data'] )){
				$previous->add( $persists['data'] );
			}
		}
		
		// Pass the user if available (for edit profile) //
		$user = new Collection( $userData, 'user' );
		
		return new CollectionSet( $register, $user, $errors, $previous );
		
	}