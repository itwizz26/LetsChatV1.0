<?php

/**
 * The App class
 *
 */
class App
{
	/*
	 * Define some class properties
	 *
	 */
	protected $controller = 'Home';
	protected $method = 'index';
	protected $params = [];
	
	/**
	 * __construct method
	 *
	 * Custom PHP class constructor
	 * 
	 * @param null
	 *
	 * @return void
	 * 
	 */
	public function __construct ()
	{
		// Set app URL
		$url = $this->parseUrl();
		
		// Check if controller exists
		if (file_exists ("../app/controllers/" . $url[0]. ".php"))
		{
			// Set controller
			$this->controller = $url[0];
			unset ($url[0]);
		}
		
		// Require controller class
		require_once ('../app/controllers/' . $this->controller . '.php');
		
		// Instantiate this controller
		$this->controller = new $this->controller;
		
		// Check if there are any extra parameters
		if (isset ($url[1]))
		{
			// If there is a method name
			if (method_exists ($this->controller, $url[1]))
			{
				// Set the method
				$this->method = $url[1];
				unset ($url[1]);
			}
		}
		
		// Check parameters
		$this->params = $url ? array_values ($url) : [];
		
		// Get parameters
		call_user_func_array ([$this->controller, $this->method], $this->params);
	}
	
	/**
	 * parseUrl method
	 *
	 * Parses/traverses the URL
	 *
	 * @param null
	 *
	 * @return array $url - the link array
	 *
	 */
	public function parseUrl ()
	{
		// Check if there is a URL
		if (isset ($_GET['url']))
		{
			// Retrun the URL as an array of elements
			return $url = explode ("/", filter_var (rtrim ($_GET['url'], "/"), FILTER_SANITIZE_URL));
		}
	}
}
	