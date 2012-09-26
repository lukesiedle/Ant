<?php
	
	namespace Ant {
		
		/*
		 *	Application hosts important
		 *	data, global core methods,
		 *	and information concerning
		 *	the application state.
		 * 
		 *	@package Ant
		 *	@subpackage Application
		 *	@type Shared
		 *	@since 0.1.0
		 *	
		 */		 
		
		Class Application {
			
			public static $app;
			
			/*
			 *	Initializes variables
			 *	for later use. Includes the default
			 *	configuration file.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function initialize( $requestVar = 'request' ){
				
				// Setup a stdClass object for storage //
				self :: $app = new \stdClass;
				
				// Declare app vars / examples //
				
				// The request variable is set inside .htaccess //
				self :: $app->requestVar	= $requestVar;
				self :: $app->client		= 'web';
				self :: $app->local			= true;
				self :: $app->request		= explode(
					'/', $_GET[ self :: $app->requestVar ] 
				);
				self :: $app->context		= '';
				self :: $app->lang			= 'en_gb';
				self :: $app->theme			= 'default';
				self :: $app->location		= array();
				self :: $app->dates			= array();
				self :: $app->time			= date('U');
				self :: $app->path			= array();
				self :: $app->connection	= array();
				
				// Load the global configuration //
				require('config/global.php');
				
			}
			
			
			/*
			 *	Returns the application storage
			 *	object.
			 * 
			 *	@since 0.1.0
			 *	@return object The storage object
			 */
			
			public static function get(){
				return self :: $app;
			}
			
			/*
			 *	Extends the application storage
			 *	object.
			 * 
			 *	@since 0.1.0			  
			 */
			
			public static function set( Array $arr ){
				foreach( $arr as $k => $v ){
					self :: $app->{ $k } = $v;
				}
			}
			
			/*
			 *	Detects and sets the environment (local or remote)
			 *	based on request.
			 * 
			 *	@since 0.1.0	  
			 */
			
			public static function setEnvironment(){
				
				self :: $app->local = false;
				self :: $app->developerMode = false;
				
				if( in_array(
					$_SERVER['SERVER_NAME'], 
					self :: config()->local_server_name
				)){
					self :: $app->local = true;
					self :: $app->developerMode = true;
				}
			}
			
			/*
			 *	Detects and sets the client (api,mobile,tablet,web)
			 *	based on request, headers, mobile detection.
			 * 
			 *	@since 0.1.0	  
			 */
			
			public static function setClient( $force = null ){
				
				// Requier request configuration //
				require('config/request.php');
				
				$req = self :: $app->request;
				
				// Case through possible hostnames to get client //
				foreach( self :: config()->hosts as $client => $hosts ){
					if(in_array($_SERVER['HTTP_HOST'], $hosts)){
						self :: $app->client = $client;
						break;
					}
				}
				
				// Case through possible request vars to get client //
				foreach( self :: config()->clients as $request => $client ){
					if($req[0] == $request ){
						self :: $app->client = $client;
						// Remove it from the request as client is not a request variable //
						array_shift( self :: $app->request );
						break;
					}
				}

				// Detect if it's a mobile client if web has been detected //
				if( self :: $app->client == 'web' ){
					self :: detectMobileClient();
				}
				
				// The user forced a client type //
				if( $force ){
					self :: $app->client = $force;
				}
				
				self :: $app->context = self :: $app->request[0];

			}
			
			/*
			 *	Returns the current client (api,mobile,tablet,web)
			 * 
			 *	@since 0.1.0	  
			 *	@return string The current client
			 */
			
			public static function getClient(){
				return self :: $app->client;
			}
			
			/*
			 *	Detects if the client is mobile using 
			 *	the TERA / WURFL Library.
			 * 
			 *	@since 0.1.0	  
			 */
			
			public static function detectMobileClient(){
				// Tera / Wurfl ... //
			}
			
			/*
			 *	Loads shared client includes
			 *	as well as client-specific includes
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setClientSettings(){
				
				// Global includes like data manipulation classes //
				require( 'config/includes/global.php' );
				
				// Client-specific includes like database class,  //
				$includes = 'config/includes/clients/' . self :: $app->client . '.php';
				
				// Client-specific includes are optional //
				if( file_exists($includes)){
					require( $includes );
				}

			}
			
			/*
			 *	Sets the current language according
			 *	to user-established variable in cookies/session.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setLanguage( $lang = 'en_all' ){
				self :: $app->lang = $lang;
				
				// Load the languages into the template globals //
				Template :: loadLanguageGlobals();
			}
			
			/*
			 *	Returns the current language
			 * 
			 *	@since 0.1.0
			 *	@return string The language shortcode
			 */
			
			public static function getLanguage(){
				return self :: $app->lang;
			}
			
			/*
			 *	Sets the current template theme
			 *	Here for forward-compatibility	
			 *	
			 *	@since 0.1.0
			 */
			
			public static function setTheme( $theme = 'default' ){
				self :: $app->theme = $theme;
			}
			
			/*
			 *	Gets the current theme
			 *	
			 *	@since 0.1.0
			 */
			
			public static function getTheme(){
				return self :: $app->theme;
			}
			
			/*
			 *	Sets the current locale (country, timezone)
			 *	based on geolocation / session, cookies
			 * 
			 *	Also sets some useful date items
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setLocale(){
				
				self :: $app->location		= array(
					'timezone_offset'	=> 7200
				);

				$offset = self :: $app->location['timezone_offset']; 

				// Set dates based on region picked up //
				date_default_timezone_set("UTC");
				
				self :: $app->dates		= array(
					'now'				=> date('U') + $offset,
					'day_starts'		=> mktime(1, 1, 1) + $offset,
					'day_ends'			=> mktime(23, 59, 59) + $offset
				);
				
				self :: $app->time		= self :: $app->dates['now'];

			}
			
			/*
			 *	Allocate resources based
			 *	on the current client designation
			 * 
			 *	@since 0.1.0
			 */
			
			public static function allocateResources(){

				// Require default resources //
				require( 'config/resources/global.php' );
				
				// Client-specific resources like database connection //
				$includes = 'config/resources/clients/' . self :: $app->client . '.php';
				
				if( file_exists($includes)){
					require( $includes );
				}
				
			}
			
			/*
			 *	Placeholder function 
			 *	for routing the application
			 *	
			 *	@since 0.1.0
			 */
			
			public static function route(){
				// Routes the app  //
				Router :: route( self :: $app->client );
			}
			
			/*
			 *	Set headers defined 
			 *	within Document
			 *	
			 *	@since 0.1.0
			 */
			
			public static function setHeaders(){
				
				// Sets headers if any were defined in Document //
				foreach( Document :: getHeaders() as $header ){
					header( $header );
				}
			}
			
			/*
			 *	Flush the output and 
			 *	release any resources
			 *	
			 *	@since 0.1.0
			 */
			
			public static function flush(){
				
				// Prepare the document parts for output //
				Document :: prepare();
				
				// If a channel exists, e.g. Ajax, output is its reponsibility //
				if( Router :: $channel ){
					return;
				}				
				
				// Output the contents of the Template buffer //
				echo Template :: output();
			}
			
			/*
			 *	Connect to the configured
			 *	database and set the table prefix.
			 *	
			 *	@since 0.1.0
			 */
			
			public static function connect( Array $config ){
				
				$time = microtime();
				
				switch( $config['connection'] ){
					// Connection to database //
					case 'mysql' :
						try {
							self :: $app->connection[ $type ] = \Library\MySQL :: connect( $config );
						} catch( \Exception $e ){
							
							self :: set(array(
								'errors' => array('mysql' => $e->getMessage())
							));
							
							// Otherwise try connect to the local host //
							$config['host'] = 'localhost';
							
							try {
								self :: $app->connection[ $type ] = \Library\MySQL :: connect( $config );
							} catch( \Exception $e ){
								self :: set(array(
									'errors' => array('mysql' => $e->getMessage())
								));
							}
							
						}
						break;
				}
				
				// Set the table prefix //
				Database :: setTablePrefix( self :: config()->mysql_table_prefix );
				
			}

			/*
			 *	Shortcut method to return 
			 *	the configuration
			 *	
			 *	@since 0.1.0
			 *	@return object The Configuration object
			 */
			
			public static function config(){
				return ( object ) Configuration :: get();
			}
			
			/*
			 *	Shortcut method to output
			 *	an array to the screen in <pre> tags	
			 * 
			 *	@since 0.1.0
			 */
			
			public static function out( $data ){
				return \Library\Arr\out( $data );
			}
			
			/*
			 *	Shortcut method to return 
			 *	the request variables from Router
			 * 
			 *	@since 0.1.0
			 *	@return object Router request variables
			 */
			
			public static function request(){
				return Router :: getRequestVars();
			}
			
			/*
			 *	Redirect the application making 
			 *	sure the session gets written and
			 *	the application exits.
			 * 
			 *	@since 0.1.0
			 */
			
			public static function redirect( $url = null ){
				if( session_id() ){
					// Make sure we write any session before redirect //
					session_write_close();
				}
				
				if( is_null($url) ){
					$url = PUBLIC_ROOT;
				} else {
					$url = PUBLIC_ROOT . $url;
				}
				
				header( 'Location: ' . $url );
				exit;
			}
			
			/*
			 *	Set an error 
			 * 
			 *	@since 0.1.0
			 * 
			 *	@note
			 *	This function actually
			 *	usurps control of the 
			 *	application, performing
			 *	the final few tasks of the
			 *	index, and exiting the script.
			 * 
			 */	
			
			public static function setError( $code = '404', $msg = null ){
				
				switch( $code ){
					case '403' :
						// Adds the error header //
						Document :: addHeader('HTTP/1.0 403 Forbidden');
						if( ! $msg ){
							$msg = 'You do not have permission to access this resource.';
						}
						break;
					case '404' :
						// Adds the error header //
						Document :: addHeader('HTTP/1.0 404 Not Found');
						if( ! $msg ){
							$msg = 'Resource not found.';
						}
					break;
				
					case '422' :
						// Adds the error header //
						Document :: addHeader('HTTP/1.0 422 Unprocessable Entity');
						if( ! $msg ){
							$msg = 'Request could not be completed due to invalid request data.';
						}
						break;
				}
				
				// Add the error message to globals //
				Template :: addGlobals( new Collection(array(array(
					"msg"	=> $msg,
					"code"	=> $code
				)),'error' ));
				
				// Resetting the channel loads error output //
				Router :: resetChannel('error');
						
				// Sets the defined headers //
				self :: setHeaders();					
				
				// Flushes the output //
				self :: flush();
				
				exit;
			}

		}
	 
	}