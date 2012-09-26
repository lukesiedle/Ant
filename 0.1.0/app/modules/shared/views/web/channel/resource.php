<?php
	
	/*
	 *	The Resource channel
	 *	is designed for 
	 *	CRUD requests to
	 *	the app. The URLs which
	 *	access resources must
	 *	observe the same structure.
	 * 
	 *	@package Ant
	 *	@since 0.1.0
	 */
	
	namespace Ant\Web\Channel\Resource {
		
		use \Ant\Router as Router;
		use \Ant\Application as Application;
		use \Ant\Template as Template;
		use \Ant\CollectionSet as CollectionSet;
		use \Ant\Controller as Controller;
		
		function index(){
			
			// Basic prevention of request forgery //
			$post = \Ant\Request :: get('post');	
			
			// Is it an AJAX request //
			$isAjax = isset( $post['__ajax'] );
			
			switch( false ){
				// Post must have at least 2 fields //
				case count( $post >= 2 ) :
				
				// Token must match resource/request //
				case \Ant\Request :: CSRFtoken( $post['__resource'] ) == $post['__token'] :
					\Ant\Application :: setError();
					return false;
				break;
			}
			
			// Run the resource controller
			// catching any errors 
			// @since 0.1.0 //
			try {
				$data = \Ant\Controller :: call( 'Application.resource', (array) Router :: getRequestVars() );
				
				$results = array(
					'success' => true
				);
				
				if( $data instanceof \Ant\Collection ){
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
					\Ant\Document :: addHeader('HTTP/1.0 404 Not Found');
					$results['error']['message'] = '404 Not Found.';
					
				} else {
					
					$results['success'] = false;
					
					// Show the message and trace //
					$results['error']['message'] = $e->getMessage();
					$results['error']['code']	= $e->getCode();
					$results['error']['trace']	= $e->getTrace();
					
					switch( $e->getCode() ){
						case 403 : 
							// Forbidden //
							\Ant\Document :: addHeader('HTTP/1.0 403 Forbidden');
							break;
						case 404 :
							// Adds the error header //
							\Ant\Document :: addHeader('HTTP/1.0 404 Not Found');
							break;
						case 422 : 
							// Unprocessable entity - syntactically correct, bad semantics //
							\Ant\Document :: addHeader('HTTP/1.0 422 Unprocessable Entity');
							break;
					}
				}
			}
			
			// Output JSON with header //
			echo json_encode( $results );
			
			\Ant\Document :: addHeader( 'Content-type:Application/Json' );
		}
	
	}