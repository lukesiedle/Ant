<?php
	
	namespace Route\User;
	
	function register(){
		return array(
			'module'	=> 'User',
			'template'	=> 'register',
			'doctitle'	=> 'Signup Here'
		);
	}
	
	function editProfile(){ 
		return array(
			'module'	=> 'User',
			'template'	=> 'register',
			'doctitle'	=> 'Signup Here'
		);
	}
	
	Toro :: serve( array(
		"/" => "Main",
		"/user/:number" => "Route\User\Register",
		"/user/register" => "Register",
		"/manufacturer/([a-zA-Z]+)" => "ManufacturerHandler"
	));


	function home(){
		echo stuff;
	}