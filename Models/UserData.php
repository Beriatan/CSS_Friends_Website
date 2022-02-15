<?php

require_once('Models/Database.php');


class UserData
{

    protected $id, $first_name, $last_name, $email, $phone_number, $username, $password, $latitude, $longitude, $photo;
    protected $friendsList;


    public function __construct($db_row, $friendsList)
    {
        $friendsList = [];
        $this->id = $db_row['id'];
        $this->first_name = $db_row['first_name'];
        $this->last_name = $db_row['last_name'];
        $this->email = $db_row['email'];
        $this->phone_number = $db_row['phone_number'];
        $this->username = $db_row['username'];
        $this->password = $db_row['password_encrypted'];
        $this->latitude = $db_row['latitude'];
        $this->longitude = $db_row['longitude'];
        $this->photo = $db_row['photo'];
        $this->friendslist = $friendsList;
    }
    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName(): mixed
    {
        return $this->first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName(): mixed
    {
        return $this->last_name;
    }

    /**
     * @return mixed
     */
    public function getEmail(): mixed
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber(): mixed
    {
        return $this->phone_number;
    }

    /**
     * @return mixed
     */
    public function getUsername(): mixed
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword(): mixed
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getLatitude(): mixed
    {
        return $this->latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude(): mixed
    {
        return $this->longitude;
    }

    /**
     * @return mixed
     */
    public function getPhoto(): mixed
    {
        return $this->photo;
    }

    /**
     * @return mixed
     */
    public function getFriendsList(): mixed
    {
        return $this->friendsList;
    }



}