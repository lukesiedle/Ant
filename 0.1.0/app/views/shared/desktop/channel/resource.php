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
	
	namespace View\Desktop\Channel\Resource {
		
		use \Core\Router as Router;
		use \Core\Application as Application;
		use \Core\Template as Template;
		use \Core\CollectionSet as CollectionSet;
		use \Core\Controller as Controller;
		use \Core\Request as Request;
		use \Core\Document as Document;
		
		// Context //
		use \Controller\Application as AppController;
		
		function index(){
			
			// Basic prevention of request forgery //
			$post = Request :: get('post');	
			
			// Is it an AJAX request //
			$isAjax = isset( $post['__ajax'] );				
			
			// Run the resource controller
			// catching any errors 
			// @since 0.1.0 //
			try {
				
				$data = AppController :: resource( array(
					'is_ajax' => $isAjax
				));
				
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
					
					$results['success'] = false;
					
					// Show the message and trace //
					$results['error']['message'] = $e->getMessage();
					$results['error']['code']	= $e->getCode();
					$results['error']['trace']	= $e->getTrace();
					
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
			
			Document :: addHeader( 'Content-type: Application/Json' );
			
			// Set the headers //
			Application :: setHeaders();
			
			// Output JSON with header //
			echo json_encode( $results );			
		}
	
	}