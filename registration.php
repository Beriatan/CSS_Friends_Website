<?php

if(isset($_POST["register"])) {
    require_once('Models/UserDataSet.php');
    $firstName = filter_var($_POST["firstName"]);
    $lastName = filter_var($_POST["lastName"]);
    $email = filter_var($_POST["email"]);
    $phoneNumber = filter_var($_POST["phoneNo"]);
    $username = filter_var($_POST["uname"]);
    $password = filter_var($_POST["pwd"]);
    $passwordRetype = filter_var($_POST["pwdR"]);
    $photo = $_POST["photo"];
    $userDataSet = new UserDataSet();
    $userDataSet->registerUser($username,$firstName,$lastName,$email,$phoneNumber,$password, $photo);
}

require_once('Views/registration.phtml');

