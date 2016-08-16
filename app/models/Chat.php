<?php

/**
 * Live Chat Class
 * 
 */
class Chat {
    /*
	 * Define class properties
	 *
	 */
	const HOST = "localhost";
    const DBASE = "live_chat";
    const USER = "root";
    const PASS = "";
    private static $_instance = null; // To store the Pdo instance
    private $_pdo, // To store pdo object
            $_query, // The last query executed
            $_results, // Store result set
            $_count = 0, // To count rows affected
            $_error = false; // Represent error state
			
    /**
     *
     * __construct method
     *
     * Class constructor
     *
     * @param null
     * @return void
     * 
     */
    public function __construct ()
	{
		// Try..Catch Exception
        try
		{
            // Set PDO instance: HOST->DB->USER->PASS
            $this->_pdo = new PDO ("mysql:host=" . self::HOST . ";dbname=" . self::DBASE, self::USER, self::PASS);
			
        }
        catch (PDOException $error)
		{
            // Show caught error and die/stop processing
            die ("There has been an error: " . $error->getMessage());
			
        }
    }
    
    /**
     *
     * GetInstance method
     *
     * Sets the PDO (PHP Data Objects) instance
     *
     * @param null
     *
     * @return self - class instance
     * 
     */
    public static function getInstance ()
	{
        // Check if instance set
        if (!isset (self::$_instance))
		{
            // Then set this instance
            self::$_instance = new Chat();
			
        }
        
        // Return the active instance
        return self::$_instance;
    }
    
    /**
     *
     * Query method
     *
     * Prepares and queries a mysql database
     *
     * @param string $stmt -  SQL statement to run
     * @param array $params - the WHERE parameters
     *
     * @return $this - bool (true|false)
     * 
     */
    public function query ($stmt, $params = [])
	{
        // Set the error to false
        $this->_error = false;
        
        // Prepare query
        if ($this->_query = $this->_pdo->prepare ($stmt))
		{
            // Bind results
            $counter = 1;
            
            if (count ($params))
			{
                // Loop through where parameters
                foreach ($params as $param)
				{
                    // Bind the query values
                    $this->_query->bindValue ($counter, $param);
                    
                    // Inrement loop
                    $counter++;
                }
            }
            
            // If query has executed successfully
            if ($this->_query->execute())
			{
				// Get results and count
				$this->_results = $this->_query->fetchAll (PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
            }
            else
			{
                // Set error
                $this->_error = true;
            }
        }
        
        // Return this object
        return $this;
    }
    
    /**
     *
     * Action method
     *
     * @param string $action - the actions to take (select or delete)
     * @param string $table - the table name
     * @param array $where - the WHERE clause
     * @param string  $sort - optional sort string, e.g. ORDER BY `column_name` ASC
     * 
     * @return mixed - $this (object) or bool (true|false)
     * 
     */
    public function action ($action, $table, $where = [], $sort = "")
	{
        // Set the error to false
        $this->_error = false;
        
        // Check that there are 3 elements in the array
        if (count ($where) === 3)
		{
            // Define allowed operators
            $operators = ["=", "!=", ">", "<", ">=", "<="];
            
            // Get array values - $where[]
            $field =  $where[0];
            $operator =  $where[1];
            $value =  $where[2];
            
            // See if the specified operator is in the allowed list
            if (in_array ($operator, $operators))
			{
                // Set the SQL statemnt with optional sort string
                $stmt = "{$action} FROM {$table} WHERE {$field} {$operator} ?{$sort};";
                
                // If the query did not executed
                if ($this->query ($stmt, array ($value)))
				{
                    // Return result set
					return ["count" => $this->_count, "results" => $this->_results];
                }
				else
				{
					// Set error
					$this->_error = true;
				}
            }
        }
        
        // Return false
        return false;
    }
    
    /**
     *
     * Get method
     *
     * Selects data from a MySQL table
     *
     * @param string $table - table name
     * @param array $where - the WHERE clause
     * @param string  $sort - optional sort string, e.g. ORDER BY column_name ASC
     *
     * @return mixed - selected info
     * 
     */
    public function get ($table, $where, $sort = "")
	{
        // Return result set
        return $this->action ("SELECT * ", $table, $where, $sort);
    }
    
    /**
     *
     * Insert method
     *
     * Inserts data into a MySQL table
     *
     * @param string $table - table name
     * @param array $where - the WHERE clause
     *
     * @return bool - true|false
     * 
     */
    public function insert ($table, $fields = [])
	{
		// Set the error to false
        $this->_error = false;
        
        // Determine the field keys
        $keys = array_keys ($fields);
        $values = "";
        $counter = 1;
        
        // Loop through the fields specified
        foreach ($fields as $field)
		{    
            // Bind values
            $values .= "?";
            
            // If at the end of values, don't add a comma
            if ($counter < count ($fields))
			{
                $values .= ", ";
            }
            
            // Increment loop
            $counter++;
        }
        
        // Set query statemnt
        $stmt = "INSERT INTO  {$table} (`" . implode ("`, `", $keys) ."`) VALUES ({$values});";
        
        // Check if query ran successfully
        if ($this->query ($stmt, $fields))
		{
            // Row inserted
			return true;
        }
		else
		{
			// Set error
			$this->_error = true;
		}
        
        // Return fail
        return false;
    }
    
    /**
     *
     * Delete method
     *
     * Deletes data from a MySQL table
     *
     * @param string $table - table name
     * @param array $where - the WHERE clause
     *
     * @return mixed - deletes the record
     * 
     */
    public function delete ($table, $where = [])
	{
        // Remove this record
        return $this->action ("DELETE", $table, $where);
    }
    
    /**
     *
     * Update method
     *
     * Updates data in a MySQL table
     *
     * @param string $table - table name
     * @param array $fields - Fields to update
     * @param string $matchField - field(s) to match
     * @param string $id - user ID (GUID)
     *
     * @return mixed - Updates the record
     * 
     */
    public function update ($table, $fields, $matchField, $id)
	{
        // Set to null
        $set = "";
        $counter = 1;
        
        // Determine columns to update
        foreach ($fields as $name => $value)
		{
            // Bind the column
            $set .= "`{$name}` = ?";
            
            // If at the end of columns, don't add a comma
            if ($counter < count ($fields))
			{
                $set .= ", ";
            }
            
            // Increment loop
            $counter++;
        }
        
        // Formulate the query statement
        $stmt = "UPDATE {$table} SET {$set} WHERE {$matchField} = '{$id}';";
        
        // Check if query ran successfully
        if ($this->query ($stmt, $fields))
		{
            // Update success
            return true;
            
        }
        
        // Return fail
        return false;
    }
    
    /**
     *
     * Count method
     *
     * Gets the records affected
     *
     * @param null
     * 
     * @return int - the total rows affected
     * 
     */
    public function count ()
	{
        // Set count
        return $this->_count;
    }
    
    /**
     *
     * Results method
     *
     * Sets the query status
     *
     * @param null
     * 
     * @return bool - true|false
     * 
     */
    public function results ()
	{    
        // Return status
        return $this->_results;
    }
    
    /**
     *
     * Error method
     *
     * Gets the error object
     *
     * @param null
     *
     * @return bool - true|false
     * 
     */
    public function error ()
	{
        // Set error and writes to PHP logs
        return error_log ($this->_error);
    }
}