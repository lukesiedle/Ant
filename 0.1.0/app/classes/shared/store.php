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
				$idKey,
				$id,
				$dataContext;
			
			/*
			 *	Instantiate a new Store
			 * 
			 *	@since 0.1.0
			 */
			
			public function __construct( Collection $col, $idKey = null ){
				$this->idKey = $idKey;
				$this->collection = $col;
			}

			/*
			 *	Save the store to the 
			 *	database, by either creating it,
			 *	or updating it.
			 * 
			 *	@since 0.1.0
			 *	@return object For chaing
			 */

			public function save(){

				// Updates if an Id is found //
				if( $this->collection->length() == 1 ){
					if( is_numeric($this->getId()) ){
						self :: update( $this->collection, array(
							$this->idKey => $this->id
						));
					}
					return $this;
				} else {
					if( is_numeric($this->getId(1) )){
						self :: updateMulti( $this->collection, $this->idKey );
						return $this;
					}
				}

				// Or it's an insert if no id is found //

				$idKey = $this->idKey;

				// For each insert, modify the id value //
				self :: insert( $this->collection, function( $id, $i ) use ( $idKey ) {
					$this->collection->at( $i )->add(array(
						$idKey => $id
					));
				});

				return $this;
			}

			/*
			 *	Best guess method to make an Id
			 * 
			 *	@since 0.1.0
			 */
			
			public function makeIdKey(){
				$this->idKey = $this->dataContext . '_id';
			}

			/*
			 *	Best guess method to get the Id,
			 *	example 'user_id', from the collection
			 * 
			 *	@since 0.1.0
			 */

			public function getId( $at ){
				if( $this->collection->length() == 1 ){
					$arr = $this->collection->first()->toArray();
					$this->id = $arr[ $this->idKey ];
				}
			}

		}

	}