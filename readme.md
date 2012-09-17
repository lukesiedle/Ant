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
@package, @subpackage, @since, @require. 

*Example:*

	/*
	 *	Application hosts important
	 *	data, global core methods,
	 *	and information concerning
	 *	the application state.
	 * 
	 *	@package Ant
	 *	@subpackage Application
	 *	@since 0.1.0
	 *	
	 */

### Commenting a class method

Methods within a class only need to specify a @since attribute, as well as @return
if the method returns something, as follows: 

*Example:*

	/*
	 *	Returns the application storage
	 *	object.
	 * 
	 *	@since 0.1.0
	 *	@return object The storage object
	 */

	public static function get(){
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

	saveComment( 'stripHTML:yes', 'emailParticipants:no' );
	
rather than:

	saveComment( true, false );
	
OR you could use an array to represent the arguments

	saveComment( array(
		'stripHTML' 		=> true,
		'emailParticipants' 	=> false
	));

##2. Implementation

### Namespaces

Ant is namespaced throughout. Most functions or classes that are part of Ant 
will be placed under the <code>Ant\\</code> namespace. Reusable aspects of code 
like the MySQL class are placed under the <code>Library\\</code> namespace, 
since they are portable.

### Clients

Ant relies on client specification, either using the request as a basis for 
determining the client e.g. <code>http://m.myapp.com (mobile)</code> or a
library like TERA/WURFL. The client may alter the nature of the view (visual 
and back-end). 

### The 'shared' space and the 'context' space

The shared space refers to the classes and other aspects of the application 
that are included with every request (according to the current client). The 
context space refers to the current context of the application, example 'Users'
'Admin' 'Categories'. Whereas the Shared space is usually system-related 
concepts, the Context space involves real-world concepts.

### Routing

Routing is handled by Ant's Router class. Routing requires an XML document 
called <code>route.xml</code> in the views folder of the appropriate client. The
XML nodes create a set of conditions to set variables based on the current
route (request), effectively white-listing URLs to perform certain tasks or load
views.

### Channels

Channeling allows an almost identical route to specify different 
functionality and output in the same context. For example, a request made to
the channel 'ajax' or 'api', might use identical functions but create the 
output as JSON instead of Html. Channeling can be implemented as follows:

	http://myapp.com/user/profile?channel=ajax
	
Channels need to be placed inside the shared views folder, under a subdirectory 
called 'channel'

### Classes

Classes that form the Ant application are the models should be loosely coupled 
where possible, so prefer hosting a function that deals with the view or request 
outside of the class in the 'controllers' section of the directory. Methods within 
the class should apply to the model and should not be designed for 
direct contact with the request/view. The Controller class can handle the 
transaction from the view to the class if needs be. 

### Controllers

Controllers are static functions called via the Controller class. The Controller class 
is also able to call a static method from within a class, should you prefer this structure. 
The Controller method <code>call</code> allows you to pass the context and the function, separated
by a period.
 
You can execute a controller as follows:

	Controller :: call('User.getUserById', array(
		'id' => 1
	));
	
Controllers can also be called directly via an http request. You need to whitelist
the controller inside route.xml, examples of which you can find inside the /setup page
when placing the Ant framework on your server.

# More Documentation

Consult the /setup page when using Ant for the first time for some basic examples to get you started. 
You can also find some more info at the [Ant Github page](http://luke-siedle.github.com/Ant). Advanced documentation
is still being put together, since there is no first release yet.