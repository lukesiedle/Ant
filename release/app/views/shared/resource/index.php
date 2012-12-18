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
			$handler = handler();
				
			// Convert to plain array data //
			if( $handler['result'] instanceof Collection ){
				$results['data'] = $handler['result']->toArray();
			} else {
				$results['data'] = $handler['result'];
				if( !is_array( $handler['result'] )){
					// Force array structure //
					$results['data'] = array();
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
			
			$errors = $handler['resource']->getErrors();
			
			$handler['result']['success'] = ! is_array( $errors );
			
			// Post resource task hook The Intention //
			if( isset($post['__intention']) ){
				
				Controller :: call( $post['__intention'], array(
					'resource'			=> $handler['resource'],
					'result'			=> $handler['result'],
					'errors'			=> $errors				
				));
				
			}
			
			// Report errors //
			if( $handler['errors'] ){
				$results['errors'] = $handler['errors'];
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
			// The following will cause problems with AJAX-based UI!
			Request :: clearCSRFtoken( Router :: getRequestURI() );
			
			if( $post['__token'] != $requestToken ){
				new \Core\Error( 403, 'resource_invalid_token' );
			}

			// Task is create but Id was sent (for RUD) //
			if( $task == 'create' && isset($request->id) ){
				new \Core\Error( 422, 'task_resource_mismatch' );
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
			
			// The resource can break out using an exception //
			try { 
				$result = $resource->{ $task }();
			} catch( \Exception $e ){
				$errors = $resource->getErrors();
				return array(
					'resource'	=> $resource,
					'errors'	=> $errors
				);
			}
			
			// We should always return a read (of a single resource) //
			if( $task != 'read' ){
				$result = $resource->read();
			}
			
			return array(
				'resource'	=> $resource,
				'result'	=> $result
			);

		}
	
	}