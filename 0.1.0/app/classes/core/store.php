<?php

	/**
	 *	Store provides a means
	 *	of storing data for 
	 *	saving to the database,
	 *	usually coupled to Resource.
	 *	
	 *	@package Ant
	 *	@subpackage Store
	 *	@since 0.1.0
	 */
	namespace Ant {

		Class Store extends Database {

			var $collection,
				$resourceName;
			
			/**
			 *	Instantiate a new Store
			 * 
			 *	@param array $data The data to store
			 *	@param string $resourceName The name of the 
			 *	resource, e.g. 'user' 'user_group' 'article'
			 *	
			 *	@since 0.1.0
			 */
			public function __construct( $data, $resourceName ){
				$this->resourceName = $resourceName;
				$this->collection = new Collection( $data, $resourceName );
			}
			
			/**
			 *	Get the data of the stored
			 *	item.
			 * 
			 *	@since 0.1.0
			 *	@return array The data 
			 */
			public function getData(){
				if( $this->collection->length() == 1 ){
					return $this->collection
							->first()
							->toArray();
				}
				return $this->collection->toArray();
				
			}
			
			/**
			 *	Get the Id or list of Ids from
			 *	the collection using
			 *	the resource name.
			 *	
			 *	@since 0.1.0
			 *	@return mixed The Id or array of Ids
			 */
			public function getId(){
				$data = $this->getData();
				
				// We need the first item //
				if( $this->collection->length() > 1 ){
					$arr = array();
					$this->collection->each( function( $record, $i ) use( $arr ){
						$arr[ $i ] = $arr[ $this->resourceName . '_id' ];
					});
					return $arr;
				}
				return $data[ $this->resourceName . '_id' ];
			}
			
		}

	}