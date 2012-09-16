<?php

	namespace Ant\Controller\Application;
	
	/*
	 *	Checks if the app is local
	 * 
	 *	@since 0.1.0
	 */
	
	function isLocal(){
		return \Ant\Application :: get()->local;
	}
