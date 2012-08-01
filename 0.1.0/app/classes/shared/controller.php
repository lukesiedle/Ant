<?php

	/*
	 *	Controller handles lazy-loading
	 *	of methods based on context.
	 *	Calls methods statically 
	 *	or creates objects.
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
			 *	@note If an initialize method exists
			 *	in the class, an instance will be
			 *	created and returned, otherwise
			 *	the method is called statically.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function call( $method, Array $args = array()){
				
				$options	= explode( '.', strtolower($method) );
				
				$methodPath = '\Ant\\Controller\\' . $options[0] . '\\' . $options[1];
				
				if( !function_exists($methodPath) ){
					require_once('app/modules/context/controllers/' . $options[0] . '/' . $options[1] . '.php');
				}
				
				$args['request'] = Router :: getRequestVars();
				
				return $methodPath( $args );
			}
			
			/*
			 *	Call a query, requiring
			 *	if it doesn't exist. The method
			 *	may exist in the shared space,
			 *	or the context space.
			 * 
			 *	@note If the shared space 
			 *	contains the method, the 
			 *	
			 *	@since 0.1.0
			 */
			
			public static function query( $context, $method, Array $args = array() ){
				
				$namespace = '\Ant\\' . 'Query\\'.$context . '\\';
				
				$pathMethod = $namespace.$method;
				
				if( !function_exists($pathMethod) ){
					require_once('app/modules/shared/queries/' . $context . '/' . $context . '.php');
				}
				
				$args = array_merge( (array)Router :: getRequestVars(), $args );
				
				return $pathMethod( new Query, Database :: getTablePrefix(), $args );
				
			}
			
		}
		
	}