<?php
	
	/*
	 *	Registration view
	 *	Passes CSRF token
	 *	and form action to
	 *	view.
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant\Desktop\User;
	
	use \Core\Controller as Controller;
	use \Core\Resource as Resource;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	use \Core\Request as Request;
	
	// Context //
	use \Model\User as UserModel;
	
	function register( $request ){
		
		// Resource for the registration form //
		$resource = 'resource/user';
		
		$tpl = array();
		$userData = array();
		
		switch( true ){
			case isset( $request->edit ) :
				
				$user = UserModel :: getCurrentUser();
				
				$tpl['title']		= 'Edit Profile';
				$tpl['task']		= 'update';
				$tpl['intention']	= 'User.intentEditProfile';
				$tpl['resource']	= 'resource/user/' . $user->getId();
				$tpl['save']		= 'Update';
				
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
				
			default : 
				$tpl['title']		= 'Sign Up';
				$tpl['task']		= 'create';
				$tpl['intention']	= 'User.intentRegister';
				$tpl['resource']	= 'resource/user';
				$tpl['save']		= 'Register';
				break;
		}
		
		// Create or get csrf token for this form's resource //
		$tpl['token'] = Request :: CSRFtoken( $tpl['resource'] );
		
		// Pass the user if available (for edit profile) //
		$user = new Collection( $userData, 'user' );
		
		// Pass to the template via collection //
		$register = new Collection( $tpl, 'register' );
		
		return new CollectionSet( $register, $user );
		
	}