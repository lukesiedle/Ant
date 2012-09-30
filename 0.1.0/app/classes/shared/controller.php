<?php

	/**
	 *	Controller handles lazy-loading
	 *	of methods based on context.
	 *	Handles similar functionality
	 *	for creating queries.
	 * 
	 *	@package Ant
	 *	@subpackage Controller
	 *	@since 0.1.0
	 */
	namespace Ant {
	
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
				
				$opt = explode( '.', strtolower($method) );
				
				$methodPath = '\Ant\\Controller\\' . implode('\\', $opt );				
				
				// If there's another option, it's a submethod //
				if( $opt[2] ){
					$subMethod = true;
				}
				
				// Try include the context class if it exists //
				if( ! class_exists($c = $opt[0], false ) ){
					$path = 'app/classes/context/' . $c . '.php';
					$hasClass = false;
					if( file_exists($path)){
						$hasClass = true;
						require_once( $path );
					}
				} else {
					$hasClass = true;
				}
				
				// Check if the method exists within the class (if there is a class) //
				if( $hasClass && method_exists('Ant\\'.$opt[0], $opt[1]) && ! $subMethod ){
					$methodPath = 'Ant\\'.$opt[0];
					$inClass = true;
					
				} else {
					$inClass = false;
					if( !function_exists($methodPath) ){
						$methodInclude = 'app/modules/context/controllers/' . implode( '/', $opt ) . '.php';
						if( file_exists($methodInclude)){
							require( $methodInclude );
						} else {
							throw new \Exception( $methodInclude . ' does not exist.', 0 );
						}
					}
				}
				
				$args['request'] = Router :: getRequestVars();
				
				if( $inClass ){
					return $methodPath :: $opt[1]( $args );
				}
				
				return $methodPath( $args );
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
			public static function query( $queryName, Array $args = array() ){
				
				$opt = explode( '.', strtolower($queryName) );
				
				$methodPath = '\Ant\\' . 'Query\\' . implode( '\\', $opt );
				
				// If there's another option, it's a submethod //
				if( $opt[2] ){
					$subMethod = true;
				}
				
				if( ! function_exists($methodPath) ){
					require_once('app/modules/context/queries/' . implode('/', $opt) . '.php' );
				}
				
				$args = array_merge( (array)Router :: getRequestVars(), $args );
				
				return $methodPath( new Query, Database :: getTablePrefix(), $args );
				
			}
			
		}
		
	}