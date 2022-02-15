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
    }


    //Returns all users as an array
    public function fetchAllUsers() {
        $sqlQuery = 'SELECT * FROM user_data';
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->execute(); //Executes the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[]  = new UserData($row,$this->getFriendshipList($row['id']));
        }
        return $dataSet;
    }
    //Gets the user by ID
    public function fetchUserById($userId) {

        $sqlQuery = "SELECT * FROM user_data WHERE id = :id";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement

        $statement->bindParam(':id',$userId);//Assign the parameter(userID) to requested query
        $statement->execute(); //Executes the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[]  = new UserData($row,$this->getFriendshipList($row['id']));
        }
        return $dataSet;
    }

    //Checks whether a person with the username and password already exists - if so, returns the user
    public function authenticateCredentials($username, $password) {
        $sqlQuery = 'SELECT * 
                     FROM user_data
                     WHERE username = :uname AND password_encrypted= :pwd';  //Prepare the query
        $statement = $this->dbHandle->prepare($sqlQuery); //Get the query in and wait for rest of parameters
        $statement->bindParam(':uname',$username); //First parameter
        $statement->bindParam(':pwd',$password); //Second parameter
        $statement->execute(); //Execute the statement
        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[]  = new UserData($row,$this->getFriendshipList($row['id']));
        }
        return $dataSet;
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
    public function fetchUsersBySearchedTerm($term)
    {
        $searchedTerm = '%'.$term.'%';
        $sqlQuery = "SELECT * FROM user_data
                     WHERE first_name 
                     LIKE :query1 OR last_name LIKE :query1 OR email LIKE :query1 OR username LIKE :query1";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->bindParam(':query1',$searchedTerm);
        $statement->execute(); //Executes the PDO statement

        $dataSet = [];
        while ($row = $statement->fetch()){
            $dataSet[]  = new UserData($row,$this->getFriendshipList($row['id']));
        }
        return $dataSet;
    }
    //Changes relationship status between users.
    public function updateFriendship($userID, $friendID, $value){
        //Values - 1- pending, 2-accept/deny - 3 -friends
        $sqlQuery = "REPLACE INTO friendship_status(friend1,friend2,relationship) VALUES (:friend1,:friend2,:relationship)";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->bindParam(':friend1',$userID);
        $statement->bindParam(':friend2',$friendID);
        $statement->bindParam(':relationship',$value);
        $statement->execute(); //Executes the PDO statement
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