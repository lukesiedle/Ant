<?php
	
	/*
	 *	@description
	 *	Database is a helper
	 *	class for getting data
	 *	from a Collection into 
	 *	the database and for
	 *	getting data from a query
	 */

	namespace Ant {
		
		use \Library\MySQL as MySQL;
		use \__ as __;
		
		Class Database {
			
			public static $tablePrefix = '';
			
			public function __construct( Query $query ){
				$this->query = $query;
			}
			
			// Return query for further modification //
			public function edit( \Closure $fn ){
				$fn( $this->query );
				return $this;
			}
			
			// Execute the query to return a result //
			public function execute(){
				return self :: query( $this->query );
			}
			
			public static function setTablePrefix( $prefix ){
				self :: $tablePrefix = $prefix;
			}
			
			public static function getTablePrefix(){
				return self :: $tablePrefix;
			}
			
			// Insert new records //
			public static function insert( Collection $col, $fnCallback = null ){
				
				$rows		= array();
				$i			= 0;
				$namespace	= $col->getNamespace();
				
				$col->each( function( $record ) use ( & $rows, & $i, & $joins ) {
					$rows[ $i ]	= $record->toArrayShallow();
					$i++;
				});
				
				self :: CheckSchema( array_keys($rows[0]), $namespace );
				
				if( $fnCallback || $col->hasJoins() ){
					
					foreach( $rows as $y => $each ){
						
						$id = MySQL :: insert(
							Query :: setInsert( array($each), self :: $tablePrefix . $namespace )
						);
						
						if( $fnCallback ){
							$fnCallback( $id, $y );
						}
						
						$joins = $col->at( $y )->getJoins();
						
						foreach( $joins as $nm => $join ){
							
							$key = $join->getMutualKey();
							
							$join->each( function( $record ) use( $key, $id ){
								$record->add( array(
									$key => $id
								));
							});
						}
						
					}
				} else {
					// Batch perform insert //
					MySQL :: doQuery(
						Query :: setInsert( $rows, self :: $tablePrefix . $namespace )
					);
				}
				
				
				// Add joins where applicable //
				if( ! $col->hasJoins() ){
					return;
				} else {
					$tablePrefix = self :: $tablePrefix;
					$col->each( function( $record ) use ( $tablePrefix ) {
						// Joins are (usually) relational and their id should be forgettable //
						foreach( $record->getJoins() as $nm => $join ){
							MySQL :: doQuery( 
								Query :: setInsert( $join->toArray(), $tablePrefix . $nm )
							);
						}
						$i++;
					});
				}
			}
			
			// Update data (of a single row) //
			public static function update( Collection $col, $conditions = array()){
				$data	= $col->toArrayShallow();
				$query	= Query :: setUpdate( 
					__ :: first( $data ), 
					$conditions, 
					self :: $tablePrefix . $col->getNamespace()
				); 
				MySQL :: doQuery( $query );
			}
			
			public static function updateMulti( Collection $col, $idKey, $conditions = array() ){
				
				$data	= $col->toArray();
				foreach( $data as $each ){
					if( !isset($each[ $idKey ])){
						throw 'IdKey not found for update.';
						break;
					}
					$conditions[ $idKey ] = $each[ $idKey ];
					$query	= Query :: setUpdate( 
						$each, 
						$conditions, 
						self :: $tablePrefix . $col->getNamespace()
					);
					MySQL :: doQuery( $query );
				}
			}
			
			// Select data //
			public static function query( Query $query ){
				return new Collection (MySQL :: doFetchQuery( $query ));
			}
			
			// Get the table schema to compare to collection data 
			public static function getSchema( $tableName ){			
				$query = new Query( "DESCRIBE " . self :: $tablePrefix . $tableName );
				$data = MySQL :: doFetchQuery($query);
				$columns = array();
				foreach( $data as $colData ){
					$columns[ $colData['Field'] ] = $colData['Type'];
				}
				return $columns;
			}
			
			public static function checkSchema( $keys, $tableName ){
				
				// Only check schema locally //
				if( ! Application :: $app->local ){
					return;
				}
				
				$schema = self :: getSchema($tableName);
				foreach( $keys as $key ){
					if( !isset($schema[$key])){	
						throw new \Exception("Schema of queried table does not match collection.");
					}
				}
				
			}
			
		}
		 
	}

?>