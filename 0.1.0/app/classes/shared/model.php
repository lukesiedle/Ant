<?php

	/*
	 *	Ant Model class. 
	 *	
	 *	@since 0.1.0
	 * 
	 */

	namespace Ant {
		
		Class Model {
			
			public function __construct( $modelName ){
				$this->modelName = $modelName;
			}
			
			public function getName(){
				return $this->modelName;
			}
			
			public function task( $resource ){
				return $this->{ $resource->getTask() }( $resource );
			}
			
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
			
			public function read( $resource ){			
				
				$query = \Ant\Controller :: query( $this->modelName . '.crud.read', array(
					'resource' => $resource
				));
				
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
			
			public function update( $resource ){
				
			}
			
			public function delete( $resource ){
				
			}
			
		}
		
	}