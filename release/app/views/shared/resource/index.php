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
			
			// Run the resource handler
			try {
				
				$data = handler();
				
				if( $data['errors'] ){
					throw new \Exception('An error occurred.', 403 );
				}
				
				$results = array(
					'success' => true
				);
				
				if( $data instanceof Collection ){
					$results['resource'] = $data->toArray();
				} else {
					if( !is_array( $data )){
						throw new \Exception('Data returned by resource must be an array.');
					}
					$results['resource'] = $data;
				}
				
			} catch ( \Exception $e ) {
				
				if( $e->getCode() == 404 
						&& ! Application :: get()->developerMode ){
					
					// Adds the error header //
					Document :: addHeader('HTTP/1.0 404 Not Found');
					$results['error']['message'] = '404 Not Found.';
					
				} else {
					
					$results['success']			= false;
					
					// Show the message and trace //
					$results['error']['message'] = $e->getMessage();
					$results['error']['code']	= $e->getCode();
					// $results['error']['trace']	= $e->getTrace();
					
					switch( $e->getCode() ){
						case 403 : 
							// Forbidden //
							Document :: addHeader('HTTP/1.0 403 Forbidden');
							break;
						case 404 :
							// Adds the error header //
							Document :: addHeader('HTTP/1.0 404 Not Found');
							break;
						case 422 : 
							// Unprocessable entity - syntactically correct, bad semantics //
							Document :: addHeader('HTTP/1.0 422 Unprocessable Entity');
							break;
					}
				}
			}
			
			// Store any internal errors that occurred //
			if( $data['errors'] ){
				$results['errors'] = $data['errors'];
			}
			
			/*
			*	The intention is used
			*	for giving the CRUD task
			*	some context, and allows
			*	for post-CRUD task executions.
			*
			*	Only occurs on success.
			*	
			*	@example 'User.registration.register'
			*	@since 0.1.0
			*/
			
			// Post resource task hook The Intention //
			if( isset($post['__intention']) ){
				Controller :: call( $post['__intention'], array(
					'resource'	=> $resource,
					'result'	=> $results
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
				$data = array_merge( $post, $request );
			}

			// Set the request Id if available //
			$data['id'] = $request->id;
			
			// Implement a resource //
			$resource = new Resource( $resourceName, $post );
			
			try {
				$resource->{ $task }();
			} catch( \Exception $e ){
				return array(
					'errors' => $resource->handler->getErrors()
				);
			}
			
			$result = $resource->read();
			
			return $result;

		}
	
	}