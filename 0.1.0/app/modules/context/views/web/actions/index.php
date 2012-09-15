<?php

	/*
	 *	The do file. 
	 *	This is a placeholder
	 *	for the do view, which
	 *	allows controllers to 
	 *	be run beneath it.
	 *	
	 *	@package Ant
	 *	@since 1.0
	 */

	namespace Ant\Web\Actions;
	use Ant\Collection as Collection;
	use Ant\CollectionSet as CollectionSet;
	
	function index(){
		
		// Show the user some helpful info when local //
		if( \Ant\Application :: get()->local ){
			return new CollectionSet( Collection :: create('message') );
		} else {
			// Just set an error //
			\Ant\Application :: setError('404');
		}
	}