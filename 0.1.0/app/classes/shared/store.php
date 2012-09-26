<?php

	/*
	 *	Store provides a means
	 *	of storing data for 
	 *	saving to the database.
	 *	
	 *	@package Ant
	 *	@subpackage Store
	 *	@since 0.1.0
	 */

	namespace Ant {

		Class Store extends Database {

			var $collection,
				$resourceName;
			
			/*
			 *	Instantiate a new Store
			 * 
			 *	@since 0.1.0
			 */
			
			public function __construct( $data, $resourceName ){
				$this->resourceName = $resourceName;
				$this->collection = new Collection( $data, $resourceName );
			}
			
			/*
			 *	Get the data of the stored
			 *	item.
			 * 
			 *	@since 0.1.0
			 */
			
			public function getData(){
				return $this->collection->first()->toArray();
			}
			
			/*
			 *	Get the Id from
			 *	the collection using
			 *	the resource name.
			 * 
			 *	@since 0.1.0
			 */
			
			public function getId(){
				$data = $this->getData();
				return $data[ $this->resourceName . '_id' ];
			}
			
		}

	}