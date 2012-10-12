<?php

	namespace Controller;
	
	use \Core\Request as Request;
	use \Core\Application as App;
	use \Core\Resource as Resource;
	use \Core\Document as Document;
	use \Core\Router as Router;
	use \Core\Controller as Controller;
	
	/**
	 *	The Application controller.
	 *	
	 */
	class Application {
		
		/**
		 *	Checks if the app is local
		 * 
		 *	@since 0.1.0
		 *	@return bool 
		 */
		static function isLocal(){
			return App :: get()->local;
		}
		
		/**
		 *	Handles CRUD resource
		 *	requests and calls the intention
		 *	controller if one exists.
		 * 
		 *	May throw errors which the resource
		 *	channel is designed to catch.
		 *	
		 *	@param array $args The arguments
		 *	including 'request'
		 * 	
		 *	@since 0.1.0
		 *	@return array The data 
		 *	from the resource
		 */
		static function resource( $args ){
			
			$request = Router :: getRequestVars();
			
			// Get POST data //
			$post = Request :: get('post');

			// Resource //
			$resourceName = $request->resource;
			
			// Store useful vars //
			$data = $post;
			$task = $post['__task'];
			$intention = $post['__intention'];
			
			// Preliminary error checking //
			// Check if the request is considered invalid by router //
			if( $request->invalid ){
				throw new \Exception( 'Invalid resource', 422 );
			}
			
			// Check the csrf token is valid for this resource //
			foreach( $request as $key => $each ){
				if( $each ){
					$requestParts[ $key ] = $each;
				}
			}
			
			$requestToken = Request :: CSRFtoken( implode('/', $requestParts ) );
			
			if( $post['__token'] != $requestToken ){
				throw new \Exception( 'Invalid token', 422 );
			}

			// Task is create but Id was sent (for RUD) //
			if( $task == 'create' && isset($request->id) ){
				throw new \Exception( 'Task/Resource mismatch. ' 
				. 'Create task should not specify Id.', 422 );
			}

			// Perform a read by default //
			if( !isset( $task ) ){
				$task = 'read';
				$data = array_merge( $data, $request );
			}

			// Set the request Id if available //
			$data['id'] = $request->id;
			
			// Implement a resource //
			$resource = new Resource( $resourceName, $data );
			
			$resource->{ $task }();
			
			$result = $resource->read();
			
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
		
	}