<?php

	namespace Data;
	
	use \Core\Resource as Resource;
	
	class User extends \Core\Data {
		
		// Required constants //
		CONST TABLE_NAME			= 'user';
		CONST PRIMARY_KEY			= 'user_id';
		
		// Status //
		CONST USER_STATUS_INACTIVE	= 0;
		CONST USER_STATUS_ACTIVE	= 1;
		CONST USER_STATUS_BANNED	= 2;
		
		// All the available fields //
		public static $fields = array(
			'id' => 'user_id',
			'user_first_name',
			'user_last_name',
			'user_register_ut',
			'user_email'
		);
		
		// Strict data types //
		public static $dataTypes = array(
			'user_name'			=> 'string',
			'user_first_name'	=> 'string',
			'user_last_name'	=> 'string'
		);
		
		// Minimum fields for create //
		public static $create = array(
			'user_first_name',
			'user_last_name',
			'user_email'
		);
		
		// Fields to query read //
		public static $read = array(
			self :: PRIMARY_KEY,
			'user_email'
		);
		
		// Fields to query update //
		public static $update = array(
			self :: PRIMARY_KEY
		);
		
		// Fields to query delete //
		public static $delete = array(
			self :: PRIMARY_KEY
		);
		
		/**
		*	Get the specified var
		*	
		*	@since 0.2.0
		*	@return string The resource
		*/
		public function getVar( $var ){
			$fields = get_class_vars( __CLASS__ );
			if( !isset($fields[$var])){
				return array();
			}
			return $fields[ $var ];
		}
		
		/**
		 *	Manipulation or post-preparation
		 *	of data before the resource is
		 *	created
		 *	
		 *	@since 0.2.0
		 */
		public function create(){
			
			// Check if the required fields are available for this task //
			$this->prepareData( 'create' );
			
			$data = $this->getPreparedData();
			
			// Check if the email is correct //
			$errs = self :: validate( $data['user_email'], 'user_email', 'email' );
			
			if( count($errs) > 0 ){
				$this->setError( \LANG :: ERR_USER_INVALID_EMAIL );
			}
			
			// Ensure the resource doesn't exist (by email) //
			$testExists = new Resource('user', array(
				'user_email' => $data['user_email']
			));
			
			if( $user = $testExists->read() ){
				$this->setError( \LANG :: ERR_USER_ALREADY_EXISTS );
			}
			
		}
		
		/**
		 *	Manipulation or post-preparation
		 *	of data before the resource is
		 *	read
		 *	
		 *	@since 0.2.0
		 */
		public function read(){
			$this->prepareData( 'read' );
		}
		
		/**
		 *	Manipulation or post-preparation
		 *	of data before the resource is
		 *	updated
		 *	
		 *	@since 0.2.0
		 */
		public function update(){
			$this->prepareData( 'update' );
		}
		
		/**
		 *	Manipulation or post-preparation
		 *	of data before the resource is
		 *	deleted
		 *	
		 *	@since 0.2.0
		 */
		public function delete(){
			$this->prepareData( 'delete' );
		}
		
	}