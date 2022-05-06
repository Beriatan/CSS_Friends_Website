<?php

class Database {
    protected static $dbInstance = null;  //static instance
    protected $dbHandle;

    /**
     * Creates a connection with the database and returns it if succesful
     * @return instance of the database
     */
    public static function getInstance()
    {
        $username = 'aee420';
        $host = 'poseidon.salford.ac.uk';
        $password = 'NBp7b3mlbwuZn5S';
        $dbName = 'user_data';

        //Check if PDO exists
        if(self::$dbInstance == null) {
            //create new single instance, if not, sending in connection info
            self::$dbInstance = new self($username,$password,$host,$dbName);
        }
        return self::$dbInstance;
    }

    private function __construct($username, $password, $host, $database)
    {
        try {
            $this->dbHandle = new PDO('mysql:host=poseidon.salford.ac.uk;dbname=user_data',$username,$password);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    //Returns the PDO handle to be used elsewhere
    public function getDbConnection(){
        return $this->dbHandle;
    }

    //Destroys the PDO when no longer needed
    public function __destruct() {
        $this->dbHandle = null;
    }


}