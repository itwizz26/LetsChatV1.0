<?php

/**
 * The default Forum controller
 * 
 */
class Forum extends Controller
{
	/*
	 * The default controller method
	 *
	 * @param string $id - The member UUID
	 * 
	 * @return void
	 * 
	 */
	public function index ($id = "")
	{
		// Call view page with memberId
		$this->view ('forum/index', [
						'memberId' => $id,
					]);
	}
}
