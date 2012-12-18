<?php
	
	/**
	 *	Test the functionality 
	 *	of class Resource
	 * 
	 *	Has multiple dependencies
	 *	 
	 *	@since 0.2.1
	 */
	
	require_once( PROJECT_ROOT . '/app/classes/application.php' );
	require_once( PROJECT_ROOT . '/app/classes/query.php' );
	require_once( PROJECT_ROOT . '/app/classes/resource.php' );
	require_once( PROJECT_ROOT . '/app/classes/data.php' );
	require_once( PROJECT_ROOT . '/app/data/test.php' );
	require_once( PROJECT_ROOT . '/app/classes/database.php' );
	require_once( PROJECT_ROOT . '/app/classes/collection.php' );
	require_once( PROJECT_ROOT . '/app/extensions/mysql.php' );
	
	require_once( dirname(PROJECT_ROOT) . '/lib/php/underscore.php' );
	
	// Initialize the App object //
	\Core\Application :: $app = new StdClass;
	
	// Set local environment to true //
	\Core\Application :: set(array(
		'local' => true,
		'connection' => array(
			'mysql' => \Extension\MySQL :: connect(array(
				'host'		=> 'localhost',
				'db'		=> 'ant',
				'port'		=> '3306',
				'timeout'	=> '30',
				'username'	=> 'root',
				'password'	=> ''
			))
		)
	));
	
	\Core\Database :: setTablePrefix('ant_');
	
	// Note the session does not persist in PHPUnit //
	class TestResource extends PHPUnit_Framework_TestCase
	{
		
		public function testResourceCreate(){
			
			$rs = new \Core\Resource('test', array(
				'test_value' => 'PHPUnit'
			));
			
			$rs->create();
			
		}
		
		public function testResourceRead(){
			
			$rs = new \Core\Resource('test', array(
				'id' => 1
			));
			
			$this->assertEquals( $rs->read(), array(
				'test_id' => 1,
				'test_value' => 'PHPUnit'
			));
			
		}
		
		public function testResourceUpdate(){
			
			$rs = new \Core\Resource('test', array(
				'value' => 'PHPUNIT',
				'id'	=> '1'
			));
			
			$rs->update();
			
			$testUpdate = new \Core\Resource('test', array(
				'id'	=> '1'
			));
			
			$this->assertEquals( $testUpdate->read(), array(
				'test_id' => 1,
				'test_value' => 'PHPUNIT'
			));
			
		}
		
		public function testResourceDelete(){
			
			$rs = new \Core\Resource('test', array(
				'id'	=> '1'
			));
			
			$rs->delete();
			
			$testDelete = new \Core\Resource('test', array(
				'id'	=> '1'
			));
			
			try {
				$testDelete->read();
			} catch ( Exception $e ){
				$this->assertTrue( true, true );
			}
			
		}
		
	}
	
	// Run the test suite //
	new TestResource;
	
	\Core\Database :: query( new \Core\Query( 'TRUNCATE ant_tests' ) );