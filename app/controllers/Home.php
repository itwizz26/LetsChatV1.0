<?php

/**
 * The default Home controller
 * 
 */
class Home extends Controller
{
	/**
	 * The default controller method
	 *
	 * @param null
	 * 
	 * @return void
	 * 
	 */
	public function index ()
	{
		// Call the view
		$this->view ('home/index', []);
	}
}
