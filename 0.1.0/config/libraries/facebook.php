<?php
	
	/*
	 *	Configuration for Facebook API
	 * 
	 *	@package Ant
	 *	@subpackage Configuration
	 *	@type Global
	 *	@since 0.1.0
	 * 
	 */
	
	namespace Ant;

	$config['facebook_app']['app_id'] = "203898496325452";
	$config['facebook_app']['app_secret'] = "b3fc5e22019552a7cd6615d8efd32bf8";
	$config['facebook_app']['scope'][] = "user_events";
	$config['facebook_app']['scope'][] = "friends_events";
	
	\Ant\Configuration :: set( $config );