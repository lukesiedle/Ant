<?php

	/*
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
			
			/*
			 *	Call a method, requiring
			 *	if it doesn't exist. The method
			 *	may exist with the context class,
			 *	but it may be necessary to include
			 *	it from the controllers directory.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function call( $method, Array $args = array()){
				
				$opt = explode( '.', strtolower($method) );
				
				$methodPath = '\Ant\\Controller\\' . $opt[0] . '\\' . $opt[1];
				
				// If there's another option, it's a submethod //
				if( $opt[2] ){
					$methodPath .= '\\' . $opt[2];
					$subMethod = true;
				}
				
				// Try include the context class if it exists //
				if( !class_exists($c = $opt[0])){
					include_once('app/classes/context/' . $c . '/' . $c . '.php' );
				}
				
				// Check if the method exists within the class //
				if( method_exists('Ant\\'.$opt[0], $opt[1]) && ! $subMethod ){
					$methodPath = 'Ant\\'.$opt[0];
					$inClass = true;
				} else {
					$inClass = false;
					if( !function_exists($methodPath) ){
						require( 'app/modules/context/controllers/' . implode( '/', $opt ) . '.php' );
					}
				}
				
				$args['request'] = Router :: getRequestVars();
				
				if( $inClass ){
					return $methodPath :: $opt[1]( $args );
				}
				return $methodPath( $args );
			}
			
			/*
			 *	Call a query
			 *	
			 *	@since 0.1.0
			 */
			
			public static function query( $queryName, Array $args = array() ){
				
				$opt = explode( '.', strtolower($queryName) );
				
				$namespace = '\Ant\\' . 'Query\\'. $opt[0]. '\\';
				
				$pathMethod = $namespace.$opt[1];
				
				if( !function_exists($pathMethod) ){
					require_once('app/modules/context/queries/' . $opt[0] . '/' . $opt[1] . '.php');
				}
				
				$args = array_merge( (array)Router :: getRequestVars(), $args );
				
				return $pathMethod( new Query, Database :: getTablePrefix(), $args );
				
			}
			
		}
		
	}