<?php
	
	/*
	 *	Perform the full suite of 
	 *	tests using exec and 
	 *	PHPUnit.
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	
	// Set the tests directory //
	$testDir = __DIR__ . '/tests/';
	
	// Try find the PHP path programmatically //
	$pathToPhp = dirname(ini_get('extension_dir'));
	
	// Execute PHPUnit with all tests 
	//(will search for *test.php within tests directory //
	exec("$pathToPhp/phpunit $testDir", $output );
	
	if( count($output) > 0 ){
		
		echo '<pre>';
		echo '<h1>Ant PHP Unit Test Completed!</h1>';
		// Output the results to screen //
		foreach( $output as $each ){
			echo $each . '<br />';
		}
		
		echo '</pre>';
		
		echo "<br /><em>*Make sure you add tests to your
				Ant modifications before committing.</em>";
		
		
		
	} else {
		echo 'Unit testing did not occur. Check your PHP path 
				configuration in <pre>\test.php</pre>
					and make sure PHPUnit is installed in PEAR.';
	}