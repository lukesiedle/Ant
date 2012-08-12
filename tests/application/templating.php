<?php

	/*
	 *	Test the templating
	 *	of the application
	 * 
	 *	@package Ant
	 *	@type Test Suite
	 *	@require CURL extension
	 *	@since 0.1.0
	 */
	 
	 class TestTemplating extends PHPUnit_Framework_TestCase {
		 
		 public function testFrameLoop(){
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/templating/frame' );
			 
			 $this->assertEquals( $output, 'Frame:Loop1Loop2' );
		 }
		 
		 public function testPageLoop(){
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/templating/page' );
			 
			 $this->assertEquals( $output, 'Frame:Loop1Loop2Page:Level1 : 1Level1 : 2Level1 : 3' );
		 }
		 
		 public function testLoopDeep(){			 
			 
			 $output = file_get_contents( PUBLIC_ROOT . '/test/templating/deep' );
			 
			 $this->assertEquals( $output, 'Page:Level1 : 1Level2 : 1Level3 : 1Level3 : 2Level2 : 2Level1 : 2Level2 : 1Level3 : 1Level3 : 2Level2 : 2Level1 : 3Level2 : 1Level3 : 1Level3 : 2Level2 : 2' );
		 }
		 
	 }