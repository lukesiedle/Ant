<?php
	
	/*
	 *	Resource takes care
	 *	of RESTFul / CRUD
	 *	structure, and data
	 *	validation, sanitization.
	 *	
	 *	@since 0.1.0
	 *	@package Ant
	 *	@subpackage Resource
	 * 
	 */

	namespace Ant {
		
		Class Resource {
			
			/*
			 *	Constructor initializes
			 *	the resource in use, and 
			 *	prepares for CRUD task.
			 * 
			 *	@since 0.1.0
			 */
			
			public function __construct( $resource, $data = array()){
				
				$this->resource = $resource;
				$this->data		= $data;
				
				// Generate a default or custom model //
				$this->model	= Model :: make( $resource );
				
				// Try set the resource Id //
				$this->setId( $this->data['id'] );
				
				// Get the schema of this resource //
				$this->getSchema();
			}
			
			/*
			 *	Get the resource
			 *	(the name)
			 * 
			 *	@since 0.1.0
			 *	@return string The resource
			 */
			
			public function getResource(){
				return $this->resource;
			}
			
			/*
			 *	Gets the schema of the
			 *	specified resource
			 * 
			 *	@since 0.1.0
			 */
			
			public function getSchema(){				
				
				// Get the XML schema //
				$schemaFile = 'app/classes/models/schema/' 
						. $this->resource . '.xml';
				
				// If it doesn't exist, throw error //
				if( file_exists( $schemaFile )){
					$xml = simplexml_load_file( $schemaFile );
				} else {
					throw new \Exception('You must specify a schema for resource ' 
						. $this->resource, 404 );
				}
				
				// Get all the fields, type casting, 
				// and validation/sanitization options //
				foreach( $xml->data as $set ){
					foreach( $set as $field ){
						$key = (string) $field;
						$this->fields[ $key ] = array(
							'type' => (string)$field->attributes()->type,
							'ALLOW_HTML' => $field->attributes()->allowHTML == 'true',
							'key' => $key
						);
						
						// Use a different key for receiving data via request //
						if( $field->attributes()->use ){
							$this->fields[ $key ]['key'] = (string) $field->attributes()->use;
						}
						
						// Set the primary key for the resource //
						if( $field->attributes()->pk ){
							$this->setPrimaryKey( $key );
						}
						
						$readable[] = $key;
					}
				}
				
				// Readable fields default to all //
				$this->setReadableFields($readable);
				
				// Configure the CRUD tasks //
				foreach( $xml->tasks as $set ){
					foreach( $set as $crud => $fieldSet ){
						$fields = array();
						foreach( $fieldSet as $field ){
							
							// Task attributes can specifiy an alias key //
							if( $field->attributes()->use ){
								$fields[ (string) $field->attributes()->use ] = (string) $field;
							} else {
								$fields[ (string) $field ] = (string) $field;
							}
							
						}
						$this->crud[ $crud ] = $fields;
					}
				}
			}
			
			/*
			 *	Compares the schema
			 *	against the provided
			 *	input and validates,
			 *	sanitizes the data.
			 * 
			 *	@since 0.1.0
			 *	
			 */
			
			function compareSchema( $operation = 'create' ){
				
				switch( $operation ){
					
					case 'create'	:
					case 'update'	:	
					case 'delete'	:
						
						// Identify all possible fields and check/sanitize //
						foreach( $this->fields as $key => $each ){
						
							// Check if field value types are correct //
							if( isset($this->data[ $each['key'] ] )){
								if( count(self :: validate( 
									$this->data[ $each['key'] ], 
									$each['key'],
									$each['type'] )) > 0 ){
									throw new \Exception('Incorrect or insufficient values or value types for ' 
										. $operation . ' task in ' . $this->resource, 0 );
								}
							}
							
							// Sanitization in create, update //
							if( in_array($operation, array( 'update','create' )) ){
								if( ! isset( $each['ALLOW_HTML'] )){
									$this->data[ $each ] = self :: sanitize( $this->data[ $each ], 'html' );
								}
							}
							
						}
						
						// Check if minimum fields are met // 
						foreach( $this->crud[ $operation ] as $key => $each ){							
							if( ! isset( $this->data[ $key ] ) ){
								throw new \Exception('Insufficent data for ' 
									. $operation . ' task in ' . $this->resource
										. '. Requires ' . implode(', ', $this->crud[ $operation ], 0 ) 
								);
							}
						}
						
						break;
					case 'read' :
						
						// For reads //
						$read = false;
						
						foreach( $this->crud[ $operation ] as $key => $each ){
							if( isset( $this->data[ $key ])){
								$read = true;
								// Check if field value types are correct //
								if( count(self :: validate( 
									$this->data[ $key ], 
									$key,
									$this->fields[ $each ] )
								) > 0 ){
									throw new \Exception('Incorrect values or value types for
										' . $operation . ' task in ' . $this->resource, 0 );
								}
							}
						}
						
						// Check if minimum field values are met //
						if( ! $read ){
							throw new \Exception('Insufficent data for ' 
								. $operation . ' task in ' . $this->resource 
									. '. Requires ' . implode(' OR ', $this->crud[ $operation ] ) 
							);
						}
						
						break;
				}
				
			}
			
			/*
			 *	Basic PHP native validation
			 * 
			 *	@since 0.1.0
			 *	@return array The failed fields
			 */
			
			private static function validate( $value, $key, $type ){
				
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
			
			/*
			 *	Sanitization of a string
			 *	usually submitted by a user	
			 *	
			 *	@since 0.1.0
			 *	@return string The sanitized string
			 */
			
			private static function sanitize( $data, $type ){
				switch( $type ){
					case 'html' :
						return strip_tags( $data );
						break;
				}
			}
			
			/*
			 *	Check if the task is 
			 *	valid set it
			 * 
			 *	@since 0.1.0
			 */
			
			public function setTask( $task ){
				switch( $task ){
					case 'create' :
					case 'read' :
					case 'update' :
					case 'delete' :
						// Set the task //
						$this->task = $task;
						break;
						default : 
							throw new \Exception('Invalid task "' . $task . '"' );
				}
				
				// Set permissions
				$this->setPermissions();
				
				// Check the data complies //
				$this->compareSchema( $task );
				
				
			}
			
			/*
			 *	Check if the user has permissions
			 *	for the current task and set them.
			 * 
			 *	@since 0.1.0
			 *	
			 */
			
			public function setPermissions(){
				
				try {
					
					$perms = \Ant\Controller :: call( $this->getResource() . '.permission', array(
						'resource'	=> $this,
						'task'		=> $this->getTask(),
						'data'		=> $this->getData()
					));
					
					// Set the readable fields if an array was returned //
					if(isset($perms['read'])){
						if( ! $perms['owner'] ){
							$this->setReadableFields( $perms['read'] );
							return $perms['allow'];
						}
						return true;
					}
					
				} catch ( \Exception $e ){
					// No permissions are defined //
					return true;
				}
				
				// Check if not allowed and throw forbidden //
				if( ! $perms['allow'] ){
					throw new \Exception( 'Insufficient permissions for this task.', 403 );
				}
				
				return $perms['allow'];
			}
			
			/*
			 *	Get the task
			 * 
			 *	@since 0.1.0
			 *	@return string The task
			 */
			
			public function getTask(){
				return $this->task;
			}
			
			/*
			 *	Do a task
			 * 
			 *	@since 0.1.0
			 */
			
			public function doTask( $task ){
				return $this->{ $task }();
			}
			
			/*
			 *	Get the data
			 * 
			 *	@since 0.1.0
			 *	@return array The data
			 */
			
			public function getData(){
				return $this->mapData();
			}
			
			/*
			 *	Map data according to input
			 *	
			 *	@since 0.1.0
			 *	@return array The data
			 */
			
			public function mapData(){
				
				$result = array();
				foreach( $this->fields as $fieldName => $field ){
					if( $this->data[ $field['key'] ] ){
						$result[ $fieldName ] = $this->data[ $field['key'] ];
					}
				}				
				return $result;
				
			}
			
			/*
			 *	Sets the primary key
			 *	For use with a Collection	
			 *	
			 *	@since 0.1.0
			 */
			
			private function setPrimaryKey( $key ){
				$this->primaryKey = $key;
			}
			
			/*
			 *	Gets the primary key
			 *	For use with a Collection	
			 *	
			 *	@since 0.1.0
			 */
			
			public function getPrimaryKey(){
				return $this->primaryKey;
			}
			
			/*
			 *	Sets the fields readable
			 *	by the script
			 *	
			 *	@since 0.1.0
			 */
			
			public function setReadableFields( $fields ){
				$this->readableFields = $fields;
			}
			
			/*
			 *	Gets the fields readable
			 *	by the script
			 *	
			 *	@since 0.1.0
			 */
			
			public function getReadableFields(){
				return $this->readableFields;
			}
			
			/*
			 *	Shortcut to CRUD methods
			 *	inside the model
			 *	
			 *	@since 0.1.0
			 * 
			 */
			
			public function read(){
				$this->setTask('read');
				return $this->model->read( $this );
			}
			
			public function create(){
				$this->setTask('create');
				return $this->model->create( $this );
			}
			
			public function update(){
				$this->setTask('update');
				return $this->model->update( $this );
			}
			
			public function delete(){
				$this->setTask('delete');
				return $this->model->delete( $this );
			}
			
			/*
			 *	Set the Id for use
			 *	with updates, reads or deletes
			 * 
			 *	@since 0.1.0
			 */
			
			public function setId( $id ){
				$this->resourceId = $id;
			}
			
			/*
			 *	Get the Id for use
			 *	with updates, reads or deletes
			 * 
			 *	@since 0.1.0
			 */
			
			public function getId(){
				return $this->resourceId;
			}
			
		}
	}
