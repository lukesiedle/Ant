<?php
	
	/*
	 *	Template handles general
	 *	data to string operations,
	 *	regular expression replacement.
	 * 
	 *	@package Ant
	 *	@subpackage Template
	 *	@type Shared
	 *	@since 0.1.0
	 */

	namespace Ant {
		
		Class Template {
			
			public static $contextMap;
			public static $sharedMap;
			public static $buffer;
			public static $templates = array();
			public static $cache = array();
			public static $globals = array();
			public static $cacheLoaded = false;
			
			public $path;
			public $output = '';
			public $template;
			
			/*
			 *	Instantiation of new template
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
			
			/*
			 *	Load the self template
			 *	into the template passed
			 *	as an argument, according
			 *	to a search paramater
			 *	
			 *	@since 0.1.0
			 */
			
			public function loadInto( $template, $search ){
				
				$template->replace(array(
					$search => $this->getOutput() 
				));
			}
			
			/*
			 *	Dual-context function for 
			 *	string replacement.
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
				if( $str ){
					return str_replace( $search, array_values($data), $str );
				}
				
				// Dynamic function behaviour //
				$this->setOutput(str_replace(
					$search, array_values($data), $this->getOutput()
				));
				
				return $this;
			}
			
			/*
			 *	Sets the output string
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			
			public function setOutput( $str ){
				$this->output = $str;
				return $this;
			}
			
			/*
			 *	Returns the original template
			 * 
			 *	@since 0.1.0
			 *	@return string The template
			 */
			
			public function getTemplate(){
				return $this->template;
			}
			
			/*
			 *	Returns the output string
			 * 
			 *	@since 0.1.0
			 *	@return string The output
			 */
			
			public function getOutput(){				
				return $this->output;
			}
			
			/*
			 *	Set the path that will 
			 *	be used for the map.
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			
			public function setPath( $path ){
				$this->path = $path;
				return $this;
			}
			
			/*
			 *	Get the xml map for mapping
			 *	data to the template
			 * 
			 *	@since 0.1.0
			 *	@return object XML			 
			 */
			
			public function getMap(){
				if( file_exists($path = $this->path . '.xml')){
					return \simplexml_load_file( $path );
				}
			}
			
			/*
			 *	Map the passed in collection set 
			 *	to the output string
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			
			public function map( CollectionSet $collections ){
				$xml = $this->getMap();
				$this->setOutput($this->loopMap( $xml, $collections->getCollections(), $this->getOutput() ));
				return $this;
			}
			
			/*
			 *	Loop through the map recursively,
			 *	applying the data in each template
			 *	using string replacement, and 
			 *	inserting into the output string
			 *	 
			 *	@since 0.1.0
			 *	@return string The resulting string
			 */
			
			public function loopMap( $xml, Array $collections, $html = null ){
				
				foreach( $xml as $tag => $each ){
					
					// Get the name of the collection //
					$nm = (string)$each->attributes()->name;
					
					// Compare the name to the available collection //
					if( $collections[$nm] ){
						
						$collection = $collections[$nm];
						
						$obj = $this;
						
						// Get the template if one is specified //
						if( $each->template ){
							$tpl = self :: getLoopTemplate( $each );
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
							
							// Run the replacement / append of the current iteration of the collection //
							$in .= Template :: replace(
								$replace
							, $tpl, $each->template );
							
						});
						
						// Run the replacement within the current iteration of the xml //
						$html = Template :: replace(array(
							'\\' . $nm => $in
						), $html );
						
						
					}
				}
				
				return $html;
				
			}
			
			/*
			 *	Add a Collection to globals
			 *	for replacement at the end
			 *	of the templating cycle.
			 *	 
			 *	@since 0.1.0
			 */
			
			public static function addGlobals( Collection $collection ){
				$ns = $collection->getNameSpace();
				if(! is_array(self :: $globals[ $ns ])){
					self :: $globals[ $ns ] = array();
				}
				
				self :: $globals[ $ns ] = array_merge( 
					$collection->first()->toArray(), 
					self :: $globals[ $ns ]
				);
			}
			
			/*
			 *	Insert all globals into
			 *	the specified string.
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
			
			/*
			 *	Get the loop template based 
			 *	on the xml node.
			 *	 
			 *	@since 0.1.0
			 *	@return string The template string
			 */
			
			public static function getLoopTemplate( $xml ){
				
				// Create the path //
				$tpl = 'public/clients/' 
					. Application :: getClient() . 
					'/templates/' 
					. Application :: getLanguage()
					. '/';
				
				$shared = $xml->template->attributes()->shared ? 1 : 0;
				
				if( $shared ){
					$tpl .= 'shared/';
				} else {
					$tpl .= 'context/' . Router :: getContext() . '/';
				}
				
				$tpl .= 'object/' . $xml->template . '.html';				
				
				// Add the template //
				$tpl = self :: addTemplate( $tpl );
				
				return $tpl;
				
			}
			
			/*
			 *	Add the template to global 
			 *	storage array. Useful
			 *	for future caching.
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
			
			/*
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
			
			/*
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
			
			/*
			 *	Load and add a template based
			 *	on a path
			 *	 
			 *	@since 0.1.0
			 *	@return string The template string
			 */
			
			public static function loadFile( $path ){
				return self :: addTemplate( $path );
			}
			
			/*
			 *	Load a template inside the context.
			 *	Example: 'home' 'user' 'about'
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
			
			/*
			 *	Load the view template
			 *	 
			 *	@since 0.1.0
			 *	@return object The template
			 */
			
			public static function loadViewTemplate(){
				
				$view = Router :: getTemplate();
				
				$path = self :: getPath( Router :: getContext() ) . $view;
							
				return new self( $path );
			}
			
			
			/*
			 *	Load a template inside the shared space.
			 *	 
			 *	@since 0.1.0
			 *	@return object The template
			 */
			
			public static function loadSharedTemplate( $tpl ){
				$path = self :: getPath() . $tpl;
				
				return new self( $path );
			}
			
			/*
			 *	Get the path to the templates
			 *	directory, optionally in context.
			 *	 
			 *	@since 0.1.0
			 *	@return string The templates directory
			 */
			
			public static function getPath( $context = null ){
				if( !$context ){
					$path = 'shared/';
				} else {
					$path = 'context/' . $context . '/';
				}
				return 'public/clients/' 
						. Application :: getClient()
						. '/templates/'
						. Application :: getLanguage()
						. '/' . $path;
			}
			
			/*
			 *	Set the buffer. The buffer
			 *	is the object Application looks
			 *	for in order to generate output
			 *	 
			 *	@since 0.1.0
			 */
			
			public static function setBuffer( $obj ){
				self :: $buffer = $obj;
			}
			
			/*
			 *	Creates final output
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

					// Strip leftovers //
					$output = preg_replace("%{(\w\S*|\\\\\w\S*)}%", "", $output );					
					
					self :: writeCache();

				}
				
				return $output;
			}
			
			/*
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
			
		}
		
	}