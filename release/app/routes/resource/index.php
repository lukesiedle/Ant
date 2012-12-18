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
		
		'/resource'									=> '\Route\Main\index',
		
		// Create //
		'/resource/:string'							=> '\Route\Main\index',
		
		// User Edit/Delete/Update //
		'/resource/:string/:number'					=> '\Route\Main\index',
		
		// Sub resource create //
		'/resource/:string/:number/:string'			=> '\Route\Main\index',
		
		// Sub resource Edit/Delete/Update
		'/resource/:string/:number/:string/:number'	=> '\Route\Main\index'
		
	);