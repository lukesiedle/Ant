<?php
	
	/**
	 *	Resource client 
	 *	resource allocation
	 *	
	 *	@package Ant
	 *	@subpackage Resource
	 *	@since 0.2.1
	 */
	 
	use Core\Application as App;
	use Core\Configuration as Config;
	
	// Mysql connection
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