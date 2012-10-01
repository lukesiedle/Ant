<?php
	
	/**
	 *	Collection is a useful 
	 *	means of storing data and 
	 *	is tightly coupled to Ant
	 *	subpackages.
	 * 
	 *	@note The namespace of a collection
	 *	is usually the table name of a mysql
	 *	table, as this is always
	 *	unique and best describes the data
	 *	held within it.
	 * 
	 *	@package Ant
	 *	@subpackage Collection
	 *	@type Shared
	 *	@require Underscore.php
	 *	@since 0.1.0
	 */
	namespace Ant {
		
		// Underscore alias //
		use \__ as __;
		
		Class Collection {
			
			private $records	= array(), 
					$index		= 0,
					$primaryKey,
					$namespace;
			
			/**
			 *	Instantiation of collection
			 *
			 *	@param array $arr The array data
			 *	@param string $namespace The data namespace
			 *		
			 *	@since 0.1.0
			 */
			public function __construct( $arr = array(), $namespace = 'stdCollection' ){
				$this->namespace	= $namespace;
				if( is_string($arr)){
					$this->namespace	= $arr;
					return;
				}
				if( ! empty($arr ) ){
					if( !is_array($arr)){
						$arr = (array) $arr;
					}
					$this->add( $arr );
				}
				return;
			}
			
			/**
			 *	Clone magic method for 
			 *	ensuring joins and 
			 *	child collections are 
			 *	also cloned.
			 *	
			 *	@since 0.1.0
			 */
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
			
			/**
			 *	Create records based on
			 *	data 
			 *	
			 *	@param array $data The array data
			 *			
			 *	@since 0.1.0
			 */
			private function createRecords( $data ){
				foreach( $data as $each ){
					$this->records[ $this->index ] = new CollectionRecord( $this->index, $each, $this );
					$this->index++;
				}
			}
			
			/**
			 *	Get the current number
			 *	of records in the collection
			 *	
			 *	@since 0.1.0
			 *	@return int The record total
			 */
			public function length(){
				return $this->index;
			}
			
			/**
			 *	Get the first record
			 *	
			 *	@since 0.1.0
			 *	@return CollectionRecord The first record
			 */
			public function first(){
				return __ :: first( $this->records );
			}
			
			/**
			 *	Get the last record
			 *	
			 *	@since 0.1.0
			 *	@return CollectionRecord The last record
			 */
			public function last(){
				return __ :: last( $this->records );
			}

			/**
			 *	Get a specific record
			 *	based on index
			 *
			 *	@param int $index The index 
			 *		
			 *	@since 0.1.0
			 *	@return CollectionRecord
			 */
			public function at( $index ){
				return $this->records[ $index ];
			}
			
			/**
			 *	Loop through the records
			 *	executing a Closure
			 *	
			 *	@param closure $fn The function
			 * 
			 *	@since 0.1.0
			 */
			public function each( \Closure $fn ){
				return __ :: each( $this->records, $fn );
			}
			
			/**
			 *	Search within the collection
			 *	for a value
			 *	
			 *	@param array $search The search array
			 *	
			 *	@since 0.1.0
			 *	@return Collection A Collection 
			 *	containing results
			 */
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
			
			/**
			 *	Convert the collection to 
			 *	an object
			 *	
			 *	@param int $x The index
			 * 
			 *	@since 0.1.0
			 *	@return stdClass
			 */
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
			
			/**
			 *	Convert the collection to 
			 *	an array
			 *	
			 *	@param int $x The index
			 * 
			 *	@since 0.1.0
			 *	@return array
			 */
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
			
			/**
			 *	Convert the collection to 
			 *	a shallow array
			 *	
			 *	@param int $x The index
			 * 
			 *	@since 0.1.0
			 *	@return array The collection data
			 */
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
			
			/**
			 *	Get the namespace of 
			 *	the collection
			 *	
			 *	@since 0.1.0
			 *	@return string The namespace
			 */
			public function getNamespace(){
				return $this->namespace;
			}
			
			/**
			 *	Join another collection
			 *	to a record. If the index
			 *	is not specified, the
			 *	collection is joined to 
			 *	every single record.
			 *	
			 *	@param Collection $col The collection
			 *	@param int $index The record index to join to
			 *	otherwise the collection is joined to each record
			 *	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function join( Collection $col, $index = null ){
				
				// Join to every record //
				if( is_null($index) ){
					$this->each( function( $record ) use( $col ){
						$record->join( clone $col );
					});
					return $this;
				}
				$this->records[ $index ] -> join( $col );
				return $this;
			}
			
			/**
			 *	Remove joins by current primary key. 
			 *	If the index is not specified, all joins
			 *  are removed.
			 *	
			 *	@param string $namespace The namespace
			 *	@param int $index A specific index to unjoin
			 *	the collection from
			 *	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function unjoin( $namespace , $index = null ){
				
				// Remove joins from every record //
				if( is_null($index) ){
					$this->each( function( $record ) use ( $namespace ) {
						$record -> unjoin( $namespace );
					});
					return $this;
				}
				
				$this->records[ $index ] -> unjoin( $namespace );
				
				return $this;
			}
			
			/**
			 *	Set the primary key. The primary
			 *	key is the key that all joins
			 *	within a collection have in
			 *	common. Example 'user_id'
			 *	
			 *	@param string $key The primary key, 'user_id', 'article_id'
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function setPrimaryKey( $key ){
				$this->primaryKey = $key;
				return $this;
			}
			
			/**
			 *	Get the primary key. 
			 *	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function getPrimaryKey(){
				return $this->primaryKey;
			}
			
			/**
			 *	Extend the collection's
			 *	records
			 *	
			 *	@param array $data The data to add
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
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
			
			/**
			 *	Clear the records and reset
			 *	the index
			 *	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function clear(){
				$this->records = array();
				$this->index = 0;
				return $this;
			}
			
			/**
			 *	Check if the collection has joins
			 *	
			 *	@since 0.1.0
			 *	@return bool True if the collection
			 *	has joins
			 */
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
			
			/**
			 *	Shortcut method to output
			 *	a collection's data to the screen
			 *	
			 *	@since 0.1.0
			 */
			public function output(){
				Application :: out( $this->toArray() );
			}
			
			/**
			 *	Create a placeholder
			 *	collection that will
			 *	iterate once, useful
			 *	for conditioning in
			 *	templates.
			 *	
			 *	@param string $namespace The namespace	
			 * 
			 *	@since 0.1.0
			 */
			public static function create( $namespace ){
				return new Collection(array( 1 ), $namespace );
			}
			
			/**
			 *	Shallow merge of collection data. Collections
			 *	must be of equal length.
			 *	
			 *	@param Collection $col1 The Collection
			 *  @param Collection $col2 The Collection
			 *	
			 *	@since 0.1.0
			 *	@return collection The new collection
			 */
			public static function merge( $col1, $col2 ){
				
				$newData = array();
				
				$nm = $col1->getNamespace();
				$col1 = $col1->toArrayShallow();
				$col2 = $col2->toArrayShallow();
				
				// Merge the collection data //
				foreach( $col1 as $i => $data ){
					$newData[ $i ] = array_merge( $data, $col2[$i] );
				}
				
				return new Collection( $newData, $nm );
			}
			
		}
		
		/**
		 *	The collection record
		 *	is a single array of data
		 *	that represents a unit within
		 *	a collection. Example:
		 *	a single user's data.
		 *	
		 *	A record can have collections of its
		 *	own, in the form of joins. Example:
		 *	a single user's collection of library
		 *	books.
		 * 
		 *	@package Ant
		 *	@subpackage Collection
		 *	@subpackage CollectionRecord
		 *	@since 0.1.0
		 */
		Class CollectionRecord {
			
			private $data		= array(),
					$joins		= array(), 
					$hasJoins	= false,
					$index;
			
			/**
			 *	Instantiate the record, using
			 *	the index, data and collection.
			 *
			 *	@param int $index The index of the record
			 *	@param array $data The array data 
			 *	@param Collection $col The parent collection
			 * 	
			 *	@since 0.1.0
			 */
			public function __construct( $index, Array $data, Collection $col ){
				$this->add( $data );
				$this->index = $index;
				$this->collection = $col;
				return $this;
			}
			
			/**
			 *	Convert the record to 
			 *	an array, including joins
			 *	unless 'shallow' is specified.
			 *	
			 *	@param string $depth Choose 'deep' to include joins
			 * 
			 *	@since 0.1.0
			 *	@return array The record data
			 */
			public function toArray( $depth = 'deep' ){
				$nm = $this->collection->getNamespace();
				$array = $this->data;
				if( count($this->joins) > 0 && $depth != 'shallow' ){
					foreach( $this->joins as $join ){
						$array[ '\\' . $join->getNamespace()] = $join->toArray();
					}
				}
				return $array;
			}
			
			/**
			 *	Convert the record to 
			 *	a shallow array. This will
			 *	exclude the joins.
			 *	
			 *	@since 0.1.0
			 *	@return array The record data
			 */
			public function toArrayShallow(){
				return $this->toArray( 'shallow' );
			}
			
			/**
			 *	Convert the record to 
			 *	an object, including joins
			 *	unless 'shallow' is specified.
			 *	
			 *	@param string $depth Choose 'deep' to include joins
			 * 
			 *	@since 0.1.0
			 *	@return array The record data
			 */
			public function toObject( $depth = 'deep' ){
				$nm = $this->collection->getNamespace();
				$obj = ( object ) $this->data;
				if( count($this->joins) > 0 && $depth == 'shallow' ){
					foreach( $this->joins as $join ){
						$obj->{ '\\' . $join->getNamespace() } = $join->toObject();
					}
				}
				return $obj;
			}
			
			/**
			 *	Return the current index
			 *	
			 *	@since 0.1.0
			 *	@return int The index
			 */
			public function getIndex(){
				return $this->index;
			}
			
			/**
			 *	Extend the data array
			 *	
			 *	@param array $data The data to add
			 * 
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function add( Array $data ){
				
				// Can't use array_merge due to renumbering //
				foreach( $data as $key => $each ){
					$this->data[ $key ] = $each;
				}
				
				return $this;
			}
			
			/**
			 *	Join a collection to the record
			 *
			 *	@param Collection $col The collection to join
			 * 	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function join( Collection $col ){
				$this->joins[ $col->getNamespace() ] = $col;
				$this->hasJoins = true;
				return $this;
			}
			
			/**
			 *	Remove a joined collection from the record
			 *
			 *	@param string $namespace The collection namespace	
			 * 	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function unjoin( $namespace ){
				unset( $this->joins[ $namespace ] );
				if( count($this->joins) == 0 ){
					$this->hasJoins = false;
				}
				return $this;
			}
			
			/**
			 *	Check if the record has joins
			 * 
			 *	@since 0.1.0
			 *	@return bool True if record has joins
			 */
			public function hasJoins(){
				return $this->hasJoins;
			}
			
			/**
			 *	Return the record's collection
			 *	
			 *	@since 0.1.0
			 *	@return Collection
			 */
			public function getCollection(){
				return $this->collection;
			}
			
			/**
			 *	Set the record's joins
			 *
			 *	@params array $joins The joins
			 * 	
			 *	@since 0.1.0
			 *	@return object The object for chaining
			 */
			public function setJoins( $joins ){
				$this->joins = $joins;
				return $this;
			}
			
			/**
			 *	Get the record's joins
			 *	
			 *	@since 0.1.0
			 *	@return array The joins
			 */
			public function getJoins(){
				return $this->joins;
			}
			
		}
		
		/**
		 *	CollectionSet hosts a number
		 *	of collections, tightly coupled
		 *	to templating in Ant.
		 * 
		 *	@package Ant
		 *	@subpackage 
		 *	@type Shared
		 *	@since 0.1.0
		 */
		Class CollectionSet {
			
			private $collections = array();
			
			/**
			 *	Instantiation of the set, uses
			 *	passed in collections.
			 *		
			 *	@since 0.1.0
			 */
			public function __construct( $array ){
				if( !is_array($array)){
					$array = func_get_args();
				}
				foreach( $array as $collection ){
					$this->collections[ $collection->getNameSpace() ] = $collection;
				}
			}
			
			/**
			 *	Return the collections
			 *		
			 *	@since 0.1.0
			 *	@return array The collections
			 */
			public function getCollections(){
				return $this->collections;
			}
			
			/**
			 *	Convert the collection set
			 *	to an array
			 *		
			 *	@since 0.1.0
			 *	@return array The collective data
			 */
			public function toArray(){
				$array = array();
				foreach( $this->collections as $key => $col ){
					$array[ $key ] = $col->toArray();
				}
				return $array;
			}	
		}
	}