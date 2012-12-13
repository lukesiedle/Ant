<?php
	
	/**
	 *	The Resource client
	 *	is designed for 
	 *	CRUD requests to
	 *	the app. The URLs which
	 *	access resources must
	 *	observe the same structure.
	 *	
	 *	@package Ant
	 *	@since 0.1.0
	 */
	
	namespace View\Resource {
		
		use \Core\Router as Router;
		use \Core\Application as Application;
		use \Core\Template as Template;
		use \Core\CollectionSet as CollectionSet;
		use \Core\Controller as Controller;
		use \Core\Request as Request;
		use \Core\Document as Document;
		use \Core\Session as Session;
		use \Core\Resource as Resource;
		
		function index(){
			
			// Get the CSRF tokens from session and add them to memory
			// @since 0.1.0 //
			Request :: setCSRF( Session :: get('csrf') );
			
			// Basic prevention of request forgery //
			$post = Request :: get('post');	
			
			// Is it an AJAX request //
			$isAjax = isset( $post['__ajax'] );				
			
			try {
				
				// Run the resource handler
				$data = handler();				
				
				// The resource data was insufficient //
				if( $data['user.errors'] ){
					throw new \Exception('A data error occurred.', 422 );
				}
				
				$results = array(
					'success' => true
				);
				
				// Convert to plain array data //
				if( $data['result'] instanceof Collection ){
					$results['data'] = $data['result']->toArray();
				} else {
					$results['data'] = $data['result'];
					if( !is_array( $data['result'] )){
						// Force array structure //
						$results['data'] = array();
					}
				}
				
			} catch ( \Exception $e ) {
				
				if( $e->getCode() == 404 
						&& ! Application :: get()->developerMode ){
					
					// Adds the error header //
					Document :: addHeader('HTTP/1.0 404 Not Found');
					$results['system.error']['message'] = '404 Not Found.';
					
				} else {
					
					$results['success']			= false;
					
					// Show the message and trace //
					$results['system.error']['message'] = $e->getMessage();
					$results['system.error']['code']	= $e->getCode();
					
					switch( $e->getCode() ){
						
						case 0 :
							$fatalError = true;
						
						case 403 : 
							// Forbidden //
							Document :: addHeader('HTTP/1.0 403 Forbidden');
							$fatalError = 403;
							break;
						case 404 :
							// Adds the error header //
							Document :: addHeader('HTTP/1.0 404 Not Found');
							$fatalError = 404;
							break;
						case 422 : 
							// Unprocessable entity - syntactically correct, bad semantics //
							Document :: addHeader('HTTP/1.0 422 Unprocessable Entity');
							break;
					}
				}
			}
			
			// Get the CSRF tokens from memory and store them
			Session :: clear( 'csrf' );
			Session :: add( 'csrf', Request :: getCSRF() );
			
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
			if( ! $fatalError && 
					isset($post['__intention'])){
				
				Controller :: call( $post['__intention'], array(
					'resource'		=> $data['resource'],
					'result'		=> $results,
					'user.errors'	=> $data['user.errors']
				));
			}
			
			Document :: addHeader( 'Content-type: Application/Json' );
			
			// Set the headers //
			Application :: setHeaders();
			
			// Output JSON with header //
			echo json_encode( $results );			
		}
		
		/**
		 *	Handles CRUD resource
		 *	requests and calls the intention
		 *	controller if one exists.
		 *	
		 *	@param array $args The arguments
		 *	including 'request'
		 * 	
		 *	@since 0.1.0
		 *	@return array The data 
		 *	from the resource
		 */
		function handler(){
			
			$request = Router :: getRouteVars();
			
			// Get POST data //
			$post = Request :: get('post');
			
			// Resource //
			$resourceName = $post['__resource'];
			
			// Store useful vars //
			$task = $post['__task'];
			
			// Check the csrf token is valid for this resource //
			$requestToken = Request :: CSRFtoken( Router :: getRequestURI() );
			
			// Then remove token to prevent automatic retries //
			// Request :: clearCSRFtoken( Router :: getRequestURI() );
			
			if( $post['__token'] != $requestToken ){
				throw new \Exception( 'Invalid token', 403 );
			}

			// Task is create but Id was sent (for RUD) //
			if( $task == 'create' && isset($request->id) ){
				throw new \Exception( 'Task/Resource mismatch. ' 
				. 'Create task should not specify Id.', 422 );
			}
			
			// Perform a read by default //
			if( !isset( $task ) ){
				$task = 'read';
				$data = array_merge( $post, $request );
			}

			// Set the request Id if available //
			$data['id'] = $request->id;
			
			// Implement a resource //
			$resource = new Resource( $resourceName, $post );
			
			try {
				$result = $resource->{ $task }();
			} catch( \Exception $e ){
				return array(
					'user.errors'	=> $resource->handler->getErrors(),
					'resource'		=> $resource,
					
					// Maybe a system error occurred //
					'system.error'	=> $e->getMessage()
				);
			}
			if( $task != 'read' ){
				$result = $resource->read();
			}
			
			return array(
				'resource' => $resource,
				'result'	=> $result
			);

		}
	
	}