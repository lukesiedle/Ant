<?php
	
	/*
	 *	The Router defines
	 *	what scripts should
	 *	be executed, depending 
	 *	on the request.
	 * 
	 *	@package Ant
	 *	@subpackage Router
	 *	@since 0.1.0
	 */

	namespace Ant {
		
		use \Ant\Application as App;
		
		Class Router {
			
			public static	$routeXml, 
							$client,
							$viewDir,
							$controlDir,
							$namespace,
							$context		= 'home',
							$path			= '',
							$requestVars	= array(),
							$routeVars		= array(),
							$channel		= false;
			
			/*
			 *	The principal function to
			 *	start routing the application.
			 *	Usually used from a shortcut
			 *	within @subpackage Application
			 *	
			 *	@since 0.1.0
			 */
			
			public static function route( $client ){
				
				// Set some useful vars and paths //
				self :: $client			= $client;
				self :: $viewDir		= 'app/modules/shared/views/' . $client . '/';
				self :: $controlDir		= 'app/modules/shared/controllers/' . $client . '/';
				self :: $namespace		= "\Ant\\" . $client;
				
				// Create the "true" query string //
				self :: setQueryString();
				
				// Initializes request vars //
				Request :: initialize( $_GET );
				
				// Load the contextual map if it exists and plan the route //
				if( self :: loadRouteMap( 'app/modules/context/views/' . $client .'/'. Application :: get()->context . '/route.xml' )){
					self :: planRoute();
				// Load the shared route map if it exists and plan the route //
				} else if( self :: loadRouteMap( self :: $viewDir . 'route.xml' )){
					self :: planRoute();
				}
				
				// Check if a channel is in use ( .../?channel=ajax ) //
				if( self :: $channel = self :: loadChannel() ){
					// Just load the channel and stop (e.g. ajax) //
					return;
				}
				
				// Load the route index, the principal file for handling the view //
				self :: loadRouteIndex();
			}
			
			/*
			 *	Set the query string which
			 *	would have been misinterpreted due	
			 *	to .htaccess creating its own.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setQueryString(){
				$array = parse_url( $_SERVER['REQUEST_URI'] );
				parse_str( $array['query'], $parts );
				$_REQUEST = array_merge( $_REQUEST, $parts );
				$_GET = $parts;
				self :: $path = $array['path'];
			}
			
			/*
			 *	Attempt to load a channel
			 *	if it exists. Channeling is used
			 *	when the same route is needed
			 *	but different output is required.
			 *	
			 *	@since 0.1.0
			 */
			
			public static function loadChannel( $channel = null ){
				if( is_null($channel) ){
					$channel = $_GET['channel'];
				}
				if( strlen($channel) > 0 ){
					if( file_exists(
						$channelFile = self :: $viewDir . 'channel/' . $channel . '.php')
					){
						require( $channelFile );
						
						$fn = '\\' . implode('\\', array(
							'Ant',
							self :: $client, 
							'Channel',
							$channel,
							'Index'
						));
						
						$fn( self :: getRequestVars() );
						return $channel;
					}
				}
				return false;
			}
			
			/*
			 *	Attempt to reset and load the specified
			 *	channel, for example, during an error 404.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function resetChannel( $channel ){
				self :: $channel = self :: loadChannel( $channel );
			}
			
			/*
			 *	Get the current public path 
			 *	to the application, i.e. not 
			 *	necessarily the root
			 * 
			 *	@since 0.1.0
			 *	@return string Public path to application
			 */
			
			// Get the path minus any query string params //
			public static function getAppPath(){
				$http = $_SERVER['HTTPS'];
				if( !$http ){
					$http = 'http://';
				}
				return  $http . $_SERVER['SERVER_NAME'] . self :: $path;
			}
			
			/*
			 *	Get the public path to the root
			 *	of the application
			 * 
			 *	@since 0.1.0
			 *	@return string Public path to application
			 */
			
			public static function getPublicRoot( $https = false ){
				$http = 'http://';
				if( $https ){
					$http = 'https://';
				}
				return  $http . $_SERVER['SERVER_NAME'] . PUBLIC_ROOT;
			}
			
			/*
			 *	Load the route map if it exists.
			 *	This file is always named 'route.xml'
			 *	within the shared client view.
			 * 
			 *	The route map is a set of conditionals
			 *	which creates certain variables based
			 *	on the depth and contents of the request.
			 * 
			 *	@since 0.1.0
			 *	@return bool True for map exists, or false
			 * 
			 */
			
			public static function loadRouteMap( $file ){
				
				// There is a different route map for every client //
				if( file_exists($file)){
					self :: $routeXml = simplexml_load_file( $file );
					return true;
				}
				return false;
			}
			
			/*
			 *	Plan the route if a route map exists,
			 *	using the variables created from the map.
			 *	Required variables include:
			 *	module, template, frame, doctitle
			 *	
			 *	Context is determined here
			 * 
			 *	@since 0.1.0
			 */
			
			public static function planRoute(){
				self :: parseRouteXml( self :: $routeXml, App :: get()->request, 0 );
				self :: $routeVars		= (object) self :: $routeVars;
				self :: $requestVars	= (object) self :: $requestVars;
			}
			
			/*
			 *	XML parsing function used in route planning.
			 *	This is the function which considers
			 *	every conditional laid out in the xml
			 *	and creates request or route vars
			 *	accordingly.
			 *	
			 *	@since 0.1.0
			 */
			
			public static function parseRouteXml( $xml, $request, $i ){
				
				$foundRoute = false;
				
				foreach( $xml->attributes() as $attr => $val ){
					
					if( $xml->attributes()->base ){
						if( $request[0] != $xml->attributes()->base ){
							break;
						}
					}
					
					switch( $attr ){
						case 'is' :
							if( in_array($request[$i], explode(',', $val))){
								if( $i == 0 ){
									self :: $routeVars['context']	= $request[$i];
									self :: $routeVars['module']	= $request[0];
								}
								self :: $requestVars[ (string)$xml->var ] = $request[ $i ];
								$foundRoute = true;
							}
							// Default the context to first item if url is empty //
							if( $i == 0 && empty($request[$i]) ){
								$context = explode(',', $val );
								self :: $routeVars['context']	= $context[0];
								self :: $routeVars['module']	= $xml->module;
								self :: $requestVars['context'] = $context[0];
								$foundRoute = true;
							}
							break;
						case 'when' :
							switch( $val ){
								case 'numeric'	:
									if( is_numeric($request[$i]) ){
										self :: $requestVars[ (string)$xml->var ] = $request[$i];
										$foundRoute = true;
									}
									break;
								case 'string' :
									if( is_string($request[$i]) ){
										self :: $requestVars[ (string)$xml->var ] = $request[$i];
										$foundRoute = true;
									}
									break;
							}
							break;
					}
				}
				
				// If a route wasn't found, it's probably a 404
				// The application will handle it from here //
				if( ! $foundRoute ){
					return;
				} else {
					// Set a document title for the current iteration //
					if( $xml->doctitle && $title = $xml->doctitle->{ self :: $routeVars['context'] }  ){
						self :: $routeVars['doctitle'] = (string)$title;
					}
				}
				
				$i++;
				$children = $xml->children();
				foreach( $children as $tag => $xml2 ){
					if( $tag == 'next' || 
							$tag == 'var' || 
								$tag == 'doctitle' ){
						continue;
					} 
					
					if( $tag == 'controller' ){
						self :: $routeVars[ $tag ][] = (string) $xml2;
						continue;
					}
					
					self :: $routeVars[ $tag ] = (string) $xml2;
				}
				
				foreach( $children as $tag => $xml2 ){
					// Recurse //
					self :: parseRouteXml ( $xml2, $request, $i );
				}
				
			}
			
			/*
			 *	Set the route context
			 * 
			 *	@since 0.1.0
			 */
			
			// Context can be overriden here //
			public static function setRouteContext( $context ){
				self :: $context = $context;
			}
			
			/*
			 *	Load the route index based on the client.
			 *	This is contained in the shared/views
			 *	folder and is required. The function
			 *	must always be namespaced accordingly.
			 *	This allows for multiple indexes
			 *	to be declared if required.
			 *	
			 *	@since 0.1.0
			 *	@return mixed The return variable of
			 *	the index function
			 */
			
			public static function loadRouteIndex(){
				
				require('app/modules/shared/views/' . self :: $client . '/index.php');
				// Execute //
				$fn = self :: $namespace . "\\index";
				return $fn( self :: getRequestVars() );
			}
			
			/*
			 *	Load the route view based on the client,
			 *	and the current context. The module/view
			 *	to be called is set within the route map.
			 *	The function must always be namespaced 
			 *	accordingly. This allows for multiple indexes
			 *	to be declared if required.
			 *	
			 *	Examples of context:
			 *	'home' 'news' 'about'
			 *	
			 *	@note This function needs to be manually 
			 *	called from within the index.
			 *	
			 *	@since 0.1.0
			 *	@return mixed | CollectionSet The collections
			 *	of the view to be passed to templating.
			 */
			
			public static function loadRouteView(){
				
				$mod = self :: getModule();				
				
				$view = ('app/modules/context/views/' 
							. self :: $client . '/'
							. self :: getContext() . '/'
							. self :: getModule() . '.php');
				
				// If not view is found, must be a 404 Not Found //
				if( ! file_exists($view) ){
					Application :: setError( '404', 'The route view could not be found.' );
					return;
				}
				
				// Include and execute //
				require( $view );
				$fn = self :: $namespace . "\\" . self :: getContext() . '\\' . self :: getModule();
				return $fn( self :: getRequestVars() );
				
			}
			
			/*
			 *	Load the shared view based on the client, 
			 *	regardless of context. The shared view
			 *	would contain functionality that 
			 *	is global, and would usually be named 
			 *	something like 'frame.php'
			 *	
			 *	@note This function needs to be manually 
			 *	called from within the index.
			 *	
			 *	@since 0.1.0
			 *	@return mixed | CollectionSet The collections
			 *	of the view to be passed to templating.
			 */
			
			public static function loadSharedView( $view, $contextView = null, $client = null ){
				
				if( !$client ){
					$client = self :: $client; 
				}
				
				require('app/modules/shared/views/' 
							. $client . '/'
							. $view . '.php');
				
				$fn = '\Ant\\' . $client . "\\" . $view;
				return $fn( self :: getRequestVars(), $contextView );
				
			}
			
			/*
			 *	Get the route variables, as defined
			 *	in route.xml. Example:
			 *	'view' 'client'
			 *	
			 *	@since 0.1.0
			 *	@return stdClass The route variables
			 */
			
			public static function getRouteVars(){
				return self :: $routeVars;
			}
			
			/*
			 *	Get the request variables. Example:
			 *	'id' 'title' 'action'
			 * 			 
			 *	@since 0.1.0
			 *	@return stdClass The route variables
			 */
			
			public static function getRequestVars(){
				return self :: $requestVars;
			}
			
			/*
			 *	Get the current module, as
			 *	defined in route.xml.
			 * 			 
			 *	@since 0.1.0
			 *	@return string The module
			 */
			
			public static function getModule(){
				return self :: $routeVars->module;
			}
			
			/*
			 *	Get the current template, as
			 *	defined in route.xml.
			 * 			 
			 *	@since 0.1.0
			 *	@return string The template
			 */
			
			public static function getTemplate(){
				return self :: $routeVars->template;
			}
			
			/*
			 *	Get the current request context,
			 *	as defined in route.xml.
			 *	Example : 'home' 'news' 'about'
			 * 			 
			 *	@since 0.1.0
			 *	@return string The request context
			 */
			
			public static function getContext(){
				return self :: $routeVars->context;
			}
			
			/*
			 *	Get the document title
			 *	set inside the route.
			 * 			 
			 *	@since 0.1.0
			 *	@return string The title
			 */
			
			public static function getDocTitle(){
				return self :: $routeVars->doctitle;
			}
			
			/*
			 *	Set the document title
			 *	in special cases, example '404 error'
			 * 			 
			 *	@since 0.1.0
			 *	@return string The title
			 */
			
			public static function setDocTitle( $title ){
				self :: $routeVars->doctitle = $title;
			}
			
			/*
			 *	Get the controllers set
			 *	inside the route.
			 * 			 
			 *	@since 0.1.0
			 *	@return string The title
			 */
			
			public static function getControllers(){
				return self :: $routeVars->controller;
			}
			
		}
		
	}