![Ant Logo](https://raw.github.com/luke-siedle/Ant/master/Ant-Logo.png)

# Ant PHP

Ant is a lightweight PHP application framework (that can do the heavy lifting).

## Requirements

- PHP 5.3+ (namespace/closure support)

## Dependencies

- Underscore.php

## Features

Lightweight MVC framework with easy-to-navigate directory structure, built-in 
classes for Routing, Views, Templating, MySQL (PDO). 

Implements Java port of Less.css for generating stylesheets server-side, as well as Google Closure 
Compiler for JavaScript optimization, and automatically handles browser caching.

URL rewriting using .htaccess file for CleanURLs

### Optional Features

- Authentication library for Facebook PHP SDK, and Google API PHP SDK.

## Unit Tests

Unit tests are available, and continue to be implemented with PHPUnit. 

# Ant Conventions

##1.PHP Conventions

##a. Commenting

### Commenting a class

Comment each class with a description as well as the attributes:
@package, @subpackage, @since.

*Example:*
	
	/**
	 *	Application hosts important
	 *	data, global core methods,
	 *	and information concerning
	 *	the application state.
	 * 
	 *	@package Ant
	 *	@subpackage Application
	 *	@since 0.1.0	
	 */

### Commenting a class method

Methods within a class only need to specify @param for parameters, @since attribute, as well as @return
if the method returns something, as follows. If you comment exactly like the following, it achieves
compatibility with PHPDocs, and many IDEs will show aspects of the comment in the hint.

*Example:*

	/**
	 *	Extends the application storage
	 *	object.
	 *	
	 *	@param array $arr The data to store
	 *	
	 *	@since 0.1.0
	 *	@return object The storage object
	 */
	public static function set( $arr ){
		return self :: $app;
	}

##b. Scope

### Instance methods vs. static methods IMPORTANT

Don't create instant methods if you don't need an instance. If only a single object is required, 
rather use static methods and store data inside static variables or constants in the class. In this case, 
the class is a wrapper class for a set of functions and variables that can exist uniquely in the script.

When you need an object to be instantiated more than once, then you should use instance methods, or a combination
of instance and static methods. Usually these type of PHP classes represent models, a set of rules signifying how
data should be manipulated. For example 'users' or 'articles'.

This discipline ensures that the programmer is always aware of what type of scope is required, and ultimately 
avoids confusion.

### Chaining

Support chaining in classes if instance methods manipulate the object and don't return a value, 
i.e. return <code>$this</code>.

##c. Case

Use CapitalCamelCase for classes, camelCase for functions, and variables. Use underscore_case for array keys, and UPPERCASE_UNDERSCORE_CASE
for constants.

##d. Function arguments

Borrowing from Wordpress, avoid making arguments booleans or some type that is meaningless to the uninformed reader, 
but rather pass a string explaining the purpose of the argument. For example :

	saveComment( 'stripHTML', 'emailParticipants' );
	
rather than:
	
	saveComment( true, false );
	
OR you could use an array to represent the arguments
	
	saveComment( array(
		'stripHTML' 		=> true,
		'emailParticipants' 	=> false
	));