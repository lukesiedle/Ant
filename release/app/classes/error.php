<?php

	namespace Core;
	
	/**
	 *	Error handler ideal
	 *	for debugging errors in
	 *	developer mode or simply
	 *	setting an HTTP error code
	 *	to be dealt with as the 
	 *	application chooses.	 
	 *	
	 *	@package Core
	 *	@subpackage Exception
	 * 
	 *	@since 0.1.0
	 */
	Class Error {
		
		public $errorCode,
					$theError;
		
		public static $errors,
						$isLoaded = false,
						$docs,	
						$errorCodes,
						$errorLog;
		
		CONST ERROR_DOC	= 'config/errors/list.xml';
		
		public static function loadErrors(){
			$xml = simplexml_load_file( self :: ERROR_DOC );
			foreach( $xml->children() as $err ){				
				$code = (string)$err->attributes()->code;
				$reason = (string)$err->attributes()->reason;
				self :: $errorCodes[ $code ][ $reason ] = array(
					'msg'	=> (string) $err->msg,
					'doc'	=> (string) $err->doc,
					'code'	=> $code
				);
			}
		}
		
		public static function getErrors(){
			$errors = array();
			foreach( self :: $errorLog as $err ){
				$errors[] = $err->theError;
			}
			return $errors;
		}
		
		public function __construct( $errorCode = 500, $reason = 'default', $silent = false ){
			
			if ( ! self :: $isLoaded ){
				self :: loadErrors();
				self :: $isLoaded = true;
			}
			
			$this->setError( $errorCode, $reason );
			self :: $errorLog[] = $this;
			
			if( $silent != 'silent' ){
				Application :: setError( $this );
			}
		}
		
		public function setError( $errorCode, $reason ){
			$this->theError = self :: $errorCodes[ $errorCode ][ $reason ];
		}
		
		public function getError(){
			return $this->theError;
		}
		
		public function getCode(){
			return $this->theError['code'];
		}
		
	}
	