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
		
		// Specific routing  //
		'/test/routing/deep/deeper/deepest/'	=> '\Route\Main\deepest',
		'/test/routing/testframe'				=> '\Route\Main\testframe',
		'/test/templating/frame'				=> '\Route\Main\loopframe',
		'/test/templating/page'					=> '\Route\Main\index',
		'/test/templating/deep'					=> '\Route\Main\index',
		
		// Generic routing //
		'/'					=> '\Route\Main\index',
		'/:string'			=> '\Route\Main\index',
		'/:string/:number'	=> '\Route\Main\index',
		'/:string/:string'	=> '\Route\Main\index',
		
	);