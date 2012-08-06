![Ant Logo](https://raw.github.com/luke-siedle/Ant/master/Ant-Logo.png)

# Ant PHP

Ant is a lightweight PHP application framework (that can do the heavy lifting).

## Requirements

- PHP 5.3+ (namespace/closure support)

## Dependencies

- Underscore.php

## Features

Lightweight MVC framework with easy-to-navigate directory structure, built-in 
classes for Routing, Views, Templating, Database calls. 

Implements PHP port of Less.css for stylesheets, as well as Google Closure 
Compiler for JavaScript optimization, and automatically handles browser caching.

URL rewriting using .htaccess file for CleanURLs

### Optional Features

- Authentication library for Facebook PHP SDK, and Google API PHP SDK.

## Notes

Open beta version. Unit tests are being implemented with PHPUnit. 

Documentation is incomplete, but files are commented throughout.

# Ant Conventions

##1.PHP Conventions

##a. Commenting

### Commenting a class

Comment each class with a description as well as the attributes:
@package, @subpackage, @since, @require. 

The attribute @type refers to whether the class is include as a shared
global, or because of the current route (request).

*Example:*

		/*
		 *	Application hosts important
		 *	data, global core methods,
		 *	and information concerning
		 *	the application state.
		 * 
		 *	@package Ant
		 *	@subpackage Application
		 *	@type Shared
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
route (request).

### Channels

Channeling allows an almost identical route to specify different 
functionality and output in the same context. For example, a request made to
the channel 'ajax' or 'api', might use identical functions but create the 
output as Json instead of Html. Channeling can be implemented as follows:

	http://myapp.com/user/profile?channel=ajax
	
Channels need to be placed inside the shared views folder, under a subdirectory 
called 'channel'

### Classes

Classes that form the Ant application are the models should be loosely coupled 
where possible, so prefer hosting a function that deals with the view or request 
outside of the class in the 'controllers' section of the directory. Methods within 
the class should apply to that class's object and should not be designed for 
direct contact with the request/view. The Controller class can handle the 
transaction from the view to the class if needs be. 

### Controllers

Controllers are static functions called via the Controller class. The controller 
can also call a static method from within a class. The Controller method 
<code>call</code> allows you to pass the context and the function, separated
by a period.
 
You should prefer using the Controller within the view to call the appropriate 
function, as follows:

		Controller :: call('User.getUserById', array(
			'id' => 1
		));