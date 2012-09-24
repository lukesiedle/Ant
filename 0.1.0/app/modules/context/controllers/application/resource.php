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
	
	function resource( $request ){
		
		// Get POST data //
		$post = Request :: get('post');
		
		$data = $post;
		$resourceName = $request['resource'];
		$task = $post['__task'];
		$intention = $post['__intention'];
		
		// Perform a read by default //
		if( !isset( $task ) ){
			$task = 'read';
			$data = array_merge( $data, $request );
		}
		
		// Include the model of the resource //
		$model = "\Ant\Model\\" . $resourceName;
		$modelInclude = 'app/classes/models/' 
			. strtolower( $resourceName ) . '.php';
		
		// Try include the model //
		if( file_exists($modelInclude) ){
			require_once( $modelInclude );
		} else {
			// Use the base model //
			$model = '\Ant\Model';
		}
		// Instantiate the model for this resource //
		$model = new $model( $resourceName );
		
		// Implement a resource //
		$resource = new \Ant\Resource( $model, $data );
		
		$resource->checkTask( $task );
		
		// Check if the user has adequate permissions //
		$permsController = "$resourceName.permission.crud.$task";
		
		try { 
			
			if( Controller :: call( $permsController , array(
				'intention' => $intention
			))){
				// Execute the task, managed by the model //
				$result = $model->task( $resource );
				
			} else {
				throw new \Exception( 'Insufficient permission for task ' . $task, 0 );
			}
			
		} catch( \Exception $e ){
			switch( $e->getCode()){
				case 0 :
					throw new \Exception( $e->getMessage(), 404 );
					break;
				default : 
					throw new \Exception( "Resource '$resourceName' does not exist ", 404 );
					
			}
			
		}
		
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
				'resource' => $resource
			));
		}
		
		return $result;
		
	}