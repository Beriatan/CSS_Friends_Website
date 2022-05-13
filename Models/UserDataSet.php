<?php

/**
 * Class that contains a set of method used to retrieve the data from the database
 */
class UserDataSet
{
    protected $dbHandle, $dbInstance;

    public function __construct()
    {
        $this->dbInstance = Database::getInstance();
        $this->dbHandle = $this->dbInstance->getDbConnection();
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
        if ($values != null) {
            $_SESSION['lastValues'] = $values;
        } else {
            $_SESSION['lastValues'] = [];
        }
        return $statement;
    }

    public function fetchUsers($statement)
    {
        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new UserData($row, $this->getFriendshipList($row['id'], 4));
        }
        return $dataSet;
    }

    //Returns a list of all users
    public function fetchAllUsers() {
        $sqlQuery = 'SELECT * FROM user_data';
        return $this->fetchUsers($this->executeQuery($sqlQuery));
    }

    //Returns all users as an array, limited by page number and number of users
    public function fetchAllUsersPerPage($page, $usersPerPage)
    {
        $pageFirstResult = ($page - 1) * $usersPerPage;
        $sqlQuery = 'SELECT * FROM user_data LIMIT ' . $pageFirstResult . ',' . $usersPerPage;
        return $this->fetchUsers($this->executeQuery($sqlQuery));
    }

    //Returns the number of all users in the database.
    // Extracted only value from the column COUNT(*) to mitigate returned array form
    public function countUsers()
    {
        $sqlQuery = 'SELECT COUNT(*) FROM user_data';
        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->execute();
        $result = $statement->fetch();
        return $result['COUNT(*)'];
    }

    //Returns the user with its corresponding ID in the database
    public function fetchUserById($userID)
    {
        $sqlQuery = "SELECT * FROM user_data WHERE id = :userID";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->bindParam(':userID', $userID);
        $statement->execute(); //Executes the PDO statement

        //Assigns the user fetched by ID or does nothing if user not found,
        $user = $statement->fetch();
        return new UserData($user, $this->getFriendshipList($user['id'], 4));

    }

    //Checks whether a person with the username and password already exists - if so, returns the user
    //It also checks whether correct password has been entered
    public function authenticateCredentials($username, $password)
    {
        //Extract the data of requested username.
        $sqlQuery = "SELECT id,password_hash 
                     FROM user_data
                     WHERE username = :uname";  //Prepare the query
        //preparing the PDO statement
        $statement = $this->dbHandle->prepare($sqlQuery);
        //executing query
        $statement->bindParam(':uname', $username, PDO::PARAM_STR);
        $statement->execute();
        $hashed_pass = $statement->fetch();
        if ($hashed_pass != null) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $dataSet = [];
            if (password_verify($password, $hashed_pass['password_hash'][0])) {
                array_push($dataSet, $this->fetchUserById($hashed_pass['id'][0]));
                return $dataSet;
            }
        }
    }


    //Adds the new user to the database - if user exists, returns to the registration page with warning
    public function registerUser($username, $first_name, $last_name, $email, $phone_number, $password, $photo)
    {
        if ($this->authenticateCredentials($username, $password) != null) {
            echo 'This user already exists, select a different username';
            require_once('registration.php');
        } else {
            $photoLink = '/images/people/'.$photo;//APPLY PHOTO - this option needs to be added
            $lat = '15';
            $long = '15';
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $sqlQuery = "INSERT INTO user_data(first_name, last_name, email,phone_number,username,latitude, longitude,photo, password_hash)
                         VALUES(:fname,:lname,:email,:pnumber,:uname,:lat,:long,:photolink,:hashedPass);";

            //Prepare and execute query to add to the user_data database
            $statement = $this->dbHandle->prepare($sqlQuery);
            $statement->bindParam(':fname', $first_name);
            $statement->bindParam(':lname', $last_name);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':pnumber', $phone_number);
            $statement->bindParam(':uname', $username);
            $statement->bindParam(':lat', $lat);
            $statement->bindParam(':long', $long);
            $statement->bindParam(':photolink', $photoLink);
            $statement->bindParam(':hashedPass', $hashedPass);
            $statement->execute();
            echo 'registered successfuly';
        }
    }

    /**
     * Checks the database and returns any users, which contain the input text.
     * @param $searchTerm - input text
     * @param $attribute - specific attribute (column in the database) you want to search
     * @param $limit - limit the number of queries received
     * @return array user data set
     */
    public function search($searchTerm,$attribute = null,  $limit = null)
    {
        $searchedTerm = '%' . $searchTerm . '%';
        //Search by attribute and value if attribute given
        if($attribute!=null){
            $sqlQuery = "SELECT * FROM user_data
                     WHERE :attribute 
                     LIKE :query1";
        }else{
            $sqlQuery = "SELECT DISTINCT * FROM user_data
                     WHERE first_name 
                     LIKE :query1 OR last_name LIKE :query1 OR email LIKE :query1 OR username LIKE :query1";
        }
        //Set the limit if given
        if($limit !=null) $sqlQuery .= " LIMIT :limit";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement

        //Set attribute and limit parameters if given
        if($attribute!=null) $statement->bindParam(':attribute', $attribute);
        if($limit !=null) $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':query1', $searchedTerm);
        $statement->execute(); //Executes the PDO statement
        return $this->fetchUsers($statement);
    }

    //Updates user's lattitude and longitude
    public function updateLocation($lat, $long, $userID){
        $sqlQuery = "UPDATE user_data SET latitude = :lat AND longitude = :long WHERE id = :id;";
        $statement = $this->dbHandle->prepare($sqlQuery); //Prepares the PDO statement
        $statement->bindParam(':lat', $lat);
        $statement->bindParam(':long', $long);
        $statement->bindParam(':id', $userID);
        $statement->execute(); //Executes the PDO statement
    }

    //Changes relationship status between users.
    public function updateFriendship($userID, $friendID, $relationship)
    {

        $friendshipID = $this->getFriendshipID($userID, $friendID);
        //first check if the relationship exists at all.
        if ($friendshipID) {
            //If it does, update the friendship status to a new value
            $sqlQuery = "UPDATE friendship_status SET relationship = ? WHERE relationshipID = " . $friendshipID . ";";
            $this->executeQuery($sqlQuery, [$relationship]);
        } else {
            //Or add new friendship
            $sqlQuery = "INSERT INTO friendship_status(friend1, friend2, relationship) VALUES (?,?,?) ";
            $this->executeQuery($sqlQuery, [$userID, $friendID, $relationship]);
        }
        if ($relationship == 0) {
            $sqlQuery = "DELETE FROM friendship_status WHERE friend1 = ? AND friend2 = ?";
            $this->executeQuery($sqlQuery, [$userID, $friendID]);
        }
    }


    //Returns a list of friends
    //Status 0 - no friends, 1 -pending, 2-accept/deny, 3-friends, 4-all relationships
    public function getFriendshipList($userID, $relationshipStatus)
    {
        if ($relationshipStatus == 4) {
            $sqlQuery = 'SELECT DISTINCT *
                     FROM user_data.friendship_status
                     WHERE friend1 = :id';
        } else {
            $sqlQuery = 'SELECT DISTINCT *
                     FROM user_data.friendship_status
                     WHERE friend1 = :id AND relationship = :relStatus';
        }
        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->bindParam(':id', $userID);
        if ($relationshipStatus < 4) {
            $statement->bindParam(':relStatus', $relationshipStatus);
        }
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()) {
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
        while ($row = $statement->fetch()) {
            $dataSet[] = new Friendship($row);
        }
        if ($dataSet != null) {
            return $dataSet[0]->getId();
        } else {
            return 0;
        }
    }

    //Gets the information about friendship status between user and friend
    public function getFriendshipStatus($userID, $friendID)
    {
        $sqlQuery = 'SELECT *
                     FROM user_data.friendship_status
                     WHERE friend1 = :userid AND friend2 = :friendid';

        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->bindParam(':userid', $userID);
        $statement->bindParam(':friendid', $friendID);
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new Friendship($row);
        }
        if ($dataSet != null) {
            return $dataSet[0]->getRelationship();
        } else {
            return 0;
        }
    }

    //Rehashes the user password
    public function rehashPassword($userID, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sqlQuery = 'UPDATE user_data SET password_hash = :phash WHERE id = :id';
        $statement = $this->dbHandle->prepare($sqlQuery);
        $statement->bindParam(':phash', $hash);
        $statement->bindParam(':id', $userID);
        $statement->execute();
    }




}