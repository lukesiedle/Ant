<?php

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
			
			public function __construct( $query = null ){
				if( $query ){
					$this->query		= $query;
					$this->isPrepared	= true;
				}
			}
			
			public function bind( $arr ){
				$this->binding = array_merge( $this->binding, $arr );
				return $this;
			}
			
			// Build the query //
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
			
			// Query options //
			public function select( $cols, $tableName = null ){
				
				if( $this->select != '' ){
					$cols .= ',';
				}
				
				$this->select .= $cols;
				
				if( $tableName ){
					$this->setTableName( $tableName );
				}
				
				// For useful chaining //
				return $this;
			}
			
			public function setTableName( $tableName ){
				$this->tableName = $tableName;
				return $this;
			}
			
			public function join( $join, $type = 'JOIN' ){
				$this->joins .= "\n " . $type . " " . $join . " ";
				return $this;
			}
			
			public function where( $where, $binding = array(), $op = '&&' ){
				
				$this->bind( $binding );
				
				if( $this->where == '' ){
					$this->where = "\n WHERE 1=1 ";
				}
				
				$this->where .= "\n " . $op . " (" . $where . ")";
				return $this;
			}
			
			public function groupBy( $groupBy ){
				if( $this->groupBy == '' ){
					$this->groupBy = 'GROUP BY ';
				}
				$this->groupBy .= $groupBy;
				return $this;
			}
			
			public function orderBy( $orderBy ){
				if( $this->orderBy == '' ){
					$this->orderBy = 'ORDER BY ';
				}
				$this->orderBy .= $orderBy;
				return $this;
			}
			
			public function limit( $limit ){
				$this->limit = 'LIMIT ' . $limit;
				return $this;
			}
			
			// Inserts and updates //
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
			
			public function getBinding(){
				return $this->binding;
			}
			
			public function setType( $type ){
				return $this->type = $type;
			}
			
			public function output(){
				Application :: out( $this->query );
			}
		
		}
	}

?>