<?php
	
	/**
	 *	Test the functionality 
	 *	of class Session
	 *	 
	 *	@since 0.2.1
	 */
	
	require_once( PROJECT_ROOT . '/app/classes/session.php' );
	
	\Core\Session :: init();
	
	// Note the session does not persist in PHPUnit //
	class TestSession extends PHPUnit_Framework_TestCase
	{
		public static $data = array(
			'username'	=> 'johndoe',
			'email'		=> 'jd@gmail.com'
		);
		
		public static $keychain = 'ant';
		
		public function testInit(){
			$this->assertEquals( true, session_id() != null );
		}
		
		public function testAdd()
		{
			\Core\Session :: add( 'user', self :: $data );
			
			$this->assertEquals( 
				array('user' => self :: $data),
				$_SESSION[ self :: $keychain ] 
			);
		}
		
		public function testAddMore()
		{
			\Core\Session :: add( 'user', self :: $data );
			\Core\Session :: add( 'user_2', self :: $data );
			
			$data = array_merge(array(
				'user'		=> self :: $data
				), array(
				'user_2'	=> self :: $data
				)
			);
			
			$this->assertEquals( 
				$data,
				$_SESSION[ self :: $keychain ] 
			);
		}
		
		
		public function testGetAll()
		{
			$this->assertEquals( \Core\Session :: get(), $_SESSION[ self :: $keychain ] );	
		}
		
		public function testGetSingle()
		{
			$this->assertEquals( \Core\Session :: get('user'), $_SESSION[ self :: $keychain ]['user'] );	
		}
		
		public function testClearSingle()
		{
			$this->testAddMore();
			
			\Core\Session :: clear('user_2');
			
			$this->assertEquals( self :: $data, $_SESSION[ self :: $keychain ]['user'] );
		}
		
		public function testClearAll()
		{
			$this->testAddMore();
			
			\Core\Session :: clear();
			
			$this->assertEquals( array(), $_SESSION[ self :: $keychain ] );
		}
		
		
	}
	
	// Run the test suite //
	new TestSession;
	
?>