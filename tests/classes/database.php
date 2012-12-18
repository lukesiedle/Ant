<?php


	/*
	 *	Test functionality
	 *	of class Database. 
	 *	Some duplication of 
	 *	application state is 
	 *	required.
	 *	
	 *	@package Ant
	 *	@type Test Suite
	 *	@since 0.1.0
	 */

	use Core\Database as Database;
	use Core\Collection as Collection;
	use Core\Application as Application;
	use Core\Query as Query;
	use Extension\MySQL as MySQL;

	require_once( PROJECT_ROOT . '/app/extensions/mysql.php' );
	require_once( PROJECT_ROOT . '/app/classes/database.php' );
	require_once( PROJECT_ROOT . '/app/classes/query.php' );
	require_once( dirname(PROJECT_ROOT) . '/lib/php/underscore.php' );
	require_once( PROJECT_ROOT . '/app/classes/collection.php' );
	require_once( PROJECT_ROOT . '/app/classes/application.php' );
	
	// Initialize the App object //
	Application :: $app = new StdClass;
	
	// Set local environment to true //
	Application :: set(array(
		'local' => true,
		'connection' => array(
			'mysql' => MySQL :: connect(array(
				'host'		=> 'localhost',
				'db'		=> 'ant',
				'port'		=> '3306',
				'timeout'	=> '30',
				'username'	=> 'root',
				'password'	=> ''
			))
		)
	));
	
	global $globalStoreId;
	
	class TestDatabase extends PHPUnit_Framework_TestCase {
		
		public static $storeId = array();
		
		public function testTablePrefix(){
			
			Database :: setTablePrefix('ant_');
			
			$this->assertEquals( 'ant_', Database :: getTablePrefix() );
			
		}
		
		public function testInsertSingle(){
			
			$collection = new Collection(array(
				'test_value' => 'Database Test Single'
			), 'tests' );
			
			$storeId = 0;
			
			$result = Database :: insert( $collection, function( $id ) use ( & $storeId ) {
				$storeId = $id;
			});
			
			$this->assertTrue( $storeId > 0 );
			
			$query = new Query;
			$query->select('test.test_value', 'ant_tests test');
			$query->where('test.test_id = :id', array(
				'id' => $storeId
			));
			
			$result = Database :: query( $query )->first()->toArray();
			
			$this->assertEquals( $result['test_value'], 'Database Test Single' );
			
		}
		
		public function testInsertMultiple(){
			
			$array = array(array(
				'test_value' => 'Database Test Multi'
			),
			array(
				'test_value' => 'Database Test Multi'
			));
			
			$collection = new Collection( $array , 'tests' );
			
			$storeId = array();
			
			$result = Database :: insert( $collection, function( $id ) use ( & $storeId ) {
				$storeId[] = $id;
			});
			
			$this->assertTrue( count($storeId) == 2 );
			
			$query = new Query;
			$query->select('test.test_value', 'ant_tests test');
			$query->where('test.test_id = :id || test.test_id = :id2', array(
				'id' => $storeId[0],
				'id2' => $storeId[1]
			));
			
			$result = Database :: query( $query )->toArray();
			
			$this->assertEquals( $array, $result );
			
			global $globalStoreId;
			
			$globalStoreId = $storeId;

		}
		
		public function testUpdateSingle(){
			
			$array = array(
				'test_value' => 'Database Test Single Updated ' . date('U')
			);
			
			$collection = new Collection( $array , 'tests' );
			
			Database :: update($collection, array(
				'test_value' => 'Database Test Single'
			));
			
			$query = new Query;
			$query->select('test.test_value', 'ant_tests test');
			$query->where('test.test_value = :val', array(
				'val' => $array['test_value']
			));
			
			$result = Database :: query( $query )->first()->toArray();
			
			$this->assertEquals( $result, $array );
		}
		
		public function testUpdateMultiple(){
			
			$array = array(
				array(
					'test_value'	=> 'Database Test Update Multi'
				),
				array(
					'test_value'	=> 'Database Test Update Multi 2'
				)
			);
			
			$collection = new Collection( $array , 'tests' );
			
			$store		= array();
			
			Database :: insert( $collection , function( $id ) use ( & $store){
				$store[] = $id;
			});
			
			$collection->each( function( $record, $i ) use( $store ){
				$data = $record->toArray();
				$record->add(array(
					'test_value'	=> 'Multi Updated',
					'test_id'		=> $store[$i]
				));
			});
			
			Database :: updateMulti( $collection, 'test_id' );
			
			$query = new Query;
			$query->select('test.test_value', 'ant_tests test');
			$query->where('test.test_id = :id || test.test_id = :id2', array(
				'id' => $store[0],
				'id2' => $store[1]
			));
			
			$result = Database :: query( $query )->toArray();
			
			$this->assertEquals( $result[0]['test_value'], 'Multi Updated' );
			$this->assertEquals( $result[1]['test_value'], 'Multi Updated' );
			
		}
		
		public function testTruncate(){
			
			$query =  new Query( 'TRUNCATE ant_tests' );
			
			Database :: query( $query );
			
			$query = new Query( 'SELECT COUNT(*) AS count FROM ant_tests' );
			
			$result = Database :: query( $query )->toArray();
			
			$this->assertEquals( $result[0]['count'], 0 );
			
		}
	}