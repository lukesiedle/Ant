<?php
	
	/**
	 *	The Document wrapper 
	 *	class. Just a place to
	 *	put general HTML document
	 *	requirements.
	 * 
	 *  @package Core
	 *	@subpackage Document
	 *	@since 0.1.0
	 */
	namespace Core {
		
		Class Document {
			
			public static $title;
			public static $javascripts = array();
			public static $stylesheets = array();
			public static $onPrepareStylesheets = null;
			public static $onPrepareJavascripts = null;
			public static $processedStylesheets;
			public static $processedJavascripts;
			public static $headers = array();
			public static $postLoad = array();
			public static $output;
			
			/**
			 *	Set the document title
			 * 
			 *	@since 0.1.0
			 */
			public static function setTitle( $str ){
				self :: $title = $str;
			}
			
			/**
			 *	Get the document title
			 * 
			 *	@since 0.1.0
			 *	@return string The title
			 */
			public static function getTitle(){
				return self :: $title;
			}
			
			/**
			 *	Add a JavaScript file to the 
			 *	Document.
			 *	
			 *	@param string $src The source of the file
			 *	on the server
			 *	@param string $namespace The group the file
			 *	belongs to.
			 * 
			 *	@since 0.1.0			 
			 */
			public static function addJavascript( $src, $namespace = 'default' ){
				self :: $javascripts[ $src ] = $namespace;
			}
			
			/**
			 *	Remove a JavaScript file to the 
			 *	Document.
			 * 
			 *	@param string $src The source of the file
			 *	on the server
			 *	
			 *	@since 0.1.0			 
			 */
			public static function removeJavascript( $src ){
				unset( self :: $javascripts[ $src ] );
				unset( self :: $javascriptsPostLoad[ $src ] );
			}
			
			/**
			 *	Add a Stylesheet to the Document.
			 * 
			 *	@param string $src The source of the file
			 *	on the server
			 *	@param string $namespace The group the file
			 *	belongs to.
			 * 
			 *	@since 0.1.0			 
			 */
			public static function addStylesheet( $src, $namespace = 'default' ){
				self :: $stylesheets[ $src ] = $namespace;
			}
			
			/**
			 *	Remove a Stylesheet to the Document.
			 * 
			 *  @param string $src The source of the file
			 *	on the server
			 * 
			 *	@since 0.1.0			 
			 */
			
			public static function removeStylesheet( $src ){
				unset( self :: $stylesheets[ $src ] );
			}
			
			/**
			 *	Prepare the Stylesheets based 
			 *	on an optional callback.
			 * 
			 *	@since 0.1.0			
			 *	@return array The stylesheets, namespaced
			 */
			public static function prepareStylesheets(){
				
				self :: $processedStylesheets = self :: $stylesheets;
				
				if( self :: $onPrepareStylesheets ){
					
					self :: $processedStylesheets = array();
					
					$fn = self :: $onPrepareStylesheets;
					$group = array();
					foreach( self :: $stylesheets as $stylesheet => $namespace ){
						$group[ $namespace ][] = $stylesheet;
					}
					foreach( $group as $namespace => $stylesheets ){
						self :: $processedStylesheets [ $namespace ] = $fn( $stylesheets, $namespace );
					}
				}
				return self :: $processedStylesheets;
			}
			
			/**
			 *	Prepare the JavaScripts based 
			 *	on an optional callback.
			 * 
			 *	@since 0.1.0			 
			 *	@return array The JavaScript files, namespaced
			 */
			public static function prepareJavascripts(){
				
				self :: $processedJavascripts = self :: $javascripts;
				
				if( self :: $onPrepareJavascripts ){
					$fn = self :: $onPrepareJavascripts;
					
					self :: $processedJavascripts = array();
					
					$group = array();
					foreach( self :: $javascripts as $script => $namespace ){
						$group[ $namespace ][] = $script;
					}
					
					foreach( $group as $namespace => $scripts ){
						self :: $processedJavascripts [$namespace] = $fn( $scripts, $namespace );
					}
				}
				return self :: $processedJavascripts;
			}
			
			/**
			 *	Prepare the document with the 
			 *	various includes
			 * 
			 *	@since 0.1.0			 
			 * 
			 */
			public static function prepare(){
				self :: createHtmlIncludes();
			}
			
			/**
			 *	Create the includes in the buffer
			 * 
			 *	@since 0.1.0	 
			 */
			public static function createHtmlIncludes(){
				
				$styles			= self :: prepareStylesheets();
				$javascript		= self :: prepareJavascripts();
				
				$buffer			= Template :: getBuffer();
				
				if( ! $buffer ){
					return false;
				}
				
				if( $buffer ){
					$styleCollection = new Collection(array(),'stylesheets');
					foreach( $styles as $nm => $style ){
						$styleCollection->add(array(
							'src'		=> Router :: getPublicRoot() . $style,
							'namespace'	=> $nm
						));
					}
					$jsCollection	= new Collection(array(), 'javascripts');
					foreach( $javascript as $nm => $js ){
						$jsCollection->add(array(
							'src'			=> Router :: getPublicRoot() . $js,
							'declaration'	=> '',
							'namespace'		=> $nm
						));
					}
				}
				
				$set = new CollectionSet( $styleCollection, $jsCollection );
				
				$buffer->map( $set );
				
			}
			
			/**
			 *	Store the callback for preparing stylesheets
			 * 
			 *	@param Closure $fn The callback
			 *	
			 *	@since 0.1.0	 
			 */
			public static function onPrepareStylesheets( \Closure $fn ){
				self :: $onPrepareStylesheets = $fn;
			}
			
			/**
			 *	Store the callback for preparing JavaScripts
			 * 
			 *	@param Closure $fn The callback
			 * 
			 *	@since 0.1.0	 
			 */
			public static function onPrepareJavascripts( \Closure $fn ){
				self :: $onPrepareJavascripts = $fn;
			}
			
			/**
			 *	Add a header
			 * 
			 *	@param string $header The header string
			 * 
			 *	@since 0.1.0	 
			 */
			public static function addHeader( $header ){
				self :: $headers[strtolower($header)] = $header;
			}
			
			/**
			 *	Remove a header
			 * 
			 *	@param string $header The header
			 *	
			 *	@since 0.1.0	 
			 */
			public static function removeHeader( $header ){
				unset( self :: $headers[ strtolower($header)]);
			}
			
			/**
			 *	Get all the headers
			 * 
			 *	@since 0.1.0
			 *	@return array The list of headers
			 */
			public static function getHeaders(){
				return array_values( self :: $headers );
			}
			
			/**
			 *	Post load Closures
			 * 
			 *	@param Closure $fn The function to run
			 * 
			 *	@since 0.1.0
			 */
			public static function postLoad( \Closure $fn ){
				self :: $postLoad[] = $fn;
			}
			
			/**
			 *	Execute item buffered for post load			 
			 *	
			 *	@since 0.1.0
			 */
			public static function execPostLoad(){
				foreach( self :: $postLoad as $load ){
					$load();
				}
			}
			
		}
		
	}

?>