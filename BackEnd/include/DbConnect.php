<?php

/**
 * Handling database connection
 * 
 * @author Átyin Ákos
 */

class DbConnect {
    
    private $conn;
    
    public function __construct() {}
    
    /**
     * Establishing database connnection
     * 
     * @return database connection handler
     */
    
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';
        
        // Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        $this->conn->set_charset("utf8");
        
        // Check for database connection error
        if(mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        
        // Returning connection resource
        return $this->conn;
    }
}

?>