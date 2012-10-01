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
			
			public $keys = array();
			public static $xml = array();
			public static $storage = array();
			
			/**
			 *	Constructor initializes
			 *	the resource in use by name, and 
			 *	prepares for CRUD task.
			 * 
			 *	@param string $resource The resource 'user','article'
			 *	@param array $data The data provided, often
			 *	by a POST, GET.
			 *	
			 *	@since 0.1.0
			 */
			public function __construct( $resource, $data = array()){
				
				$this->resource = strtolower( $resource );
				$this->data		= $data;
				
				// Get the schema of this resource //
				$this->setSchema();
			}
			
			/**
			 *	Get the resource
			 *	(the named resource)
			 * 
			 *	@since 0.1.0
			 *	@return string The resource
			 */
			public function getResource(){
				return $this->resource;
			}
			
			/**
			 *	Sets the schema of the
			 *	specified resource. The schema
			 *	is stored inside an xml file
			 *	and offers directives about what
			 *	data exists and how it can be
			 *	accessed.
			 *	
			 *	@since 0.1.0
			 */
			public function setSchema(){				
				
				// Get the XML schema //
				$schemaFile = 'app/classes/models/schema/' 
						. $this->resource . '.xml';
				
				// Don't load xml more than once //
				if( self :: $xml[ $this->resource ] ){
					$xml = self :: $xml[ $this->resource ];
				} else {
					// If it doesn't exist, throw error //
					if( file_exists( $schemaFile )){
						$xml = simplexml_load_file( $schemaFile );
						self :: $xml[ $resource ] = $xml;
					} else {
						throw new \Exception('You must specify a schema for resource ' 
							. $this->resource, 404 );
					}
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
						
						// Store the alias/actual keys //
						$this->keys[ $key ] = $this->fields[ $key ]['key'];
						
						// Set the primary key for the resource //
						if( $field->attributes()->pk ){
							$this->setPrimaryKey( $key );
							$this->setIdKey( $key );
							$this->setId( $this->data[ $this->fields[ $key ]['key'] ] );
						}
						
						// Set an Id key for the resource //
						if( $field->attributes()->id ){
							$this->setIdKey( $key );
							$this->setId( $this->data[ $this->fields[ $key ]['key'] ] );		
							
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
							// Get the key being used for this field //
							$fields[ $this->keys[(string)$field ] ] = (string)$field;
						}
						
						$this->crud[ $crud ] = $fields;
					}
				}
			}
			
			/**
			 *	Compares the schema
			 *	against the provided
			 *	input and validates,
			 *	sanitizes the data.
			 * 
			 *	@since 0.1.0
			 */
			function compareSchema(){
				
				$operation = $this->task;
				
				switch( $this->task ){
					
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
									throw new \Exception('Incorrect or insufficient values ('.$each['key'].') or value types for ' 
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
						
						if( $this->task == 'create' || $this->task == 'delete' ){
							// Check if minimum fields are met // 
							foreach( $this->crud[ $operation ] as $key => $each ){							
								if( ! isset( $this->data[ $key ] ) ){
									throw new \Exception('Insufficent data for ' 
										. $operation . ' task in ' . $this->resource
											. '. Requires ' . implode(', ', $this->crud[ $operation ] ), 0 
									);
								}
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
			
			public function crudFields( $task ){
				return $this->crud[ $task ];
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
			
			/**
			 *	Sanitization of a string
			 *	usually submitted by a user	
			 *	
			 *	@param string $data The string
			 *	to sanitize
			 *	@param string $type The type 
			 *	of sanitization. Currently only 'html' 
			 *	is avaiable.
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
			
			/**
			 *	Check if the task is 
			 *	valid set it
			 * 
			 *	@param string $task The task:
			 *	only 'create' 'read' 'update' 'delete'
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
				$this->compareSchema();
				
			}
			
			/**
			 *	Check if the user has permissions
			 *	for the current task and set them.
			 *	
			 *	@since 0.1.0
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
			
			/**
			 *	Get the task
			 * 
			 *	@since 0.1.0
			 *	@return string The task
			 */
			public function getTask(){
				return $this->task;
			}
			
			/**
			 *	Execute a task. This first sets the task
			 *	and may throw an error if the requirements
			 *	are not met.
			 *	
			 *	@param string $task The CRUD task 
			 * 
			 *	@since 0.1.0
			 */
			public function doTask( $task ){
				return $this->{ $task }();
			}
			
			/**
			 *	Set the data
			 *	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function setData( $data, $reverseLookup = 'reverseLookup' ){
				
				// Get the expected key names //
				if( $reverseLookup == 'reverseLookup' ){
					foreach( $this->fields as $fieldName => $field ){
						if( $data[ $fieldName ] ){
							$this->data[ $field['key'] ] = $data[ $fieldName ];
						}
					}
				} else {
					$this->data = $data;
				}
				
				$this->setSchema();
				
				return $this;
			}
			
			/**
			 *	Get the mapped data. Data
			 *	will be returned using key
			 *	value pairs according to the
			 *	XML schema.
			 *	
			 *	@since 0.1.0
			 *	@return array The data
			 */
			public function getData( $key = null ){
				$data = $this->mapData();
				if( $key ){
					return $data[ $key ];
				}
				return $data;
			}
			
			/**
			 *	Map data according to input
			 *	and the schema
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
				return $this->readableFields;
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
			
			/**
			 *	Get the name of the resource			 
			 * 
			 *	@since 0.1.0
			 *	@return string The resource name
			 */
			public function getName(){
				return $this->resource;
			}
			
			/**
			 *	The create task
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function create(){
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$this->getData(), 
					$this->resource
				);
				
				// Pass to Database for insertion, set the Id //
				$resource = $this;
				Database :: insert( $collection, function( $id ) use( $collection, $resource ){
					$collection->first()->add(array(
						$resource->getPrimaryKey() => (int) $id
					));
				});
				
				// Store the resource as a collection in memory //
				self :: store( $collection, $this->getName() );
				
				// Reset the data //
				$resource->setData( $collection->first()->toArray() );
				
				return $this;
			}
			
			/**
			 *	The read task. Uses the 
			 *	read fields specified in schema
			 *	for the query, and only reads
			 *	acceptable fields.
			 * 
			 *	@since 0.1.0
			 *	@return Collection The data read
			 */
			public function read(){			
				
				if( $storedData = self :: getStoredData( $this->getName()) ){
					return $storedData;
				}
				
				$query = new \Ant\Query;
				
				$query->select( 
					implode(',', $this->getReadableFields()),
					\Ant\Database :: getTablePrefix() . $this->getName()
				);	
				
				// Try getting the resource using any acceptable read fields //
				$data = $this->getData();
				$wh = '';
				$i=0;
				
				foreach( $this->crudFields('read') as $field ){
					
					// Skip this field if not set //
					if( is_null($data[$field] )){
						continue;
					}
					
					// Append to the where //
					$bind[ $field ] = $data[ $field ];
					if( $i > 0 ){ $wh .= ' || '; }
					$wh .= '(' . $field . ' = :' . $field . ')';
					$i++;
				}
				
				$query->where( $wh, $bind );
				
				$collection = \Ant\Database :: query( $query );
				
				// No results from query //
				if( $collection->length() == 0 ){
					throw new \Exception( 'Resource does not exist', 404 );
				}
				
				// Store the resource as a collection in memory //
				self :: store( $collection, $this->getName() );
				
				// Return array data //
				if( $collection->length() == 1 ){
					return $collection->first()->toArray();
				}
				
				return $collection->toArray();
			}
			
			/**
			 *	The default update task
			 *	
			 *	@param Resource $resource
			 *  
			 *	@since 0.1.0
			 *	@return Collection The data updated/read
			 */
			public function update(){
				
				$data	= $this->getData();
				$id		= $this->getId();
				$idKey	= $this->getIdKey();
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$data,
					$this->getName()
				);
				
				// Update the database //
				\Ant\Database :: update( $collection, array(
					$idKey => $id
				));
				
				// Store the resource as a collection in memory //
				self :: store( $collection, $this->getName() );
				
				// Reset the data //
				$this->setData( $collection->first()->toArray() );
				
				return $this;
			}
			
			/**
			 *	The default delete task
			 * 
			 *	@param Resource $resource
			 *	
			 *	@since 0.1.0
			 *	@return bool The success
			 */
			public function delete(){
				return $this;
			}
			
			/**
			 *	Store and extend the resource
			 *	as a Collection in memory. This reduces
			 *	'read' calls to the database but can
			 *	put greater load on memory if the 
			 *	resources are large
			 *	
			 *	@param array $col The Collection
			 * 
			 *	@uses Collection
			 *	@since 0.1.0
			 */
			public static function store( $col, $resourceName ){
				if( $store = self :: $storage[ $resourceName ] ){
					self :: $storage[ $resourceName ] = Collection :: merge( $store, $col );
				} else {
					self :: $storage[ $resourceName ] = $col;
				}
			}
			
			/**
			 *	Get the stored data. 
			 * 
			 *	@param string $resourceName The named resource
			 *	
			 *	@uses Collection
			 *	@since 0.1.0
			 *	@return array The Collection data
			 */
			public static function getStoredData( $resourceName ){
				if( $col = self :: $storage[ $resourceName ] ){
					if( $col->length() == 1 ){
						return $col->first()->toArray();
					}
					return $col->toArray();
				}
				return false;
			}
			
		}
	}
