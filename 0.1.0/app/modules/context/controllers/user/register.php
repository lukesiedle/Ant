<?php

	namespace Ant\Controller\User;
	
	function register( $request ){
		
		$user = new self( new Collection($request, 'user') );
		
		$user->save();

		return $user;
	}