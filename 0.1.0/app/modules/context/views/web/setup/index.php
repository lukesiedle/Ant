<?php

	/*
	 *	The setup index
	 * 
	 *	@package Ant
	 *	@since 1.0
	 */

	namespace Ant\Web\Setup;
	use Ant\Collection as Collection;
	use Ant\CollectionSet as CollectionSet;
	
	function index(){
		
		$col = new Collection(array(
			'folder' => dirname( __DIR__ ) . '\\' . 
		), 'newview' );
		
		// Return the collection set for string replacement //
		return new CollectionSet( $col );
		
	}