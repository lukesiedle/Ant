<?php

	namespace Ant\Web\Channel\Ajax {
		
		function index( $request ){
			
			// The view returns a collection set //
			$view		= \Ant\Router :: loadRouteView();
			
			echo json_encode(array(
				'data' => $view->toArray()
			));
			
			\Ant\Document :: addHeader('Content-type:Application/Json');
		}
	
	}
?>