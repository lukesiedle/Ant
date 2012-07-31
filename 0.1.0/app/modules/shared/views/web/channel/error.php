<?php
	
	namespace Ant\Web\Channel\Error {
		
		use \Ant\Template as Template;
		
		function index( $request ){
			
			// Load a template from the shared space
			// @since 0.1.0 //
			$frame		= Template :: loadSharedTemplate('frame');
			
			Template :: loadSharedTemplate('error')->loadInto( $frame, '__CONTENT__' );
			
			// Buffer the template for output
			// $since 0.1.0 //
			Template :: buffer( $frame );
			
			// Channel is always responsible for output
			// $since 0.1.0 //
			echo Template :: output();
		}
	
	}

?>