<?php

	/*
	 *	Test the routing
	 *	of the application
	 * 
	 *	@package Ant
	 *	@type Test Suite
	 *	@require CURL extension
	 *	@since 0.1.0
	 */
	 
	 class TestRoutingBasicTemplating extends PHPUnit_Framework_TestCase {
		 
		 public function testRouteDefault(){
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/' );
			 
			 $this->assertEquals( $output, 'Test Suite Home' );
		 }
		 
		 public function testRouteToHome(){
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/home/' );
			 
			 $this->assertEquals( $output, 'Test Suite Home' );
		 }
		 
		 public function testRouteToNotFound(){			 
			 
			 // Test 404 //
			 $is404 = false;
			 try {
				$output = file_get_contents( PUBLIC_ROOT . '/test/nothing/' );
			 } catch( Exception $e ){
				 $is404 = true;
			 }
			 
			 $this->assertTrue( $is404 );
			 
		 }
		 
		 public function testRouteDeep(){			 
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/routing/deep/deeper/deepest/' );
			 
			 $this->assertEquals( $output , 'deepest' );
			 
		 }
			 
		 public function testRouteAlternateFrame(){			 
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/routing/testframe/' );
			 
			 $this->assertEquals( $output , 'testframe' );
			 
		 }
		 
	 }