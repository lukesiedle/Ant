<?php
	
	/*
	 *	Test the functionality 
	 *	of class Collection
	 * 
	 *	@package Ant
	 *	@type Test Suite
	 *	@since 0.1.0
	 */
	
	// Require Collection and its dependency, Underscore //
	require_once( PROJECT_ROOT . '/app/classes/configuration.php' );
	
	class TestConfiguration extends PHPUnit_Framework_TestCase
	{
		public static $config = array(
			'facebook' => array(
				'secret'	=> '123',
				'appid'		=> '321'
			)
		);
		
		public function testSet()
		{
			\Core\Configuration :: set( self :: $config );
			$this->assertEquals( self :: $config, \Core\Configuration :: $data );
		}
		
		public function testSetExtended()
		{
			$ext = array(
				'extended' => 'yes'
			);
			
			$arr = array_merge( self :: $config, $ext );
			
			\Core\Configuration :: set( self :: $config  );
			\Core\Configuration :: set( $ext );
			
			$this->assertEquals( $arr, \Core\Configuration :: $data );
			
		}
		
		public function testGetAll()
		{
			
			$arr = array(
				'facebook' => array(
					'secret'	=> '123',
					'appid'		=> '321'
				),
				'extended' => 'yes'
			);
			
			$this->assertEquals( $arr, \Core\Configuration :: get() );	
		}
		
		public function testGetSingle()
		{
			
			$arr = array(
				'facebook' => array(
					'secret'	=> '123',
					'appid'		=> '321'
				),
				'extended' => 'yes'
			);
			
			$this->assertEquals( $arr['facebook'], \Core\Configuration :: get('facebook') );
		}
		
		
	}
	
	// Run the test suite //
	new TestConfiguration;
	
?>