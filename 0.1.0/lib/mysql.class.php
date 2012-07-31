<?php
	
	namespace Library {
		
		use \Ant\Query as Query;
		
		Class MySQL {

			public static $lastConnection;
			private $connection;

			public static function connect( $set ){

				$dsn =  'mysql:host=' . $set['host']	.
						';dbname='    . $set['db']		.
						';port='	  . $set['port']	. 
						';connect_timeout=' . $set['timeout'];

				self :: $lastConnection = new \PDO($dsn, $set['username'], $set['password']);

				return self :: $lastConnection;
			}
			
			public static function disconnect( $conn = null ){
				if( !$conn ){
					self :: $lastConnection = null;
					return;
				}
				$conn = null;
			}
			
			public static function errors(){
				return self :: $lastConnection->errorInfo();
			}

			public function __construct( $connection = null ){
				$this->connection = $connection;
				if( !$connection ){
					$this->connection = self :: $lastConnection;
				}
			}

			public function execQuery( $query ){

				$queryStr	= $query;
				$binding	= array();

				if( $query instanceof Query ){
					$binding	= $query->getBinding();
					$queryStr	= $query->prepare();
				}
				
				$result = new MySqlPDOStatement( $queryStr, $this->connection );

				$result->setBinding( $binding );

				$result->execute();

				return $result;
			}

			public static function doQuery( $query, $conn = null ){

				if( ! $conn ){
					$conn = self :: $lastConnection;
				}

				$mysql		= new MySQL( $conn );

				$result		= $mysql->execQuery( $query );

				return $result;

			}

			public static function doFetchQuery( $query, $conn = null ){
				
				if( ! $conn ){
					$conn = self :: $lastConnection;
				}
				
				$result = MySQL :: doQuery( $query, $conn );

				$result = $result->fetchAll();

				// Convert to collection if available //
				return $result;

			}

			public static function insert( $query ){
				$result = self :: doQuery( $query );
				return $result->lastInsertId();
			}

			public function lastInsertId( $result = null ){
				if( is_null($result)){
					$result = $this;
				}
				return $result->lastInsertId();
			}

			// Tables //
			public static function setTablePrefix( $prefix ){
				self :: $tablePrefix = $prefix . '_';
			}

			public static function tableName( $name ){
				return self :: $tablePrefix . $name;
			}

		}

		Class MySQLPDOStatement {

			private $statement, 
					$connection, 
					$query, 
					$binding;

			public function __construct( $query, $connection = null ){
				$this->connection = $connection;
				if( !$connection ){
					$this->connection = MySQL :: $lastConnection;
				}

				$this->query = $query;
				$this->binding = array();


				$this->makeStatement();
			}

			public function makeStatement(){
				return $this->statement	= $this->connection->prepare( $this->query );
			}

			function execute(){

				foreach( $this->binding as $i => $each ){
				
					if( is_numeric($each )){
						$this->statement->bindValue( $i, $each, \PDO::PARAM_INT );
						continue;
					}
					if( is_string($each )){
						$this->statement->bindValue( $i, $each, \PDO::PARAM_STR );
					}
				}

				$this->statement->execute();
			}

			public function fetchAll(){
				return $this->statement->fetchAll( \PDO::FETCH_ASSOC );
			}

			function setBinding( $binding ){
				$this->binding = $binding;
			}

			public function lastInsertId(){
				return $this->connection->lastInsertId();
			}
		}
	}
?>