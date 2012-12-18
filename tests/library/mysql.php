<?php

	/*
	 *	Test functionality 
	 *	of portable library
	 *	MySQL
	 * 
	 *	@package Library
	 *	@type Test Suite
	 *	@since 0.1.0
	 */
	 
	require_once(PROJECT_ROOT . '/app/extensions/mysql.php');
	
	use \Extension\MySQL as MySQL;
	
	class TestLibraryMySQL extends PHPUnit_Framework_TestCase {
		
		public function testConnection(){
			
			$connection = MySQL :: connect(array(
				'host'		=> 'localhost',
				'db'		=> 'ant',
				'port'		=> '3306',
				'timeout'	=> '30',
				'username'	=> 'root',
				'password'	=> ''
			));
			
		}
		
		public function testQueryInsertAndLastId(){
			
			$result = MySQL :: doQuery( 
			"
				INSERT INTO ant_tests
				(test_value)
				VALUES
				('test1')
			" );
			
			$this->assertTrue( MySQL :: lastInsertId( $result ) > 0 );
			$this->assertTrue( $result->lastInsertId() > 0 );
			
		}
		
		public function testDoFetchQuery(){
			
			$results = MySQL :: doFetchQuery( 
			"
				SELECT COUNT(*) AS count
				FROM ant_tests
			" );
			
			$this->assertTrue( $results[0]['count'] > 0 );
		}
		
		public function testQueryBinding(){
			
			require_once(PROJECT_ROOT . '/app/classes/query.php');
			
			$query	= new Core\Query("SELECT test_id FROM ant_tests WHERE test_id = :id");
			
			$query->bind(array(
				':id' => 1
			));
			
			$results = MySQL :: doFetchQuery( $query );
			
			$this->assertTrue( $results[0]['test_id'] == 1 );
			
		}
		
	}
	
	new TestLibraryMySQL;
	