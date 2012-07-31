<?php

	namespace Ant;
	
	Class UserRegister extends User { 
		
		/*
		 *	Creates a collection
		 *	from the request, 
		 *	and saves.
		 */
		
		public static function register( $request ){
			$user = new self( new Collection($request, 'user') );
			
			// Saves the available data to the database //
			$user->save();
			
			return $user;
		}
		
	}

?>