<?php

	/*
	 *	The setup index
	 * 
	 *	@package Ant
	 *	@since 1.0
	 */

	namespace View\Desktop\Setup;
	use \Core\Collection as Collection;
	use \Core\CollectionSet as CollectionSet;
	
	function index(){
		
		$col = new Collection(array(
			'folder'	=> dirname( __DIR__ ) . '\\newview',
			'file'		=> dirname( __DIR__ ) . '\\newview\index.php',
			'name'		=> 'NewView',
			'html'		=> APPLICATION_ROOT . '\public\clients\web\templates\default\context\newview\index.html',
			'route'		=> APPLICATION_ROOT . '\app\views\shared\views\web\route.xml',
			'newroute'	=> APPLICATION_ROOT . '\app\views\context\views\web\newview\route.xml',
			'less'		=> APPLICATION_ROOT . '\public\clients\web\css\shared\my_styles.less',
			'js'		=> APPLICATION_ROOT . '\public\clients\web\javascript\shared\my_plugin.js',
			'resources' => APPLICATION_ROOT . '\app\config\resources\clients\includes\web.xml',
			'class'		=> APPLICATION_ROOT . '\app\classes\context\newview\newview.php',
			'controller' => APPLICATION_ROOT . '\app\views\context\controllers\newview\dostuff.php'
			
		), 'newview' );
		
		// Test for a database error //
		$dbError	= \Core\Application :: get()->errors['mysql'];
		
		$status = new Collection(array(
			'database'	=> $dbError ? $dbError : 'OK.',
			'class'		=> $dbError ? 'red' : 'green',
			'config'	=> APPLICATION_ROOT . '\config\mysql.php'
		), 'status' );
		
		// Return the collection set for string replacement //
		return new CollectionSet( $col, $status );
		
	}