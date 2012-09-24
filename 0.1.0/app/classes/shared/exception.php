<?php

	/*
	 *	Custom exception
	 *	handler
	 * 
	 *	@since 0.1.0
	 */

	namespace Ant;
	
	Class Exception extends \Exception {
		
		public function __construct( $devMessage, $errMessage ){
			parent :: construct( $errMessage );
			$this->devMessage = $devMessage;
			$this->errMessage = $errMessage;
		}
		
		public function getDevMessage(){
			return $this->devMessage;
		}
		
	}
	