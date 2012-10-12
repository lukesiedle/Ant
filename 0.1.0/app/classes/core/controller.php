<?php

	/**
	 *	Controller handles lazy-loading
	 *	of methods based on context.
	 *	Handles similar functionality
	 *	for creating queries.
	 * 
	 *	@package Core
	 *	@subpackage Controller
	 *	@since 0.1.0
	 */
	namespace Core {
	
		Class Controller {
			
			/**
			 *	Call a method, requiring
			 *	if it doesn't exist. The method
			 *	may exist with the context class,
			 *	or it will be included 
			 *	from the controllers directory.
			 * 
			 *	Couples with Database class to get 
			 *	the table prefix.
			 *	
			 *	@params string $method A period-separated string
			 *	@params array $args The arguments to be passed.
			 *	Request is always passed in by default $args['request']			 
			 * 
			 *	@since 0.1.0
			 *	@return mixed The result
			 */
			public static function call( $method, Array $args = array()){
				$parts = explode('.', $method );
				$method = '\Controller\\' . $parts[0];
				$args['request'] = Router :: getRequestVars();
				return $method :: $parts[1]( $args );
			}
			
			/**
			 *	Call a query method. Lazy-loads
			 *	a method defined in app/modules/context/queries
			 *	
			 *	@params string $method A period-separated string
			 *	@params array $args The arguments to be passed.
			 *	Request is always passed in by default $args['request']			 
			 * 
			 *	@since 0.1.0
			 *	@return Query The query
			 */
			public static function query( $method, Array $args = array() ){
				$parts = explode('.', $method );
				$method = '\Query\\' . $parts[0];
				return $method :: $parts[1]();
			}
			
		}
		
	}