<?php

	namespace Controller;
	
	use \Core\Request as Request;
	use \Core\Application as App;
	use \Core\Resource as Resource;
	use \Core\Document as Document;
	use \Core\Router as Router;
	use \Core\Controller as Controller;
	
	/**
	 *	The Application controller.
	 *	
	 */
	class Application {
		
		/**
		 *	Checks if the app is local
		 * 
		 *	@since 0.1.0
		 *	@return bool 
		 */
		static function isLocal(){
			return App :: get()->local;
		}
		
	}