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
	
	// Javascript to include
	// with namespaced groups for longer browser caching
	// @since 0.1.0  //
	Document :: addJavascript( DOCUMENT_ROOT . '/__shared/javascript/jquery-1.7.2.js', 'library');
	Document :: addJavascript( DOCUMENT_ROOT . '/__shared/javascript/underscore-1.3.3.js', 'library');
	Document :: addJavascript( DOCUMENT_ROOT . '/__shared/javascript/backbone-0.9.2.js', 'library');
	
	// Styles to include
	// @since 0.1.0 //
	Document :: addStylesheet( 'public/clients/web/css/shared/reset.less' );
	
	// Minification of JavaScript using Google Closure Compiler
	// Less.php compilation of CSS files
	// @since 0.1.0 //
	
	// Minify and deploy javascript (only if local)
	// @return string Public filename of each compiled JavaScript file //
	
	if( App :: get()->local ){
		Document :: onPrepareJavascripts( function( $javascripts ){
			
			$minDir = APPLICATION_ROOT . '/public/clients/web/javascript/shared/';
			$compiler = LIB_PATH . 'java/google-closure/compiler.jar';
			$publicDir = 'public/clients/web/javascript/shared/';
			$unique = '';
			
			foreach( $javascripts as $each ){
				$unique .= filemtime( $each );
			}
			
			$filename = substr(md5( $unique ), 5, 10) . '.js';
			
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
		
		Document :: onPrepareStylesheets( function( $stylesheets ) {
			
			$minDir = APPLICATION_ROOT . '/public/clients/web/css/shared/';
			$publicDir = 'public/clients/web/css/shared/';
			$unique = '';
			
			foreach( $stylesheets as $each ){
				$unique .= filemtime( $each );
			}
			
			$filename = substr( md5( $unique ), 5, 10 ) . '.css';
			
			$minFile = $minDir . $filename;
			
			if(file_exists($minFile)){
				return $publicDir . $filename;
			}
			
			$css = '';
			
			foreach( $stylesheets as $stylesheet ){
				$less = new lessc( $stylesheet );
				$less->setFormatter("compressed");
				$css .= ' ' . $less->parse();
			}
			
			file_put_contents( $minFile, $css );
			
			return $publicDir . $filename;
			
		});
	}
	
	// Configuration for template caching
	// @since 0.1.0 //
	Config :: set(array(
		'template_cache_path' => 'app\cache\clients\web\template.cache'
	));	