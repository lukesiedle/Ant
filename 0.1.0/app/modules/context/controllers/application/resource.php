<?php
	
	/*
	 *	Handles CRUD resource
	 *	requests.
	 *	
	 *	@since 0.1.0
	 */

	namespace Ant\Controller\Application;
	use \Ant\Request as Request;
	use \Ant\Controller as Controller;
	
	function resource( $args ){
		
		$request = (array) $args['request'];
		
		// Get POST data //
		$post = Request :: get('post');
		
		// URI data //
		$resourceName = $request['resource'];
		
		// A var was set if it shouldn't be there //
		$invalid = $request['invalid'];
		
		// Store useful vars //
		$data = $post;
		$task = $post['__task'];
		$intention = $post['__intention'];
		
		// Preliminary error checking //
		if( $invalid ){
			throw new \Exception( 'Invalid resource', 422 );
		}
		
		// Check the csrf token is valid for this resource //
		$requestToken = \Ant\Request :: CSRFtoken( implode('/', $request ) );
		
		if( $post['__token'] != $requestToken ){
			throw new \Exception( 'Invalid token', 422 );
		}
		
		// Task is create but Id was sent (for RUD) //
		if( $task == 'create' && isset($request['id']) ){
			throw new \Exception( 'Task/Resource mismatch. ' 
			. 'Create task should not specify Id.', 422 );
		}
		
		// Perform a read by default //
		if( !isset( $task ) ){
			$task = 'read';
			$data = array_merge( $data, $request );
		}
		
		// Set the request Id if available //
		$data['id'] = $request['id'];
		
		// Implement a resource //
		$resource = new \Ant\Resource( $resourceName, $data );
		
		// Execute the task, managed by the model //
		$resource->setTask( $task );
		
		// Execute the task, managed by the model //
		$resource->doTask( $task );
		
		/*
		 *	The intention is used
		 *	for giving the CRUD task
		 *	some context, and allows
		 *	for post-CRUD task executions.
		 *	
		 *	@example 'User.registration.register'
		 *	@since 0.1.0
		 */
		
		// Post resource task hook The Intention //
		if( isset($intention) ){
			Controller :: call( $intention, array(
				'resource'	=> $resource,
				'is_ajax'	=> $args['is_ajax']
			));
		}
		
		return $result;
		
	}