<?php
	
	/*
	 *	Global resource allocation
	 *	
	 *	@note Database connection allocation
	 *	will need to be specific to client,
	 *	so that you don't connect for 
	 *	resources like image.php or other
	 *	non-db related requests.
	 *	
	 *	@package Ant
	 *	@subpackage Resource
	 *	@type Global
	 *	@since 0.1.0
	 */
	 
	// Load the session //
	Core\Session :: init();