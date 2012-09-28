<?php
	
	/**
	 *	MySQL wrapper class 
	 *	using the PDO Library
	 *	
	 *	@package Ant
	 *	@subpackage Library
	 *	@since 0.1.0
	 *	
	 */
	namespace Library {
		
		use \Ant\Query as Query;
		
		Class MySQL {

			public static $lastConnection;
			public static $tablePrefix;
			private $connection;
			
			/**
			 *	Connect to the given 
			 *	database based on settings
			 *	
			 *	@param array $set The config to set
			 * 
			 *	@since 0.1.0
			 *	@return PDO The PDO object (connection)
			 */
			public static function connect( $set ){
				
				if ( ! defined('PDO::ATTR_DRIVER_NAME') ) {
					throw new \Exception( 'You must enable the PDO driver in PHP.ini' );
				}
				
				$dsn =  'mysql:host=' . $set['host']	.
						';dbname='    . $set['db']		.
						';port='	  . $set['port']	. 
						';connect_timeout=' . $set['timeout'];
				
				try {
					self :: $lastConnection = new \PDO($dsn, $set['username'], $set['password']);
				} catch( \PDOException $e ){
					throw new \Exception( $e->getMessage() );
				}
				
				return self :: $lastConnection;
			}
			
			/**
			 *	Get the last known connection
			 * 
			 *	@since 0.1.0
			 *	@return resource The connection
			 */
			public static function getLastConnection(){
				return self :: $lastConnection;
			}
			
			/**
			 *	Disconnect from the database
			 *	
			 *	@param resource $conn The connection to 
			 *	disconnect from
			 * 
			 *	@since 0.1.0
			 */
			public static function disconnect( $conn = null ){
				if( !$conn ){
					self :: $lastConnection = null;
					return;
				}
				$conn = null;
			}
			
			/**
			 *	Get the errors from the last
			 *	connection.
			 * 
			 *	@since 0.1.0
			 *	@return array The errors
			 */
			public static function getErrors(){
				return self :: $lastConnection->errorInfo();
			}

			/**
			 *	Create a new PDO instance
			 *	with a connection or the 
			 *	last connection
			 * 
			 *	@param resource $conn The connection
			 *	to use in the PDO instance
			 * 
			 *	@since 0.1.0
			 */
			public function __construct( $connection = null ){
				$this->connection = $connection;
				if( !$connection ){
					$this->connection = self :: $lastConnection;
				}
			}
			
			/**
			 *	Execute a query
			 *	and return the result
			 * 
			 *	@param Query|string $query The query 
			 *	
			 *	@since 0.1.0
			 *	@return MySQLPDOStatement The statement wrapper
			 */
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
			
			/**
			 *	Static method to execute a query
			 *	using the last connection or a 
			 *	connection via arguments
			 * 
			 *	@param Query|string $query The query
			 *	@param resource $conn The connection
			 * 
			 *	@since 0.1.0
			 *	@return MySQLPDOStatement The statement wrapper
			 */
			public static function doQuery( $query, $conn = null ){

				if( ! $conn ){
					$conn = self :: $lastConnection;
				}

				$mysql		= new MySQL( $conn );

				$result		= $mysql->execQuery( $query );
				
				return $result;

			}

			/**
			 *	Static method to execute a query
			 *	and then fetch its results
			 * 
			 *	@param Query|string $query The query to use 
			 *	and fetch the resulting data
			 *	@param resource $conn The connection
			 * 
			 *	@since 0.1.0
			 *	@return array The result
			 */
			public static function doFetchQuery( $query, $conn = null ){
				
				if( ! $conn ){
					$conn = self :: $lastConnection;
				}
				
				$result = MySQL :: doQuery( $query, $conn );

				$result = $result->fetchAll();
				
				return $result;

			}

			/**
			 *	Static method to execute an insert
			 *	and return its Id
			 * 
			 *	@param Query|string $query The query to use
			 *	for insert
			 * 
			 *	@since 0.1.0
			 *	@return int The Id of the insert
			 */
			public static function insert( $query ){
				$result = self :: doQuery( $query );
				return $result->lastInsertId();
			}

			/**
			 *	Get the last Id
			 * 
			 *	@param MySQL $result The object
			 *	
			 *	@since 0.1.0
			 *	@return int The Id of the insert
			 */
			public function lastInsertId( $result = null ){
				if( is_null($result)){
					$result = $this;
				}
				return $result->lastInsertId();
			}
			
		}
		
		/**
		 *	Wrapper class for handling
		 *	a PDO statement (MySQL)
		 * 
		 *	@package Ant
		 *	@subpackage Library
		 *	@since 0.1.0
		 */
		
		Class MySQLPDOStatement {

			private $statement, 
					$connection, 
					$query, 
					$binding;
			
			/**
			 *	Create a new PDO statement
			 *	from a query (string)
			 * 
			 *	@param string $query The query
			 *	@param resource $conn The connection
			 *	
			 *	@since 0.1.0
			 */
			public function __construct( $query, $connection = null ){
				$this->connection = $connection;
				if( !$connection ){
					$this->connection = MySQL :: $lastConnection;
				}

				$this->query = $query;
				$this->binding = array();


				$this->makeStatement();
			}

			/**
			 *	Make the statement
			 *	using the connection
			 * 
			 *	@since 0.1.0
			 */
			public function makeStatement(){
				$this->statement	= $this->connection->prepare( $this->query );
			}
			
			/**
			 *	Execute the statement, 
			 *	implementing bound values
			 * 
			 *	@since 0.1.0
			 */
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
			
			/**
			 *	Fetch all results
			 * 
			 *	@since 0.1.0
			 *	@return array The associated array of data
			 */
			public function fetchAll(){
				return $this->statement->fetchAll( \PDO::FETCH_ASSOC );
			}
			
			/**
			 *	Set the binding array
			 * 
			 *	@since 0.1.0
			 */
			function setBinding( $binding ){
				$this->binding = $binding;
			}
			
			/*
			 *	Get the last insert Id
			 * 
			 *	@since 0.1.0
			 *	@return int The insert Id
			 */
			public function lastInsertId(){
				return $this->connection->lastInsertId();
			}
		}
	}