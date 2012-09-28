<?php
	
	/**
	 *	Query building class
	 * 
	 *	@package Ant
	 *	@subpackage Query
	 *	@since 0.1.0
	 */
	namespace Ant {
		
		use \Ant\Collection as Collection;
		use \__ as __;
		
		Class Query {
			
			var $select		= '',
				$joins		= '',
				$groupBy	= '',
				$orderBy	= '',
				$limit		= '',
				$query		= '',
				$type		= '',
				$tableName	= '',
				$where		= '',
				$binding	= array(),
				$queryData	= array(),
				$isPrepared = false;
			
			/**
			 *	Instantiate the query
			 * 
			 *	@param string $query A manual query
			 *	
			 *	@since 0.1.0
			 */
			public function __construct( $query = null ){
				if( $query ){
					$this->query		= $query;
					$this->isPrepared	= true;
				}
			}
			
			/**
			 *	Create or extend bind data
			 *	to be used in the PDO transaction,
			 *	for inserting values safely into
			 *	queries.
			 * 
			 *	@param array $arr Key value binding
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function bind( $arr ){
				$this->binding = array_merge( $this->binding, $arr );
				return $this;
			}
			
			/**
			 *	Prepare the query for passing	
			 *	to MySQL/PDO wrapper class.
			 *	
			 *	@since 0.1.0
			 *	@return string The query
			 */
			public function prepare(){
				
				if( $this->isPrepared ){
					return $this->query;
				}
				
				$this->query = 
				"SELECT " . $this->select
				. "\n" . "FROM " . $this->tableName
				. "\n" . $this->joins
				. "\n" . $this->where
				. "\n" . $this->groupBy
				. "\n" . $this->orderBy
				. "\n" . $this->limit;
				
				return $this->query;
						
			}
			
			/**
			 *	Add a select statement
			 * 
			 *	@param string $cols The select columns
			 *	@param string $tableName The table name
			 *	
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			
			public function select( $cols, $tableName = null ){
				
				if( $this->select != '' ){
					$cols .= ',';
				}
				
				$this->select .= $cols;
				
				if( $tableName ){
					$this->setTableName( $tableName );
				}
				
				return $this;
			}
			
			/**
			 *	Set the table name
			 * 
			 *	@param string $tableName The table name
			 * 
			 *	@since 0.1.0
			 *	@deprecated Not in use
			 *	@return object For chaining
			 */
			public function setTableName( $tableName ){
				$this->tableName = $tableName;
				return $this;
			}
			
			/**
			 *	Add a join statement
			 *	
			 *	@param string $join The join statement
			 *	@param string $type The join type, 'LEFT', 'RIGHT'
			 *	
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function join( $join, $type = 'JOIN' ){
				$this->joins .= "\n " . $type . " " . $join . " ";
				return $this;
			}
			
			/**
			 *	Add a where statement
			 *	
			 *	@param string $where The condition (exclude 
			 *	the WHERE statement). To avoid SQL injection
			 *	use dynamic search strings ( e.g. ":id" ) 
			 *	and bind them using the	second argument. 
			 *	
			 *	@param array $binding The items to bind, e.g.
			 *		array(
			 *			':id' => $_GET['id']
			 *		)
			 *	@param string $op The operator to use. Remember
			 *	to bracket conditions adequately to preserve
			 *	your logic.	
			 * 
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function where( $where, $binding = array(), $op = '&&' ){
				
				$this->bind( $binding );
				
				if( $this->where == '' ){
					$this->where = "\n WHERE 1=1 ";
				}
				
				$this->where .= "\n " . $op . " (" . $where . ")";
				return $this;
			}
			
			/**
			 *	Add a group by statement
			 *	
			 *	@param string $groupBy 
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function groupBy( $groupBy ){
				if( $this->groupBy == '' ){
					$this->groupBy = 'GROUP BY ';
				}
				$this->groupBy .= $groupBy;
				return $this;
			}
			
			/**
			 *	Add an order by statement
			 * 
			 *	@param string $orderBy
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function orderBy( $orderBy ){
				if( $this->orderBy == '' ){
					$this->orderBy = 'ORDER BY ';
				}
				$this->orderBy .= $orderBy;
				return $this;
			}
			
			/**
			 *	Add a limit statement
			 *	
			 *	@param string $limit
			 * 
			 *	@since 0.1.0
			 *	@return object For chaining
			 */
			public function limit( $limit ){
				$this->limit = 'LIMIT ' . $limit;
				return $this;
			}
			
			/**
			 *	Create the insert and return it
			 * 
			 *	@param array $rows They key value 
			 *	pairs representing the columns and 
			 *	values.
			 *	@param string $tableName 
			 * 
			 *	@since 0.1.0
			 *	@return Query A new query
			 */
			public static function setInsert( $rows, $tableName ){
				$query = 'INSERT INTO ' . $tableName;
				$structure = array_keys( __ :: first( $rows ));
				
				$query .= "\n" . '(' . implode( ',', $structure ) . ')';
				$query .= "\n" ."VALUES ";
				foreach( $rows as $i => $row ){
					$y=0;
					if( $i > 0 ){
						$query .= ',';
					}
					$query .= '(';
					foreach( $row as $col => $val ){
						if( $y > 0 ){
							$query .= ',';
						}
						$query .= "\n " . ':' . $col . $i;
						$binding[ ':' . $col . $i ] = $val;
						$y++;
					}
					$query .= "\n" . ')';
				}
				
				$query = new Query( $query );
				$query->setType( 'insert' );
				$query->bind( $binding );
				return $query;
			}
			
			/**
			 *	Set the update data
			 *
			 *	@param array $data The key value
			 *	pairs of data representing columns
			 *	and values.
			 *	@param array $conditions The key value
			 *	pairs of data representing columns
			 *	and values for conditions.	
			 *	@param string $tableName
			 * 
			 *	@since 0.1.0
			 *	@return Query A new query
			 */
			public static function setUpdate( $data, $conditions, $tableName ){
				
				$query		= 'UPDATE ' . $tableName . ' SET ';
				$i			= 0;
				$binding	= array();
				
				foreach( $data as $col => $val ){
					if( $i > 0 ){
						$query .= ',';
					}
					$query .= "\n " . $col .= ' = :' . $i;
					$binding[ ':' . $i ] = $val;
					$i++;
				}
				
				if( count($conditions) > 0 ){
					$query .= "\n" . "WHERE (";
					$y=0;
					foreach( $conditions as $col => $val ){
						if( $y > 0 ){
							$query .= "AND";
						}
						$query .= "\n " . $col . ' = :' . $i;
						$binding[ ':' . $i ] = $val;
						$y++;
						$i++;
					}
					$query .= "\n" . ")";
				}
				
				$query = new Query( $query );
				$query->setType('update');
				$query->bind( $binding );				
				return $query;
			}
			
			/**
			 *	Set the update data
			 *	
			 *	@param $conditions The key value
			 *	pairs representing columns and values
			 *	that will be used to delete rows.
			 *	@param string $tableName 
			 * 
			 *	@since 0.1.0
			 *	@return Query A new query
			 */
			public static function setDelete( Array $conditions, $tableName ){
				
				$query		= 'DELETE FROM ' . $tableName;
				$i			= 0;
				$binding	= array();
				
				if( count($conditions) > 0 ){
					$query .= "\n" . "WHERE (";
					$y=0;
					foreach( $conditions as $col => $val ){
						if( $y > 0 ){
							$query .= "AND";
						}
						$query .= "\n " . $col . ' = :' . $i;
						$binding[ ':' . $i ] = $val;
						$y++;
						$i++;
					}
					$query .= "\n" . ")";
				}
				
				$query = new Query( $query );
				$query->setType('delete');
				$query->bind( $binding );				
				return $query;
			}
			
			/**
			 *	Get the binding
			 *	
			 *	@since 0.1.0
			 *	@return array The binding
			 */
			
			public function getBinding(){
				return $this->binding;
			}
			
			/**
			 *	Set the type of statement
			 *	
			 *	@param $type The type of statement, 
			 *	INSERT, UPDATE
			 * 
			 *	@since 0.1.0
			 */
			public function setType( $type ){
				return $this->type = $type;
			}
			
			/**
			 *	Shortcut method to output
			 *	the query.
			 * 
			 *	@since 0.1.0
			 */
			public function output(){
				Application :: out( $this->query );
			}
			
		}
	}
	