<?php
	
	/**
	 *	Template handles general
	 *	data to string operations,
	 *	regular expression replacement.
	 * 
	 *	@package Core
	 *	@subpackage Template
	 *	@since 0.1.0
	 */
	namespace Core {
		
		Class Template {
			
			public static $contextMap;
			public static $sharedMap;
			public static $buffer;
			public static $templates = array();
			public static $cache = array();
			public static $globals = array();
			public static $cacheLoaded = false;
			public static $controllers;
			
			public $path;
			public $output = '';
			public $template;
			
			/**
			 *	Instantiation of new template
			 *	
			 *	@param string $pathToFile Path to 
			 *	the template file
			 *	
			 *	@since 0.1.0
			 */
			public function __construct( $pathToFile = null ){
				
				// Load the cache //
				if( ! self :: $cacheLoaded ){
					self :: loadCache();
				}
				
				// Load the file as a string //
				if( $pathToFile ){
					$this->setPath( $pathToFile );
					if( ! $this->template ){				
						$file = $pathToFile .'.html';
						if( ! file_exists( $file )){
							throw new \Exception('Template file "' . $file .'" does not exist.');
							return false;
						}
						$this->template = self :: loadFile( $file );
						$this->setOutput( $this->template );
					}
				}
				
				return true;
			}
			
			/**
			 *	Load the self template
			 *	into the template passed
			 *	as an argument, according
			 *	to a search paramater
			 *	
			 *	@param Template A template object.
			 *	The self template will be loaded
			 *	into this object using a search.
			 *	@param string $search The search key
			 *	
			 *	@since 0.1.0
			 */
			public function loadInto( $template, $search ){
				
				$template->replace(array(
					$search => $this->getOutput() 
				));
			}
			
			/**
			 *	Dual-context function for 
			 *	string replacement.
			 *	
			 *	@param array $data The key
			 *	value pairs to replace in 
			 *	the template
			 *	@param string $str The target string
			 *	if scope is static. 
			 *	@param string $ns A namespace to prefix
			 *	the searches with
			 *	
			 *	@since 0.1.0
			 */
			public function replace( $data, $str = null, $ns = null ){
				
				$search = array_keys($data);
				
				foreach( $search as $i => $value ){
					if( $ns && $value[0] != '\\' ){
						$value = $ns . '.' . $value;
					}
					$search[$i] = '{' . $value . '}';
				}
				
				// Static function behaviour //
				if( !is_null($str) ){
					return str_replace( $search, array_values($data), $str );
				}
				
				// Dynamic function behaviour //
				$this->setOutput(str_replace(
					$search, array_values($data), $this->getOutput()
				));
				
				return $this;
			}
			
			/**
			 *	Sets the output string
			 * 
			 *	@param string $str The string
			 *	for output
			 *	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 *	
			 */
			public function setOutput( $str ){
				$this->output = $str;
				return $this;
			}
			
			/**
			 *	Returns the original template
			 *	string, before replacements.
			 *	
			 *	@since 0.1.0
			 *	@return string The template
			 */
			public function get(){
				return $this->template;
			}
			
			/**
			 *	Returns the output string
			 *			 
			 *	@since 0.1.0
			 *	@return string The output
			 */
			public function getOutput(){				
				return $this->output;
			}
			
			/**
			 *	Set the path that will 
			 *	be used to load the template
			 *	and also the xml map.
			 *	
			 *	@param string $path The path
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function setPath( $path ){
				$this->path = $path;
				return $this;
			}
			
			/**
			 *	Get the xml map for mapping
			 *	data to the template.
			 *	
			 *	@since 0.1.0
			 *	@return object XML object 
			 */
			public function getMap(){
				if( file_exists($path = $this->path . '.xml')){
					return \simplexml_load_file( $path );
				}
			}
			
			/**
			 *	Map the passed in collection set 
			 *	to the output string
			 * 
			 *  @param CollectionSet The collection set
			 *	which contains data to map to the template			 
			 *	using the XML map as a guide.
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function map( CollectionSet $collections ){
				$xml = $this->getMap();
				
				if( $xml ){
					$this->setOutput(
						$this->loopMap( 
							$xml, 
							$collections->getCollections(), 
							$this->getOutput() 
						)
					);
					return $this;
				}
			}
			
			/**
			 *	Loop through the map recursively,
			 *	applying the data in each template
			 *	using string replacement, and 
			 *	inserting into the output string
			 *	
			 *	@param object $xml The XML object to apply
			 *	in the current loop
			 *	@param array $collections The collections
			 *	@param string $html The html to apply in
			 *	the current loop
			 *		
			 *	@since 0.1.0
			 *	@return string The resulting string
			 */
			public function loopMap( $xml, Array $collections, $html = null ){
				
				foreach( $xml as $tag => $each ){
					
					$nm = $tag;
					
					// Compare the name to the available collection //
					if( $collections[$tag] ){						
						
						$collection = $collections[$tag];						
						
						$obj = $this;
						
						// Controller conditionals //
						if( $each->attributes()->when ){
							$controllers = explode(',', (string) $each->attributes()->when );
							$checkFailed = false;
							
							foreach( $controllers as $controller ){
								if( ! self :: checkController( trim($controller) ) ){
									$checkFailed = true;
									break;
								}
							}
							
							if( $checkFailed ){
								continue;
							}
						}
						
						// Get the template if one is specified //
						if( $each->template ){
							$tpl = self :: getLoopTemplate( $each->template );
						} else {
							// The collection will be applied globally //
							Template :: addGlobals( $collection );
							continue;
						}
						
						// Create the current iteration string buffer //
						$in = '';
						
						// Loop through the collection's records //
						$collection->each( function($record) use( $obj, $each, $tpl, & $in, $nm ){
							$replace = $record->toArray();
							if( count($joins = $record->getJoins()) > 0 ){
								// If there are joins, loop through as well //
								foreach( $joins as $join ){
									$joinNm = $join->getNameSpace();
									$collections = $each;
									
									// If the xml node has its own set of collections... //
									if( $each->collections ){
										$collections = $each->collections->children();
									}
									
									$thisStr = $obj->loopMap( $collections, array($joinNm => $join), '{\\' . $joinNm . '}' );
									
									// Add the resulting string to the replacement buffer //
									$replace[ '\\' . $joinNm ] = $thisStr;
								}
							}
							
							$useNs = $each->attributes()->ns;
							
							// Run the replacement / append of the current iteration of the collection //
							$in .= Template :: replace(
								$replace
							, $tpl, $useNs );
							
						});											
						
						// Run the replacement within the current iteration of the xml //
						$html = Template :: replace(array(
							'\\' . $nm => $in
						), $html );
					}
				}
				
				return $html;
				
			}
			
			/**
			 *	Evaluate and run the specified controller
			 *	according to the bool prefix. Controllers
			 *	are used to show/hide certain templates
			 * 
			 *	@param string $controller The controller
			 *	that was set inside the XML map.
			 * 
			 *	@since 0.1.0
			 *	@return bool The result
			 */
			public static function checkController( $controller ){
				
				$controller = (string) $controller;
				
				$expectBool = $controller[0] != '!';
				
				if( $expectBool == false ){
					$controller = substr( $controller, 1 );
				}
				
				// Check if the result exists in memory //
				if( self :: $controllers[ $controller ] ){
					return $expectBool == self :: $controllers[ $controller ];
				}
				
				$result = \Core\Controller :: call( $controller );
				
				self :: $controllers[ $controller ] = $result;
				return $expectBool == $result;
			}
			
			/**
			 *	Add a Collection to globals
			 *	for replacement at the end
			 *	of the templating cycle.
			 *	
			 *	@param Collection The collection	
			 *	@param string $nsPrefix The prefix to apply
			 *	before the Collection namespace
			 * 
			 *	@since 0.1.0
			 */
			public static function addGlobals( Collection $collection, $nsPrefix = '' ){
				
				$ns = $nsPrefix . $collection->getNameSpace();
				
				if(! is_array(self :: $globals[ $ns ])){
					self :: $globals[ $ns ] = array();
				}
				
				// Can't use array merge due to renumbering //
				$collection->each(function( $record ) use( & $self, $ns ){
					
					foreach( $record->toArray() as $key => $data ){
						Template :: $globals[ $ns ][$key ] = $data;
					}
					// Add any joins from the collection (recurses) //
					if( $record->hasJoins() ){
						$joins = $record->getJoins();
						foreach( $joins as $join ){
							Template :: addGlobals( $join, $ns . '.' );
						}
					}
				});
			}
			
			/**
			 *	Insert all globals into
			 *	the specified string.
			 *	
			 *	@param string $str The string
			 *  
			 *	@since 0.1.0
			 *	@return string The altered string
			 */
			public static function replaceGlobals( $str ){
				foreach( self :: $globals as $ns => $each ){
					foreach( array_keys($each) as $key ){
						$search[$ns . '.' . $key] = $each[$key];
					}
				}
				if( is_array($search)){
					return Template :: replace( $search, $str );
				}
				return $str;
			}
			
			/**
			 *	Get the loop template based 
			 *	on the path.
			 *	 
			 *	@param string $path The path 
			 *	to the template file
			 * 
			 *	@since 0.1.0
			 *	@return string The template string
			 */
			public static function getLoopTemplate( $path ){
				
				// Create the path //
				$tpl = 'public/clients/' 
					. Application :: getClient() . 
					'/templates/' 
					. Application :: getTheme()
					. '/' . $path . '.html';				
				
				// Add the template //
				$tpl = self :: addTemplate( $tpl );
				
				return $tpl;
				
			}
			
			/**
			 *	Add the template to global 
			 *	storage array. Useful
			 *	for future caching.
			 *  
			 *	@param string $path The path
			 *	to the template file
			 * 
			 *	@since 0.1.0
			 *	@return string The template string
			 */
			public static function addTemplate( $path ){
				if( !isset( self :: $templates[ $path ] )){
					self :: $templates[ $path ] = file_get_contents( $path );
				}
				return self :: $templates[ $path ];
			}
			
			/**
			 *	Load the cache based on the
			 *	current client.
			 *	 
			 *	@since 0.1.0
			 */
			public static function loadCache(){
				
				// Prevent local caching //
				if( Application :: get()->local ){
					self :: $cacheLoaded = true;
					return;
				}
				
				// Load the cache into the Class //
				if( file_exists($file = Application :: config()->template_cache_path)){
					$file = file_get_contents( $file );
					$templates = json_decode( $file, true );
					self :: $cache = $templates;
					self :: $templates = $templates;
				}
				
				self :: $cacheLoaded = true;
			}
			
			/**
			 *	Write the cache based on the
			 *	current set of templates
			 *	in memory. Writes to a file
			 *	as a json string.
			 *	 
			 *	@since 0.1.0
			 */
			public static function writeCache(){
				if( Application :: config()->template_cache_path ){
					$h = fopen( Application :: config()->template_cache_path, 'w+' );
					fwrite( $h, json_encode(self :: $templates));
					fclose( $h );
				}
			}
			
			/**
			 *	Load and add a template based
			 *	on a path
			 *	
			 *	@param string $path The path to
			 *	the file
			 *	
			 *	@since 0.1.0
			 *	@return string The template string
			 */
			public static function loadFile( $path ){
				return self :: addTemplate( $path );
			}
			
			/**
			 *	Load a template inside the app context.
			 *	E.g. 'home' 'user' 'about'
			 *	
			 *	@param string $tpl The template inside the context
			 *	Defaults to the context name.
			 *	@param string $context The context to use.
			 *	Defaults to the current context.
			 * 
			 *	@since 0.1.0
			 *	@return object The template
			 */
			public static function loadContextTemplate( $tpl = null, $context = null ){
				
				if( !$tpl ){
					$tpl = Router :: getContext();
				}
				if( !$context ){
					$context = Router :: getContext();
				}
				
				$path = self :: getPath( $context ) . $tpl;
				
				return new self( $path );
				
			}
			
			/**
			 *	Load the view template, according
			 *	to the current context and 
			 *	specified view.
			 *				 
			 *	@since 0.1.0
			 *	@return object The template
			 */
			public static function loadViewTemplate(){
				
				$view = Router :: getTemplate();
				
				$path = self :: getPath( Router :: getContext() ) . $view;
							
				return new self( $path );
			}
			
			
			/**
			 *	Load a template inside the shared space.
			 *	
			 *	@param string $tpl The template name
			 *  
			 *	@since 0.1.0
			 *	@return object The template
			 */
			public static function loadSharedTemplate( $tpl ){
				$path = self :: getPath() . 'shared/' . $tpl;
				
				return new self( $path );
			}
			
			/**
			 *	Load a template inside template space,
			 *	optionally returning as a string 
			 *	by default.
			 *	
			 *	@param string $tpl The template string
			 *	@param string $return The type to return
			 * 
			 *	@since 0.1.0
			 *	@return object The template / string The html
			 */
			public static function getTemplate( $tpl, $return = 'string' ){
				$path	= self :: getPath() . $tpl;
				$tpl	= new self( $path );
				if( $type == 'string' ){
					return $tpl->getOutput();
				}
				return $tpl;
			}
			
			/**
			 *	Get the path to the templates
			 *	directory, optionally in context.
			 *	 
			 *	@param string $context
			 * 
			 *	@since 0.1.0
			 *	@return string The templates directory
			 */
			public static function getPath( $context = null ){
				$path = '';
				if( $context ){
					$path = 'context/' . $context . '/';
				}
				return 'public/clients/' 
						. Application :: getClient()
						. '/templates/'
						. Application :: getTheme()
						. '/' . $path;
			}
			
			/**
			 *	Set the buffer. The buffer
			 *	is the object Application looks
			 *	for in order to generate output
			 *	
			 *	@param Template The template object
			 *  
			 *	@since 0.1.0
			 */
			public static function setBuffer( $obj ){
				self :: $buffer = $obj;
			}
			
			/**
			 *	Creates final output from
			 *	the buffer
			 * 	
			 *	@since 0.1.0
			 *	@return string The output string
			 */
			public static function output(){
				
				if( self :: $buffer ){					
					
					// Clear remaining search strings //
					$output = self :: getBuffer()->getOutput();
					
					// Do a final replace of any globals //
					$output = Template :: replaceGlobals( $output );
					
					// Again in case of globals within globals //
					$output = Template :: replaceGlobals( $output );
					
					// Strip leftovers //
					$output = preg_replace("%{(\w\S*|\\\\\w\S*)}%", "", $output );
					
					// Write the cache for performance sake //
					self :: writeCache();
					
				}
				
				return $output;
			}
			
			/**
			 *	Get the buffer if it exists
			 *	
			 *	@since 0.1.0
			 *	@return string The buffer template object
			 */
			public static function getBuffer(){
				if( self :: $buffer ){
					return self :: $buffer;
				}				
				return false;
			}
			
			/**
			 *	Load current language
			 *	into globals
			 * 
			 *	@since 0.1.0
			 */
			public static function loadLanguageGlobals(){
				
				$dir = 'config/i18n/languages/' 
					. Application :: get()->lang . '.php';
				
				require_once( $dir );
				
				$refl = new \ReflectionClass('\LANG');
				
				Template :: addGlobals( new Collection( 
					$refl->getConstants(), 'LANG'
				));
			}
			
			/**
			 *	Return a language phrase from memory
			 *	
			 *	@since 0.1.0
			 *	@return String The language string
			 */
			public static function phrase( $str ){
				return self :: $globals['LANG'][ $str ];
			}
			
		}
		
	}