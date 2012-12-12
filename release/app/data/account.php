<?php

	namespace Data;
	
	use \Core\Database as DB;
	use \Core\Application as App;
	
	class Account extends \Core\Data {
		
		// Required constants //
		CONST TABLE_NAME			= 'user_account_user';
		CONST PRIMARY_KEY			= '_id';
		CONST JOIN_KEY				= 'user_account_id';
		
		// Account Types //
		CONST ACCOUNT_GOOGLE		= 1;
		CONST ACCOUNT_FACEBOOK		= 2;
		
		// All the available fields //
		public static $fields = array(
			'_id',
			'user_id',
			'user_account_type_id',
			'last_update_ut',
			'id' => 'user_account_id'
		);
		
		// Strict data types //
		public static $dataTypes = array(
			'user_id'				=> 'int'
		);
		
		// Minimum fields for create //
		public static $create = array(
			'user_id',
			'user_account_type_id',
			'last_update_ut',
			'user_account_id'
		);
		
		// Fields to query read //
		public static $read = array(
			self :: PRIMARY_KEY,
			self :: JOIN_KEY,
			'user_id'
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
		 *	Manipulation or pre-preparation
		 *	of data before the resource is
		 *	created
		 *	
		 *	@since 0.2.0
		 */
		public function create(){
			
			$rawData = $this->getData();
			
			switch( $rawData['auth_type'] ){
				case 'google' :
					$rawData['user_account_type_id'] = self :: ACCOUNT_GOOGLE;
					$rawData['user_first_name'] = $rawData['given_name'];
					$rawData['user_last_name'] = $rawData['family_name'];
					break;
				case 'facebook' :
					$rawData['user_account_type_id'] = self :: ACCOUNT_FACEBOOK;
					break;
			}
			
			$this->setData( $rawData );
			
			// Check if the required fields are available for this task //
			$this->prepareData( 'create' );
			
		}
		
		/**
		 *	Manipulation or pre-preparation
		 *	of data before the resource is
		 *	read
		 *	
		 *	@since 0.2.0
		 */
		public function read(){
			$this->prepareData( 'read' );
		}
		
		/**
		 *	Modify the read query
		 *	in case another table's data
		 *	is relevant to the read.
		 * 
		 *	@since 0.2.0
		 */
		public function __beforeRead( $query ){
			
			$query->select( 'type.user_account_type' )
				->join( DB :: _() . 'user_account_type type
					ON this.user_account_type_id 
					= type.user_account_type_id' );
			
			return $query;
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