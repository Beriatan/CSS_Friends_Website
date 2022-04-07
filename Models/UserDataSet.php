<?php

require_once('Models/UserData.php');
require_once('Models/Database.php');
require_once('Models/Friendship.php');

class UserDataSet
{
    protected $dbHandle, $dbInstance;

    public function __construct() {
        $this->dbInstance = Database::getInstance();
        $this->dbHandle = $this->dbInstance->getDbConnection();
        if(session_id() == '') session_start();
    }

    //Used to save values in the session variable, so the query is loaded once, rather than each time when data is required.
    function executeQuery(string $sqlQuery, array $values = null): bool|PDOStatement
    {
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->execute($values);
        //saving the query and values in session variables
        $_SESSION['lastQuery'] = $sqlQuery;
        if($values != null)
        {
            $_SESSION['lastValues'] = $values;
        }else {
            $_SESSION['lastValues'] = [];
        }
        return $statement;
    }

    public function fetchUsers($statement)
    {
        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[]  = new UserData($row);
        }
//        var_dump($dataSet);
        return $dataSet;
    }

    //Returns all users as an array
    public function fetchAllUsers() {
        $sqlQuery = 'SELECT * FROM user_data';
        return $this->fetchUsers($this->executeQuery($sqlQuery));
    }
    //Gets the user by ID
    public function fetchUserByAttributeAndValue($attribute, $value) {

        $sqlQuery = "SELECT * FROM user_data WHERE ".$attribute." = ?";
        return $this->fetchUsers($this->executeQuery($sqlQuery));
    }

    public function fetchUserById($userID) {
        $sqlQuery = "SELECT * FROM user_data WHERE id = :userID";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->bindParam(':userID',$userID);
        $statement->execute(); //Executes the PDO statement

        //Assigns the user fetched by ID or does nothing if user not found,
        $user = $statement->fetch();
        return new UserData($user, $this->getFriendshipList($user['id']));

    }

    //Checks whether a person with the username and password already exists - if so, returns the user
    public function authenticateCredentials($username, $password) {
        $sqlQuery = 'SELECT * 
                     FROM user_data
                     WHERE username = ? AND password_encrypted= ?';  //Prepare the query

        return $this->fetchUsers($this->executeQuery($sqlQuery, [$username, $password]));
    }
    //Adds the new user to the database - if user exists, returns to the registration page with warning
    public function registerUser($username, $first_name, $last_name, $email, $phone_number, $password)
    {
        if($this->authenticateCredentials($username, $password)!=null){
            echo 'This user already exists, select a different username';
            require_once('registration.php');
        }else {
            $photoLink = '/images/people/person4.jpg';//APPLY PHOTO - this option needs to be added
            $lat = '15';
            $long = '15';
            $sqlQuery = "INSERT INTO user_data(first_name, last_name, email,phone_number,username,password_encrypted,latitude, longitude,photo)
                         VALUES(:fname,:lname,:email,:pnumber,:uname,:pwd,:lat,:long,:photolink);";

            //Prepare and execute query to add to the user_data database
            $statement = $this->dbHandle->prepare($sqlQuery);
            $statement->bindParam(':fname', $first_name);
            $statement->bindParam(':lname', $last_name);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':pnumber', $phone_number);
            $statement->bindParam(':uname', $username);
            $statement->bindParam(':pwd', $password);
            $statement->bindParam(':lat', $lat);
            $statement->bindParam(':long', $long);
            $statement->bindParam(':photolink', $photoLink);
            $statement->execute();
        }
    }
    //Find users based on search query
    public function search($searchTerm)
    {
        $splitSearch = explode(",", $searchTerm);

        $searchedTerm = '%'.$searchTerm.'%';
        $sqlQuery = "SELECT * FROM user_data
                     WHERE first_name 
                     LIKE :query1 OR last_name LIKE :query1 OR email LIKE :query1 OR username LIKE :query1";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->bindParam(':query1',$searchedTerm);
        $statement->execute(); //Executes the PDO statement
        $this->fetchUsers($statement);
    }
    //Changes relationship status between users.


    public function updateFriendship($userID, $friendID, $relationship){

        $friendshipID = $this->getFriendshipID($userID, $friendID);
        //first check if the relationship exists at all.
        if($friendshipID)
        {
            //If it does, update the friendship status to a new value
            $sqlQuery = "UPDATE friendship_status SET relationship = ? WHERE relationshipID = ".$friendshipID.";";
            $this->executeQuery($sqlQuery,[$relationship]);
        }
        else {
            //Or add new friendship
            $sqlQuery = "INSERT INTO friendship_status(friend1, friend2, relationship) VALUES (?,?,?) ";
            $this->executeQuery($sqlQuery,[$userID, $friendID, $relationship]);
        }
        if($relationship == 0) {
            $sqlQuery = "DELETE FROM friendship_status WHERE friend1 = ? AND friend2 = ?";
            $this->executeQuery($sqlQuery,[$userID, $friendID]);
        }
    }
    //Returns a list of friends
    public function getFriendshipList($userID){
        $sqlQuery = 'SELECT *
                     FROM user_data.friendship_status
                     WHERE friend1 = :id';

        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->bindParam(':id',$userID);
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[] = new Friendship($row);
        }
        return $dataSet;

    }

    //Returns the ID of the friendship between two people.
    public function getFriendshipID($friend1, $friend2)
    {
            $sqlQUery = "SELECT * FROM friendship_status WHERE friend1 = ? AND friend2 = ?";
            $statement = $this->executeQuery($sqlQUery, [$friend1, $friend2]);
             $dataSet = [];
             while ($row = $statement->fetch()){
                $dataSet[] = new Friendship($row);
             }
             if($dataSet!=null){
                return  $dataSet[0]->getId();
             }else{
                 return 0;
            }


    }

    //Gets the information about friendship status between user and friend
    public function getFriendshipStatus($userID, $friendID){
        $sqlQuery = 'SELECT *
                     FROM user_data.friendship_status
                     WHERE friend1 = :userid AND friend2 = :friendid';

        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->bindParam(':userid',$userID);
        $statement->bindParam(':friendid',$friendID);
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[] = new Friendship($row);
        }
        if($dataSet!=null){
            return  $dataSet[0]->getRelationship();
        }else{
            return 0;
        }

    }


}