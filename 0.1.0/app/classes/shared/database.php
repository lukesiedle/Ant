<?php
	
	/*
	 *	Database is a helper
	 *	class for getting data
	 *	from a Collection into 
	 *	the database and for
	 *	getting data from a query
	 * 
	 *	@package Ant
	 *	@subpackage Database
	 *	@since 0.1.0
	 */

	namespace Ant {
		
		use \Library\MySQL as MySQL;
		use \__ as __;
		
		Class Database {
			
			public static $tablePrefix = '';
			
			/*
			 *	Instantiate the Database object
			 * 
			 *	@since 0.1.0
			 * 
			 */
			
			public function __construct( Query $query ){
				$this->query = $query;
			}
			
			/*
			 *	Execute the query and return
			 *	the result
			 * 
			 *	@since 0.1.0
			 *	@return bool OR Collection The result
			 */
			
			public function execute(){
				return self :: query( $this->query );
			}
			
			/*
			 *	Set the table prefix
			 * 
			 *	@since 0.1.0
			 */
			
			public static function setTablePrefix( $prefix ){
				self :: $tablePrefix = $prefix;
			}
			
			/*
			 *	Get the table prefix
			 * 
			 *	@since 0.1.0
			 *	@return string The prefix
			 */
			
			public static function getTablePrefix(){
				return self :: $tablePrefix;
			}
			
			/*
			 *	Insert records from a Collection,
			 *	with an optional callback that will
			 *	return the Id for every insert.
			 *	
			 *	@since 0.1.0			 
			 */
			
			public static function insert( Collection $col, $fnCallback = null ){
				
				$rows		= array();
				$i			= 0;
				$namespace	= $col->getNamespace();
				
				$col->each( function( $record ) use ( & $rows, & $i, & $joins ) {
					$rows[ $i ]	= $record->toArrayShallow();
					$i++;
				});
				
				// Make sure the insert data is OK //
				self :: CheckSchema( array_keys($rows[0]), $namespace );
				
				if( $fnCallback || $col->hasJoins() ){
					
					foreach( $rows as $y => $each ){
						
						if( $col->hasJoins() && !$col->getPrimaryKey() ){
							throw new Exception('A collection needs a primary key when inserting joins.');
						}
						
						$id = MySQL :: insert(
							Query :: setInsert( array($each), self :: $tablePrefix . $namespace )
						);
						
						if( $fnCallback ){
							$fnCallback( $id, $y );
						}
						
						$joins = $col->at( $y )->getJoins();
						
						// Add the new Id to the record if we know it's primary key //
						if( $col->getPrimaryKey() ){
							$col->at( $y )->add(array(
								$col->getPrimaryKey() => $id
							));
						}
						
						foreach( $joins as $nm => $join ){
							
							$key = $join->getPrimaryKey();
							
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
			
			/*
			 *	Update records (of a single row),
			 *	with optional conditions.
			 * 
			 *	@since 0.1.0			 
			 */
			
			public static function update( Collection $col, $conditions = array()){
				$data	= $col->toArrayShallow();
				$query	= Query :: setUpdate( 
					__ :: first( $data ), 
					$conditions, 
					self :: $tablePrefix . $col->getNamespace()
				); 
				MySQL :: doQuery( $query );
			}
			
			/*
			 *	Update records (of a multiple rows)
			 * 
			 *	@since 0.1.0			 
			 */
			
			public static function updateMulti( Collection $col, $idKey, $conditions = array() ){
				
				$col->each( function( $record ) use( $conditions, $idKey, $col ){
					
					$each = $record->toArray();
					
					if( !isset($each[ $idKey ])){
						throw new \Exception( 'IdKey not found for update.' );
						break;
					}
					
					$conditions[ $idKey ] = $each[ $idKey ];
					
					$query	= Query :: setUpdate( 
						$each, 
						$conditions, 
						Database :: $tablePrefix . $col->getNamespace()
					);
					
					MySQL :: doQuery( $query );
					
				});
			}
			
			/*
			 *	Delete records
			 * 
			 *	@since 0.1.0			 
			 */
			
			public static function delete( Collection $col, Array $conditions ){
				
				$query	= Query :: setDelete( 
					$conditions, 
					Database :: $tablePrefix . $col->getNamespace()
				);
				
				MySQL :: doQuery( $query );
			}
			
			/*
			 *	Select records, using query
			 * 
			 *	@since 0.1.0			 
			 *	@return bool false OR Collection
			 */
			
			public static function query( Query $query ){
				if( $result = MySQL :: doFetchQuery( $query )){
					return new Collection ( $result );
				}
				// Return an empty collection //
				return new Collection;
			}
			
			/*
			 *	Get the table schema to compare
			 *	collection data
			 * 
			 *	@since 0.1.0			 
			 *	@return array The schema
			 */
			
			public static function getSchema( $tableName ){			
				$query = new Query( "DESCRIBE " . self :: $tablePrefix . $tableName );
				$data = MySQL :: doFetchQuery($query);
				$columns = array();
				foreach( $data as $colData ){
					$columns[ $colData['Field'] ] = $colData['Type'];
				}
				return $columns;
			}
			
			/*
			 *	Compare the collection data 
			 *	to the table schema
			 * 
			 *	@since 0.1.0
			 */
			
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