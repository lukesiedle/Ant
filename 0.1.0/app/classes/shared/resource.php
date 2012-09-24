<?php

	/*
	 *	Model takes care
	 *	of the representation
	 *	of a context as a resource
	 *	
	 *	@since 0.1.0
	 *	@package Ant
	 *	@subpackage Model
	 * 
	 */

	namespace Ant {
		
		Class Resource {
			
			/*
			 *	Constructor initializes
			 *	the model in use, and 
			 *	prepares for CRUD task.
			 * 
			 *	@since 0.1.0
			 */
			
			public function __construct( $model, $data ){
				
				$this->model	= $model;
				$this->data		= $data;
				$this->getSchema();
			}
			
			/*
			 *	Gets the schema of the
			 *	specified model
			 * 
			 *	@since 0.1.0
			 */
			
			public function getSchema(){				
				
				// Get the XML schema //
				try { 
					$xml = simplexml_load_file( 'app/classes/models/schema/' 
						. $this->model->getName() . '.xml' );
				} catch( \Exception $e ){
					throw new \Exception('You must specify a schema for resource ' 
						. $this->model->getName() );
				}
				
				// Get all the fields, type casting, and validation/sanitization options //
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
					}
				}
				
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
										. $operation . ' task in ' . $this->model->getName(), 0 );
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
									. $operation . ' task in ' . $this->model->getName() 
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
										' . $operation . ' task in ' . $this->model, 0 );
								}
							}
						}
						
						// Check if minimum field values are met //
						if( ! $read ){
							throw new \Exception('Insufficent data for ' 
								. $operation . ' task in ' . $this->model 
									. '. Requires ' . implode(' OR ', $this->crud[ $operation ], 1 ) 
							);
						}
						
						break;
				}
				
				return true;
				
			}
			
			/*
			 *	Basic PHP native validation
			 * 
			 *	@since 0.1.0
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
			
			private static function sanitize( $data, $type ){
				
				switch( $type ){
					case 'html' :
						return strip_tags( $data );
						break;
				}
				
			}
			
			/*
			 *	Check if the task is 
			 *	valid and set it
			 * 
			 *	@since 0.1.0
			 */
			
			public function checkTask( $task ){
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
				
				// Check the data complies //
				$this->compareSchema( $task );
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
			
		}
	}
