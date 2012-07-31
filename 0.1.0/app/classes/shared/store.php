<?php

/*
 *	@description
 *	Store provides a means
 *	of storing data for 
 *	saving to the database.
 *	
 *	@notes
 *	Extends Database class
 */

namespace Ant {
	
	Class Store extends Database {
		
		var $collection,
			$idKey,
			$id,
			$dataContext;
		
		public function __construct( Collection $col, $idKey = null ){
			$this->idKey = $idKey;
			$this->collection = $col;
		}
		
		/*
		 *	Save the store to
		 *	the database, either
		 *	by creating it, or 
		 *	updating it (if exists). 
		 * 
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
		 *	@function
		 *	Best guess to make an 
		 *	id key and value
		 */
		public function makeIdKey(){
			$this->idKey = $this->dataContext . '_id';
		}
		
		/*
		 *	@function
		 *	Using best guess idKey
		 *	try to get the id from
		 *	the collection
		 */
		
		public function getId( $at ){
			if( $this->collection->length() == 1 ){
				$arr = $this->collection->first()->toArray();
				$this->id = $arr[ $this->idKey ];
			}
		}
		
	}
	
}