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
	
	namespace Ant;
	
	$config['mysql_local']['host']		= 'localhost';
	$config['mysql_local']['db']		= 'ant';
	$config['mysql_local']['username']	= 'root';
	$config['mysql_local']['password']	= '';
	
	$config['mysql_remote']['host']		= '';
	$config['mysql_remote']['db']		= 'ant';
	$config['mysql_remote']['username']	= 'root';
	$config['mysql_remote']['password']	= '';
	
	// Set the table prefix (can be blank) //
	$config['mysql_table_prefix']		= 'ant_';
	
	// Apply //
	Configuration :: set( $config );