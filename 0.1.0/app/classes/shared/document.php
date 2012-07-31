<?php

	namespace Ant {
		
		Class Document {
			
			public static $title;
			public static $javascripts = array();
			public static $stylesheets = array();
			public static $onPrepareStylesheets = null;
			public static $onPrepareJavascripts = null;
			public static $processedStylesheets;
			public static $processedJavascripts;
			public static $headers = array();
			
			public static function setTitle( $str ){
				self :: $title = $str;
			}
			
			public static function getTitle(){
				return self :: $title;
			}
			
			public static function addJavascript( $src, $namespace = 'default' ){
				self :: $javascripts[ $src ] = $namespace;
			}
			
			public static function removeJavascript( $src ){
				unset( self :: $javascripts[ $src ] );
				unset( self :: $javascriptsPostLoad[ $src ] );
			}
			
			public static function addStylesheet( $src, $namespace = 'default' ){
				self :: $stylesheets[ $src ] = $namespace;
			}
			
			public static function removeStylesheet( $src ){
				unset( self :: $stylesheets[ $src ] );
			}
			
			public static function prepareStylesheets(){
				if( self :: $onPrepareStylesheets ){
					$fn = self :: $onPrepareStylesheets;
					$group = array();
					foreach( self :: $stylesheets as $stylesheet => $namespace ){
						$group[ $namespace ][] = $stylesheet;
					}
					foreach( $group as $namespace => $stylesheets ){
						self :: $processedStylesheets [] = $fn( $stylesheets );
					}
				}
				return self :: $processedStylesheets;
			}
			
			public static function prepareJavascripts(){
				if( self :: $onPrepareJavascripts ){
					$fn = self :: $onPrepareJavascripts;
					
					$group = array();
					foreach( self :: $javascripts as $script => $namespace ){
						$group[ $namespace ][] = $script;
					}
					
					foreach( $group as $namespace => $scripts ){
						self :: $processedJavascripts [] = $fn( $scripts );
					}
				}
				return self :: $processedJavascripts;
			}
			
			public static function prepare(){
				return self :: createHtmlIncludes();
			}
			
			public static function createHtmlIncludes(){
				$styles			= self :: prepareStylesheets();
				$javascript		= self :: prepareJavascripts();
				
				$buffer			= Template :: getBuffer();
				
				if( $buffer ){
					$styleCollection = new Collection(array(),'stylesheets');
					foreach( $styles as $style ){
						$styleCollection->add(array(
							'src' => Router :: getPublicRoot() . $style
						));
					}
					$jsCollection	= new Collection(array(), 'javascripts');
					foreach( $javascript as $js ){
						$jsCollection->add(array(
							'src'			=> Router :: getPublicRoot() . $js,
							'declaration'	=> ''
						));
					}
				}
				
				$set = new CollectionSet( $styleCollection, $jsCollection );
				
				$buffer->map( $set );
				
			}
			
			public static function onPrepareStylesheets( $fn ){
				self :: $onPrepareStylesheets = $fn;
			}
			
			public static function onPrepareJavascripts( $fn ){
				self :: $onPrepareJavascripts = $fn;
			}
			
			public static function addHeader( $header ){
				self :: $headers[strtolower($header)] = $header;
			}
			
			public static function removeHeader( $header ){
				unset( self :: $headers[ strtolower($header)]);
			}
			
			public static function getHeaders(){
				return array_values( self :: $headers );
			}
			
		}
		
	}

?>