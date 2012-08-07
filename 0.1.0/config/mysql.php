<?php
	
	/*
	 *	MySQL configuration
	 *	local and remote.
	 * 
	 *	@package Ant
	 *	@subpackage Configuration
	 *	@type Client
	 *	@since 0.1.0	 
	 */
	 
	Ant\Configuration :: set(array(
		'mysql_local'	=> array(
			'host'			=> 'localhost',
			'db'			=> 'ant',
			'username'		=> 'root',
			'password'		=> ''
		),
		
		'mysql_remote'	=> array(
			'host'			=> '',
			'db'			=> 'ning',
			'username'		=> 'root',
			'password'		=> ''
		)
	));
	
	// Set the table prefix (can be blank) //
	Ant\Database :: setTablePrefix( 'ant_' );