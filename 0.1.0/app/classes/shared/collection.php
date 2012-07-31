<?php
	
	/*
	 *	@description
	 *	For data manipulation
	 *	and exporting to a database
	 *	
	 *	@requires
	 *	Underscore.php
	 * 
	 *	@notes
	 *	The namespace of a collection
	 *	can be the table name of a mysql
	 *	table, as this is always
	 *	unique and best describes the data
	 *	held within it.
	 */
	
	namespace Ant {
		
		// Underscore alias //
		use \__ as __;
		
		Class Collection {
			
			private $records	= array(), 
					$index		= 0,
					$namespace;
			
			public function __construct( $arr = array(), $namespace = 'stdCollection' ){
				$this->namespace	= $namespace;
				if( ! empty($arr )){
					$this->add( $arr );
				}
				return $this;
			}
			
			// Note, this clone is recursive //
			function __clone(){
				
				$cloneRecords = array();
				$joins			= array();
				
				$this->each( function( $record, $i ) use ( & $cloneRecords , & $joins ){
					$cloneRecords[$i] = $record->toArrayShallow();
					foreach( $record->getJoins() as $join ){
						$joins[$i][] = clone $join;
					}
				});
				
				$this->clear();
				$this->createRecords( $cloneRecords );
				
				if( count($joins) > 0 ){
					$this->each( function( $record, $i ) use( $joins ){
						if( $joins[$i] ){
							foreach( $joins[$i] as $join ){
								$record->join( $join );
							}
						}
					});
				};
			}
			
			private function createRecords( $data ){
				foreach( $data as $each ){
					$this->records[ $this->index ] = new CollectionRecord( $this->index, $each, $this );
					$this->index++;
				}
			}
			
			public function length(){
				return $this->index;
			}

			// Return the first record //
			public function first(){
				return __ :: first( $this->records );
			}
			
			// Return the last record //
			public function last(){
				return __ :: last( $this->records );
			}

			// Return a specific record // 
			public function at( $index ){
				return $this->records[ $index ];
			}
			
			// Loop //
			public function each( $fn ){
				return __ :: each( $this->records, $fn );
			}
			
			public function find( $search ){
				$rec = array();
				$this->each( function( $record ) use( $search, & $rec ){
					$toArray	= $record->toArray();
					$found		= 0;
					foreach( $search as $k => $e ){
						if( $toArray[$k] == $e ){
							$found++;
						}
					}
					if( $found == count($search)){
						$rec[] = $toArray;
					}
				});
				
				return new Collection( $rec );
			}
			
			// Return all records data //
			public function toObject( $x = null ){
				$data = array();
				__ :: each( $this->records, function( $record, $i ) use( & $data ){
					$data[ $i ] = $record->toObject();
				});
				if( is_numeric($x) ){
					return $data[ $x ];
				}
				return $data;
			}
			
			public function toArray( $x = null ){
				$data = array();
				__ :: each( $this->records, function( $record, $i ) use ( & $data ){
					$data[ $i ] = $record->toArray();
				});
				if( is_numeric($x) ){
					return $data[ $x ];
				}
				return $data;
			}
			
			public function toArrayShallow( $x = null ){
				$data = array();
				__ :: each( $this->records, function( $record, $i ) use ( & $data ){
					$data[ $i ] = $record->toArray( true );
				});
				if( is_numeric($x) ){
					return $data[ $x ];
				}
				return $data;
			}
			
			public function getNamespace(){
				return $this->namespace;
			}
			
			// Join a collection to a record //
			public function join( Collection $col, $key, $index = null ){
				// Join to every record //
				$col->setMutualKey( $key );
				if( is_null($index)){
					$this->each( function( $record ) use( $col ){
						$record->join( clone $col );
					});
					return $this;
				}
				$this->records[ $index ] -> join( $col );
				return $this;
			}
			
			// The mutual key is the key
			// that all joins within a collection
			// of records have in common.
			public function setMutualKey( $key ){
				$this->mutualKey = $key;
				return $this;
			}
			
			public function getMutualKey(){
				return $this->mutualKey;
			}
			
			public function add( $data ){
				
				if( $data instanceof Collection ){
					$data = $data->toArray();
					return $this;
				}
				
				// Force collection structure //
				if( ! is_array(__::first($data))){
					$data = array( $data );
				}
				
				$this->createRecords( $data );
				return $this;
			}
			
			public function clear(){
				$this->records = array();
				$this->index = 0;
			}
			
			public function hasJoins(){
				$hasJoins = false;
				$this->each( function( $record ) use( & $hasJoins ) {
					if( $record->hasJoins() ){
						$hasJoins = true;
						return;
					}
				});
				return $hasJoins;
			}
			
			public function save( String $idKey ){
				$store = new Store( $this, $idKey );
				$store->save();
				return $store;
			}
			
			public function output(){
				Application :: out( $this->toArray() );
			}
			
		}
		
		Class CollectionRecord {
			
			private $data		= array(),
					$joins		= array(), 
					$hasJoins	= false,
					$index;
			
			public function __construct( $index, Array $data, Collection $col ){
				$this->add( $data );
				$this->index = $index;
				$this->collection = $col;
				return $this;
			}
			
			public function toArray( $shallow = false ){
				$nm = $this->collection->getNamespace();
				$array = $this->data;
				if( count($this->joins) > 0 && ! $shallow ){
					foreach( $this->joins as $join ){
						$array[ '\\' . $join->getNamespace()] = $join->toArray();
					}
				}
				return $array;
			}
			
			public function toArrayShallow(){
				return $this->toArray( true );
			}
			
			public function toObject(){
				$nm = $this->collection->getNamespace();
				$obj = ( object ) $this->data;
				if( count($this->joins) > 0 ){
					foreach( $this->joins as $join ){
						$obj->{ '\\' . $join->getNamespace() } = $join->toObject();
					}
				}
				
				return $obj;
			}
			
			public function getIndex(){
				return $this->index;
			}
			
			public function add( Array $data ){
				$this->data = array_merge( $this->data, $data );
				return $this;
			}
			
			public function join( Collection $col ){
				$this->joins[ $col->getNamespace() ] = $col;
				$this->hasJoins = true;
				return $this;
			}
			
			public function unjoin( $namespace ){
				unset( $this->joins[ $namespace ] );
				if( count($this->joins) == 0 ){
					$this->hasJoins = false;
				}
				return $this;
			}
			
			public function hasJoins(){
				return $this->hasJoins;
			}
			
			public function getCollection(){
				return $this->collection;
			}
			
			public function setJoins( $joins ){
				$this->joins = $joins;
			}
			
			public function getJoins(){
				return $this->joins;
			}
			
		}
		
		Class CollectionSet {
			
			private $collections = array();
			
			public function __construct( $array ){
				if( !is_array($array)){
					$array = func_get_args();
				}
				foreach( $array as $collection ){
					$this->collections[ $collection->getNameSpace() ] = $collection;
				}
			}
			
			public function getCollections(){
				return $this->collections;
			}
			
			public function toArray(){
				$array = array();
				foreach( $this->collections as $key => $col ){
					$array[ $key ] = $col->toArray();
				}
				return $array;
			}
			
		}

	}
	
?>