<?php

	namespace Core;
	
	/**
	 *	The data class helps with 
	 *	transactions of data to databases
	 *	and creates a set of rules one
	 *	must follow, for a tighter
	 *	structure.
	 * 
	 *	@since 0.2.0
	 */
	abstract class Data {
		
		var $rawData,
			$preparedData,
			$errors,
			$alias,
			$name;
		
		/**
		 *	Instance accepts raw data 
		 *	and stores it for later use
		 *	
		 *	@since 0.2.0
		 */
		public function __construct( Array $data ){
			$this->rawData = $data;
			$this->setPrimaryKey( static :: PRIMARY_KEY );
			$this->setName( static :: TABLE_NAME );
		}
		
		/**
		 *	Prepare the data for the 
		 *	specified task. This will
		 *	do dependency checks to ensure
		 *	the data is available and is
		 *	of the correct type.
		 *	
		 *	@since 0.2.0
		 */
		public function prepareData( $task ){
			
			$minFieldsMet = false;
			$minFields = $this->getVar( $task );
			$dataTypes = $this->getVar('dataTypes');
			$createFields = $this->getVar('create');
			
			foreach( $this->getVar('fields') as $alias => $fieldName ){
				
				$key = $fieldName;
				
				// An alias is available //
				if( ! is_numeric( $alias ) ){
					$key = $alias;
					// Use the field name if it's available
					// and the alias is not //
					if( isset($this->rawData[ $fieldName ] ) 
						&& !isset($this->rawData[ $key ] )){
						$key = $fieldName;
					}
				}
				
				// Set the Id and Key //
				if( $fieldName == $this->getPrimaryKey() ){
					$this->setId( $this->rawData[ $key ] );
					$this->setIdKey( $this->getPrimaryKey() );
				}
				
				// Check if min create fields are met //
				if( $task == 'create' ){
					if( in_array( $fieldName, $createFields )
							&& ! isset($this->rawData[ $key ] )){
						throw new \Exception('Insufficient fields for create task.', 422 );
					}
				}
				
				if( isset($this->rawData[ $key ] ) ){
					// If a type casting definition exists, check it //
					if( $type = $dataTypes[ $fieldName ] ){
						if( count(self :: validate( $this->rawData[ $key ], $fieldName, $type )) > 0 ){
							throw new \Exception( 'Insufficient values for fields (' 
								. $fieldName . ') in ' . $task );
						}
					}
					$this->preparedData[ $fieldName ] = $this->rawData[ $key ];
					
					// Check if at least one field is available for other tasks //
					switch( $task ){
						case 'create' :
							$minFieldsMet = true;
							break;
						case 'update' :
						case 'read' :
						case 'delete' :
							// Just needs at least one field set //
							if( ! $minFieldsMet ){
								foreach( $minFields as $field ){
									if( $this->preparedData[$field] ){
										$minFieldsMet = true;
										break;
									}
								}
							}
							break;
					}
				}
			}
			
			if( ! $minFieldsMet ){
				throw new \Exception( 'Insufficient fields for ' . $task . ' task.'
					. ' Requires one of the following: ' . implode(',',$minFields), 422 );
			}
			
		}
		
		/**
		 *	Set the task
		 *	
		 *	@since 0.2.0
		 */
		public function setTask( $task ){
			$this->task = $task;
			$this->{ $task }();
		}
		
		
		/**
		 *	Set (by extending) the raw data
		 * 
		 *	@since 0.1.0
		 *	@return object The object for chaining
		 */
		public function setData( $data ){
			$this->rawData = array_merge( $this->rawData, $data );
			return $this;
		}
		
		/**
		 *	Get the raw data
		 *	
		 *	@since 0.1.0
		 *	@return array The data
		 */
		public function getData(){
			return $this->rawData;
		}

		/**
		 *	Get the prepared data
		 *	
	 	 *	@since 0.2.0
		 *	@return array The data
		 */
		public function getPreparedData(){
			return $this->preparedData;
		}
	
		/**
		 *	Sets the primary key
		 *	For use with a Collection
		 *	
		 *	@param string $key The key 
		 *	used for an Id, 'user_id' 'article_id'
		 * 
		 *	@since 0.1.0
		 */
		private function setPrimaryKey( $key ){
			$this->primaryKey = $key;
		}
		
		/**
		*	Gets the primary key
		*	For use with a Collection	
		*	
		*	@since 0.1.0
		*	@return string The primary key
		*/
		public function getPrimaryKey(){
			return $this->primaryKey;
		}

		/**
		*	Sets the primary key
		*	For use with a Collection
		*	
		*	@param string $key The key 
		*	used for an Id, 'user_id' 'article_id'
		* 
		*	@since 0.1.0
		*/
		private function setIdKey( $key ){
			$this->idKey = $key;
		}

		/**
		*	Gets the primary key
		*	For use with a Collection	
		*	
		*	@since 0.1.0
		*	@return string The primary key
		*/
		public function getIdKey(){
			return $this->idKey;
		}

		/**
		*	Sets the fields readable
		*	by the script. This may be
		*	modified by the permissions
		*	controller, and thin down
		*	the readable fields depending
		*	on the current user.
		*	
		*	@since 0.1.0
		*/
		public function setReadableFields( $fields ){
			$this->readableFields = $fields;
		}

		/**
		*	Gets the fields readable
		*	by the script
		*	
		*	@since 0.1.0
		*	@return array The readable 
		*	field names (column names)
		*/
		public function getReadableFields(){
			return $this->getVar('fields');
		}

		/**
		*	Set the resource Id for use
		*	with updates, reads or deletes
		*	This doesn't have to be the primary
		*	key, but since it must be unique
		*	this is often the case.
		*	
		*	@param int $id The Id
		* 
		*	@since 0.1.0
		*/
		public function setId( $id ){
			$this->resourceId = $id;
		}

		/**
		*	Get the Id for use
		*	with updates, reads or deletes
		* 
		*	@since 0.1.0
		*	@return int The resource Id
		*/
		public function getId(){
			return $this->resourceId;
		}
		
		public function fields( $type ){
			return $this->getVar( $type );
		}
		
		/**
		 *	Get the true name of the resource
		 *	that is reflected in the database.
		 * 
		 *	@since 0.1.0
		 *	@return string The resource name
		 */
		public function getName(){
			return $this->resource;
		}
		
		/**
		 *	Set the true name of the resource, 
		 *	for use in the database transactions
		 *	of the Resource class.
		 * 
		 *	@since 0.1.0
		 *	@return string The resource name
		 */
		public function setName( $nm ){
			$this->resource = $nm;
		}
		
		/**
		 *	Sets an error
		 * 
		 *	@since 0.2.0
		 */
		public function setError( $err, $errCode = 422 ){
			$this->errors[] = array(
				'value' => $err,
				'code'	=> $errCode
			);
		}
		
		/**
		 *	Get errors
		 * 
		 *	@since 0.2.0
		 */
		public function getErrors(){
			return $this->errors;
		}
		
		/**
		 *	Removes error
		 * 
		 *	@since 0.2.0
		 */
		public function removeError( String $err, $errCode = 422 ){
			foreach( $this->errors[ $errCode ] as $i => $error ){
				if( $err == $error ){
					unset( $this->errors[ $errCode ][$i] );
				}
			}
		}
		
		/**
		*	Basic PHP native validation
		* 
		*	@param string $value The value 
		*	passed in
		*	@param string $key The key to 
		*	preserve for failures
		*	@param string $type The type of 
		*	value expected 'email' 'int'
		* 
		*	@since 0.1.0
		*	@return array The failed fields
		*/
		public static function validate( $value, $key, $type ){
			
			$fails = array();
			
			switch( $type ){
				case 'email' :
					if( ! filter_var( $value, FILTER_VALIDATE_EMAIL )){
						$fails[ $key ] = $value;
					}
					break;

				case 'int' :
					if( ! filter_var( $value, FILTER_VALIDATE_INT )){
						$fails[ $key ] = $value;
					}
					break;
				default : 
					if( strlen( $value ) == 0 ){
						$fails[ $key ] = $value;
					}
					break;
			}

			return $fails;

		}
		
		/**
		 *	Sanitization of a string
		 *	usually submitted by a user	
		 *	
		 *	@param string $data The string
		 *	to sanitize
		 *	@param string $type The type 
		 *	of sanitization. Currently only 'html' 
		 *	is available.
		 *	
		 *	@since 0.1.0
		 *	@return string The sanitized string
		 */
		public static function sanitize( $data, $type ){
			switch( $type ){
				case 'html' :
					return strip_tags( $data );
					break;
			}
		}
		
		/**
		 *	Get specific var declared	
		 *	in child classes
		 * 
		 *	@since 0.2.0
		 */
		abstract function getVar( $type );
		
		/**
		 *	For handling create
		 * 
		 *	@since 0.2.0
		 */
		abstract function create();
		
		/**
		 *	For handling read
		 * 
		 *	@since 0.2.0
		 */
		abstract function read();
		
		/**
		 *	For handling update
		 * 
		 *	@since 0.2.0
		 */
		abstract function update();
		
		/**
		 *	For handling delete
		 * 
		 *	@since 0.2.0
		 */
		abstract function delete();
		
	}