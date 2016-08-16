<?php

/**
 * The default Signup controller
 * 
 */
class Signup extends Controller
{
	/*
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
		$this->view ('signup/index', []);
	}
}
