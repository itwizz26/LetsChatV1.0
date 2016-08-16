<?php

/**
 * Controller Class
 *
 */
class Controller
{
    /*
     * model method
     *
     * @param string $model - The model class name, e.g. Database
     * 
     * @return void
     * 
     */
    public function model ($model)
	{
        // Get the model - Database
        require_once '../app/models/' . $model . '.php';
    }
    
    /**
     * view method
     *
     * @param string $view - the name of the view
     * @param array $data - data passed in the url
     * 
     * @return void
     * 
     */
    public function view ($view, $data = [])
	{
        // Include $this view
        require_once '../app/views/' . $view . '.php';
    }
}