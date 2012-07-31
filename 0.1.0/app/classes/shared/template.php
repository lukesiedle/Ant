<?php

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
			
			public function __construct( $pathToFile = null ){
				
				if( ! self :: $cacheLoaded ){
					self :: loadCache();
				}
				
				if( $pathToFile ){
					$this->setPath( $pathToFile );
					if( ! $this->template ){
						$this->template = self :: loadFile( $pathToFile .'.html' );
						$this->setOutput( $this->template );
					}
				}
			}
			
			public function loadInto( $template, $search = null ){
				
				$template->replace(array(
					$search => $this->getOutput() 
				));
			}
			
			public function replace( $data, $str = null, $ns = null ){
				
				$search = array_keys($data);
				
				foreach( $search as $i => $value ){
					if( $ns && $value[0] != '\\' ){
						$value = $ns . '.' . $value;
					}
					$search[$i] = '{' . $value . '}';
				}
							
				if( $str ){
					return str_replace( $search, array_values($data), $str );
				}
				
				$this->setOutput(str_replace(
					$search, array_values($data), $this->getOutput()
				));
				
				return $this;
			}
			
			public function setOutput( $str ){
				$this->output = $str;
				return $this;
			}
			
			public function getTemplate(){
				return $this->template;
			}
			
			public function getOutput(){
				return $this->output;
			}
			
			public function setPath( $path ){
				$this->path = $path;
				return $this;
			}
			
			public function getMap(){
				if( file_exists($path = $this->path . '.xml')){
					return \simplexml_load_file( $path );
				}
			}
			
			// Map the data to the template //
			public function map( CollectionSet $collections ){
				$xml = $this->getMap();
				$this->setOutput($this->loopMap( $xml, $collections->getCollections(), $this->template ));
				return $this;
			}
			
			public function loopMap( $xml, Array $collections, $html = null ){
				
			
				foreach( $xml as $tag => $each ){
					
					$nm = (string)$each->attributes()->name;
					
					if( $collections[$nm] ){
						
						$collection = $collections[$nm];
						
						$obj = $this;
						
						if( $each->template ){
							$tpl = self :: getLoopTemplate( $each );
						} else {
							Template :: addGlobals( $collection );
							continue;
						}
						
						$in = '';

						$collection->each( function($record) use( $obj, $each, $tpl, & $in, $nm ){
							$replace = $record->toArray();
							if( count($joins = $record->getJoins()) > 0 ){
								foreach( $joins as $join ){
									$joinNm = $join->getNameSpace();
									$collections = $each;
									if( $each->collections ){
										$collections = $each->collections->children();
									}
									$thisStr = $obj->loopMap( $collections, array($joinNm => $join), '{\\' . $joinNm . '}' );
									$replace[ '\\' . $joinNm ] = $thisStr;
								}
							}
							
							$in .= Template :: replace(
								$replace
							, $tpl, $each->template );
							
						});
						$html = Template :: replace(array(
							'\\' . $nm => $in
						), $html );
						
						
					}
				}
				
				return $html;
				
			}
			
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
			
			public static function getLoopTemplate( $xml ){
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
				$tpl = self :: addTemplate( $tpl );
				return $tpl;
				
			}
			
			public static function addTemplate( $path ){
				if( !isset( self :: $templates[ $path ] )){
					self :: $templates[ $path ] = file_get_contents( $path );
				}
				return self :: $templates[ $path ];
			}
			
			public static function loadCache(){
				
				// Prevent caching locally in case of dev //
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
			
			public static function getCachePath(){
				return implode('.', 
					(array)Application :: request()
				) . '.cache';
			}
			
			// Store the templates in a single file for performance //
			public static function writeCache(){
				if( Application :: config()->template_cache_path ){
					$h = fopen( Application :: config()->template_cache_path, 'w+' );
					fwrite( $h, json_encode(self :: $templates));
					fclose( $h );
				}
			}
			
			public static function loadFile( $path ){
				return self :: addTemplate( $path );
			}
			
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
			
			public static function loadSharedTemplate( $tpl ){
				$path = self :: getPath() . $tpl;
				
				return new self( $path );
			}
			
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
			
			public static function buffer( $obj ){
				self :: $buffer = $obj;
			}
			
			public static function output(){
				
				if( self :: $buffer ){
				
					// Clear remaining search strings //
					$output = self :: $buffer->getOutput();

					// Do a final replace of any globals //
					$output = Template :: replaceGlobals( $output );

					// Strip leftovers //
					$output = preg_replace("%{(\w\S*|\\\\\w\S*)}%", "", $output );

					self :: writeCache();

				}
				
				return $output;
			}
			
			public static function getBuffer(){
				if( self :: $buffer ){
					return self :: $buffer;
				}
				return false;
			}
			
		}
		
	}

?>