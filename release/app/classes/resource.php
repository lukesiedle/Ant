<?php
	
	/**
	 *	The Resource is the backbone
	 *	of database transactions. 
	 *	It takes care of RESTFul / CRUD
	 *	structure, and data
	 *	validation, sanitization, to
	 *	allow for greater consistency
	 *	and to reduce coding overheads.
	 *	
	 *	@since 0.1.0
	 *	@package Core
	 *	@subpackage Resource
	 */
	namespace Core {
		
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
			public function __construct( $resource, Array $data = array()){

				// Interpret resource //
				$this->parseResource( $resource, $data );
				
				// Initialize the data handler of this resource //
				$handler = '\Data\\' . $this->resource;
				$this->handler = new $handler( $this->data );
			}
			
			/**
			 *	Parses the resource, performs
			 *	any required string replacements
			 * 
			 *	@since 0.2.0
			 */
			public function parseResource( $resource, $data ){				
				
				foreach( $data as $key => $val ){
					$search[ ] = ':' . $key;
				}
				
				$rs = explode('/', str_replace( 
					$search, $data, $resource 
				));
				
				$current;
				$main = $rs[0];
				
				$recurse = function( $i ) use ( & $data, & $current, & $main ){
					if( ! is_numeric($i) ){
						$current = '\Data\\' . $i;
						$main = $i;
					} else {
						$data[ $current :: PRIMARY_KEY ] = $i;
					}
				};
				
				foreach( $rs as $each ){
					$recurse( $each );
				}
				
				// Set the resource actually being used //
				$this->resource = $main;
				
				// Extend the data //
				$this->data = $data;
				
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
						$this->handler->setTask( $task );
						
						// Check if the handler discovered errors //
						if( $errors = $this->getErrors() ){
							// Create an error but keep it silent //
							new \Core\Error( '422', 'resource_data_handler_error', 'silent' );
						}
						
						break;
						
						default : 
							throw new \Exception('Invalid task "' . $task . '"' );
				}
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
				return $this->{ $task }( );
			}
			
			/**
			 *	Get the handler instance
			 * 
			 *	@since 0.2.0
			 *	@return Data The handler
			 */
			public function getHandler(){
				return $this->handler;
			}
			
			/**
			 *	The create task
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function create(){
				
				$this->setTask( 'create' );
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$this->handler->getPreparedData(),
					$this->handler->getName()
				);
				
				// Get the handler //
				$handler = $this->getHandler();
				
				// Pass to Database for insertion, set the Id //
				Database :: insert( $collection, function( $id ) use( $collection, $handler ){
					$collection->first()->add(array(
						$handler->getPrimaryKey() => (int) $id
					));
				});
				
				// Store the resource as a collection in memory //
				// self :: store( $collection, $this->getName() );
				
				// Reset the data //
				$handler->setData( $collection->first()->toArray() );
				
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
				
				$this->setTask( 'read' );
				
				if( $storedData = self :: getStoredData( $this->handler->getName()) ){
					return $storedData;
				}
				
				$query = new Query;
				
				$query->select( 
					'this.' . implode(',this.', $this->handler->getReadableFields()),
					Database :: getTablePrefix() . $this->handler->getName() . ' this'
				);
				
				
				// Try getting the resource using any acceptable read fields //
				$data = $this->handler->getPreparedData();
				$wh = '';
				$i=0;
				
				foreach( $this->handler->fields('read') as $field ){
					
					// Skip this field if not set //
					if( is_null($data[$field] )){
						continue;
					}
					
					// Append to the where //
					$bind[ $field ] = $data[ $field ];
					if( $i > 0 ){ $wh .= ' && '; }
					$wh .= '(' . $field . ' = :' . $field . ')';
					$i++;
				}
				
				$query->where( $wh, $bind );
				
				// Before read hook //
				if( method_exists( $this->handler, '__beforeRead') ){
					$query = $this->handler->__beforeRead( $query );
				}
				
				$collection = Database :: query( $query );
				
				// No results from query //
				if( $collection->length() == 0 ){
					return false;
				}
				
				// Store the resource as a collection in memory //
				self :: store( $collection, $this->handler->getName() );
				
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
				
				$this->setTask( 'update' );
				
				$handler = $this->getHandler();
				
				$data	= $handler->getPreparedData();
				$id		= $handler->getId();
				$idKey	= $handler->getIdKey();
				
				if( !$id || !$idKey ){
					throw new \Exception( 'Id or Id Key not set.', 422 );
				}
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$data,
					$this->handler->getName()
				);
				
				// Update the database //
				Database :: update( $collection, array(
					$idKey => $id
				));
				
				// Store the resource as a collection in memory //
				self :: store( $collection, $this->handler->getName() );
				
				// Reset the data //
				$handler->setData( $collection->first()->toArray() );
				
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
				
				$this->setTask( 'delete' );
				
				$handler = $this->getHandler();
				
				$data	= $handler->getPreparedData();
				$id		= $handler->getId();
				$idKey	= $handler->getIdKey();
				
				if( !$id || !$idKey ){
					throw new \Exception( 'Id or Id Key not set.', 422 );
				}
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$data,
					$this->handler->getName()
				);
				
				Database :: delete( $collection, array(
					$idKey => $id
				));
				
				return $this;
			}
			
			public function getErrors(){
				if( count($errors = $this->handler->getErrors()) > 0 ){
					return $errors;
				}	
				return false;
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
