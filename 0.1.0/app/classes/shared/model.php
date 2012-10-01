<?php

	/*
	 *	Ant Model class. 
	 *	
	 *	@since 0.1.0
	 * 
	 */

	namespace Ant {
		
		Class Model {
			
			/**
			 *	Make the model
			 *	Uses the specific model
			 *	if it exists, or the generic
			 *	
			 *	@param string $modelName The model name
			 * 
			 *	@since 0.1.0
			 *	@return object The model
			 */
			public static function make( $modelName ){
				$modelPath = 'app/classes/models/' . $modelName . '.php';
				if( file_exists( $modelPath )){
					require_once( $modelPath );
					$model = '\Ant\Model\\' . $modelName;
					return new $model( $modelName );
				}				
				return new Model( $modelName );
				
			}
			
			/**
			 *	Constructor
			 *	Store the model name
			 *	
			 *	@param string $modelName The model name
			 * 
			 *	@since 0.1.0
			 */
			public function __construct( $modelName ){
				$this->modelName = strtolower( $modelName );
			}
			
			/**
			 *	Get the model name
			 *	
			 *	@since 0.1.0
			 *	@return string The model name
			 */
			public function getName(){
				return $this->modelName;
			}
			
			/**
			 *	Execute the specified
			 *	task attached to a 
			 *	resource
			 * 
			 *	@param Resource $resource The resource
			 *	containing data and instructions
			 * 
			 *	@since 0.1.0
			 *	@return mixed The task result
			 */
			public function task( $resource ){
				return $this->{ $resource->getTask() }( $resource );
			}
			
			/**
			 *	Set the task, then execute
			 *	with resource.
			 * 
			 *	@param string $task The resource task
			 *  @param resource $resource The resource object
			 *	
			 *	@since 0.1.0
			 *	@return mixed The task result
			 */
			public function doTask( $task, $resource ){
				$resource->setTask( $task );
				return $this->task( $resource );
			}
			
			/**
			 *	The default create task
			 * 
			 *	@param Resource $resource
			 * 
			 *	@since 0.1.0
			 *	@return Collection The data created
			 */
			public function create( $resource ){
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$resource->getData(), 
					$this->getName()
				);
				
				// Pass to Database for insertion, set the Id //
				Database :: insert( $collection, function( $id ) use( $collection, $resource ){
					$collection->first()->add(array(
						$resource->getPrimaryKey() => (int) $id
					));
				});
				
				$resource->setData( $collection->first()->toArray() );
				
				return $resource;
			}
			
			/**
			 *	The default read task. Uses the 
			 *	read fields specified in schema
			 *	for the query, and only reads
			 *	acceptable fields.
			 *	
			 *	@param Resource $resource
			 * 
			 *	@since 0.1.0
			 *	@return Collection The data read
			 */
			public function read( $resource ){			
				
				$query = new \Ant\Query;
				
				$query->select( 
					implode(',', $resource->getReadableFields()),
					\Ant\Database :: getTablePrefix() . $this->getName()
				);	
				
				// Try getting the resource using any acceptable read fields //
				$data = $resource->getData();
				$wh = '';
				$i=0;
				
				foreach( $resource->crudFields('read') as $field ){
					
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
			public function update( $resource ){
				
				echo $this->getName();
				echo $id;
				echo $idKey;
				
				$data	= $resource->getData();
				$id		= $resource->getId();
				$idKey	= $resource->getIdKey();
				
				
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$data,
					$this->getName()
				);
				
				// Update the database //
				\Ant\Database :: update( $collection, array(
					$idKey => $id
				));
				
				$resource->setData( $collection->first()->toArray() );
				
				return $resource;
			}
			
			/**
			 *	The default delete task
			 * 
			 *	@param Resource $resource
			 *	
			 *	@since 0.1.0
			 *	@return bool The success
			 */
			public function delete( $resource ){
				return $resource;
			}
			
		}
		
	}