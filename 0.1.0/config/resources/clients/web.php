<?php
	
	/*
	 *	Web resource allocation
	 *	
	 *	@package Ant
	 *	@subpackage Resource
	 *	@type Client
	 *	@since 0.1.0
	 * 
	 */

	// Set useful namespaces //
	use Ant\Application as App;
	use Ant\Configuration as Config;
	use Ant\Document as Document;
	
	// Mysql connection
	// @since 0.1.0 //
	$mysql = Config :: get('mysql_remote');
	
	if( App :: get()->local ){
		$mysql = Config :: get('mysql_local');
	}
	
	App :: connect( array_merge(
		$mysql, array(
			'port'			=> '3306',
			'timeout'		=> '15',
			'connection'	=> 'mysql'
		)
	));
	
	$resources = simplexml_load_file( __DIR__ . '/includes/web.xml' );
	
	// Get resources from XML //
	$resources = $resources->children();
	
	if( is_object($resources)){
		foreach( $resources as $type => $resource ){
			foreach( $resource as $ns => $files ){
				foreach( $files as $each ){
					
					// Build the path to each file //
					$path = $each->attributes()->path;
					if( $path && $const = constant( $path ) ){
						$path = $const .= (string) $each;
					} else {
						$path = (string) $each;
					}
					
					// Error checking //
					if( !file_exists( $path )){
						throw new Exception('Resource @ ' . $path . ' does not exist, and cannot be allocated.');
					}
					
					switch( $type ){
						
						// Javascript to include
						// with namespaced groups for longer browser caching
						// @since 0.1.0  //
						case 'javascript' :
							Document :: addJavascript( $path, (string) $ns );
							break;
						
						// Styles to include
						// @since 0.1.0 //
						case 'stylesheets' :
							Document :: addStylesheet( $path, (string) $ns );
							break;
					}
					
				}
			}
		}
	}
	
	// Configuration for template caching
	// @since 0.1.0 //
	Config :: set(array(
		'template_cache_path' => 'app\cache\clients\web\template.cache'
	));	
	
	// Minification of JavaScript using Google Closure Compiler
	// Less.php compilation of CSS files
	// @since 0.1.0 //
	
	// Minify and deploy javascript (only if local)
	// @return string Public filename of each compiled JavaScript file //
	
	if( App :: get()->local ){
		Document :: onPrepareJavascripts( function( $javascripts, $namespace ){
			
			$minDir = APPLICATION_ROOT . '/public/clients/web/generated/javascript/';
			$compiler = LIB_PATH . '/java/google-closure/compiler.jar';
			$publicDir = 'public/clients/web/generated/javascript/';
			$unique = '';
		
			
			foreach( $javascripts as $each ){
				$unique .= filemtime( $each );
			}
			
			$filename = $namespace . '_' . substr(md5( $unique ), 5, 10) . '.js';
			
			$minFile = $minDir . $filename;
			
			if(file_exists($minFile)){
				return $publicDir . $filename;
			}
			
			$scripts = ' --js ' . implode( ' --js ', $javascripts );
			$comm = 'java -jar ' . $compiler . ' ' . $scripts . ' --js_output_file ' . $minFile;			
			
			exec( $comm );
			
			return $publicDir . $filename;

		});
	}
	
	// Compile less.css stylesheets and deploy
	// @return string Public filename of each compiled stylesheet //
	if( App :: get()->local ){
		
		Document :: onPrepareStylesheets( function( $stylesheets, $namespace ) {
			
			$minDir = APPLICATION_ROOT . '/public/clients/web/generated/css/';
			$publicDir = 'public/clients/web/generated/css/';
			$unique = '';
			$compiler = dirname(APPLICATION_ROOT) . '/lib/php/less.java.jar';
			
			foreach( $stylesheets as $each ){
				$unique .= filemtime( $each );
			}
			
			$filename = $namespace . '_' . substr( md5( $unique ), 5, 10 ) . '.css';
			
			$minFile = $minDir . $filename;
			
			if(file_exists($minFile)){
				return $publicDir . $filename;
			}
			
			$glue = ' --less_file ' . APPLICATION_ROOT . '/';
			$scripts = $glue . implode( $glue , $stylesheets );
			
			$comm = 'java -jar ' . $compiler . ' ' . $scripts . ' --output_file ' . $minFile;			
			
			exec( $comm );
			
			return $publicDir . $filename;
			
		});
	}