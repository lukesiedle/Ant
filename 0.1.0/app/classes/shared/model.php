<?php

	/*
	 *	Ant Model class. 
	 *	
	 *	@since 0.1.0
	 * 
	 */

	namespace Ant {
		
		Class Model {
			
			/*
			 *	Make the model
			 *	Uses the specific model
			 *	if it exists, or the generic
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
			
			/*
			 *	Constructor
			 *	Store the model name
			 * 
			 *	@since 0.1.0
			 * 
			 */
			
			public function __construct( $modelName ){
				$this->modelName = strtolower( $modelName );
			}
			
			/*
			 *	Get the model name
			 * 
			 *	@since 0.1.0
			 *	@return string The model name
			 */
			
			public function getName(){
				return $this->modelName;
			}
			
			/*
			 *	Execute the specified
			 *	task attached to a 
			 *	resource
			 * 
			 *	@since 0.1.0
			 *	@return mixed The task result
			 */
			
			public function task( $resource ){
				return $this->{ $resource->getTask() }( $resource );
			}
			
			/*
			 *	Check the task, then execute
			 *	with resource.
			 * 
			 *	@since 0.1.0
			 *	@return mixed The task result
			 */
			
			public function doTask( $task, $resource ){
				$resource->checkTask( $task );
				return $this->task( $resource );
			}
			
			/*
			 *	The default create task
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
				
				return $collection;
			}
			
			/*
			 *	The default read task
			 * 
			 *	@since 0.1.0
			 *	@return Collection The data read
			 */
			
			public function read( $resource ){			
				
				$query = \Ant\Controller :: query( $this->modelName . '.crud.read', array(
					'resource' => $resource
				));
				
				$collection = \Ant\Database :: query( $query );
				
				// No results from query //
				if( $collection->length() == 0 ){
					throw new \Exception( 'Resource does not exist', 404 );
				}
				
				// Remove non-readable fields //
				$buffer = array();
				$collection->each( function( $record ) use( & $buffer, $resource ) {
					$each = $record->toArray();
					$fields = $resource->getReadableFields();
					foreach( $each as $key => $value ){
						if( in_array($key, $fields )){
							$buffer[ $key ] = $value;
						}
					}
				});
				
				$collection = new \Ant\Collection( $buffer, $collection->getNamespace() );
				
				if( $collection->length() == 1 ){
					return $collection->first()->toArray();
				}
				
				return $collection->toArray();
			}
			
			/*
			 *	The default update task
			 * 
			 *	@since 0.1.0
			 *	@return Collection The data updated/read
			 */
			
			public function update( $resource ){
				
				$data	= $resource->getData();
				$id		= $resource->getId();
				
				// Create a collection from the resource //
				$collection = new Collection( 
					$data,
					$this->getName()
				);
				
				// Update the database //
				\Ant\Database :: update( $collection, array(
					$resource->getPrimaryKey() => $id
				));
				
				// Return the resource //
				$rs = new \Ant\Resource( $resource->getResource(), array(
					'id' => $id
				));
				
				// Read the resource //
				return $rs->read();
			}
			
			/*
			 *	The default delete task
			 * 
			 *	@since 0.1.0
			 *	@return bool The success
			 */
			
			public function delete( $resource ){
				
			}
			
		}
		
	}