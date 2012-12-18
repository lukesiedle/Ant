<?php

	namespace Data;
	
	use \Core\Resource as Resource;
	
	class Test extends \Core\Data {
		
		// Required constants //
		CONST TABLE_NAME			= 'tests';
		CONST PRIMARY_KEY			= 'test_id';
		
		// All the available fields //
		public static $fields = array(
			'id'	=> 'test_id',
			'value' => 'test_value'
		);
		
		// Strict data types //
		public static $dataTypes = array();
		
		// Minimum fields for create //
		public static $create = array(
			'test_value'
		);
		
		// Fields to query read //
		public static $read = array(
			self :: PRIMARY_KEY,
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
			$this->prepareData( 'create' );
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