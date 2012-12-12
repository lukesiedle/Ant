<?php
	
	/**
	 *	All the possible routes,
	 *	easy to read and identify,
	 *	also forms a "whitelist" of
	 *	acceptable URIs.
	 *	
	 *	@since 0.2.1
	 */
	 
	return array(
		
		// Generic routes //
		
		'/resource'					=> '\Route\Main\index',
		
		// User //
		'/resource/:string'			=> '\Route\Main\index',
		
		// User Edit/Delete/Update //
		'/resource/:string/:number'	=> '\Route\Main\index'
		
	);