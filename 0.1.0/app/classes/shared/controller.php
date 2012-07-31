<?php

	namespace Ant {
	
		Class Controller {
			
			/*
			 *	@description
			 *	Calls a method statically, 
			 *	loads in the required 
			 *	classes to execute
			 *	the method
			 */
			
			public static function call( $context, $classMethod, Array $args = array()){
				
				$class = '\Ant\\' . $context;
				
				if( !class_exists($c = $context) ){
					require_once('app/classes/context/' . $c . '/' . $c . '.php');
				}
				
				if( !method_exists($class, $classMethod)){
					if( !class_exists( $c = $context . $classMethod )){
						require_once('app/modules/context/controllers/' 
							. $context . '/' 
							. $classMethod . '.php');
					}
					$class = '\Ant\\' . $context . $classMethod;
				} else {
					$class = '\Ant\\' . $context;
				}
				
				$args['request'] = Router :: getRequestVars(); 
				
				if( method_exists($class, 'initialize')){
					$instance = new $class( $args );
					$instance->initialize( $args );
					return $instance;
				} else {
					return $class :: $classMethod( $args );
				}
				
			}
			
			public static function query( $context, $classMethod, Array $args = array() ){
				
				$class = '\Ant\\' . 'Query'.$context;
				
				if( !class_exists('Query'.$context) ){
					require_once('app/modules/shared/queries/' . $context . '/' . $context . '.php');
				}
				
				if( !method_exists($class, $classMethod)){
					if( !class_exists( $context . $classMethod )){
						require_once('app/modules/context/queries/' 
							. $context . '/' 
							. $classMethod . '.php');
					}
					$class	= '\Ant\\' . 'Query'.$classMethod;
					$method = false;
					
				} else {
					$class	= '\Ant\\' . 'Query'.$context;
					$method = $classMethod;
					
				}
				
				$args = array_merge( (array)Router :: getRequestVars(), $args );
				
				if( $method ){
					$obj = new $class( new Query );
					$obj->{ $classMethod }( Database :: getTablePrefix(), $args );
				} else {
					$obj = new $class( new Query, Database :: getTablePrefix(), $args );
				}
				
				return $obj;
				
			}
			
			public static function get( $context, $method = null, $args = array() ){
				require_once('app/classes/context/' . $context . '/' . $context . '.php');
				$cls = '\Ant\\' . $context;
				$obj = new $cls;
				if( $method ){
					return $obj->{ $method }( $args );
				}
				return $cls;
			}
			
		}
		
	}

?>