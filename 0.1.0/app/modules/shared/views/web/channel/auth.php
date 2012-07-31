<?php
	
	namespace Ant\Web\Channel\Auth {
		
		use \Ant\Authentication as Auth;
		use \Ant\Template as Template;
		
		function index( $request ){
			
			// Load a template from the shared space
			// $since 0.1.0 //
			$frame		= Tpl :: loadSharedTemplate('auth');
			
			Auth :: authFacebook();
			
			// Buffer the template for output
			// $since 0.1.0 //
			Template :: buffer( $frame );
			
			// Channel is always responsible for output
			// $since 0.1.0 //
			echo Template :: output();
			
		}
		
	}

?>