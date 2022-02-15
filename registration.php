<?php
$view = new stdClass();
$view->pageTitle = 'Registration';

$check = false;
if(isset($_POST["register"])) {
    require_once('Models/UserDataSet.php');
    $firstName = filter_var($_POST["firstName"], FILTER_SANITIZE_STRING);
    $lastName = filter_var($_POST["lastName"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
    $phoneNumber = filter_var($_POST["phoneNo"], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST["uname"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["pwd"], FILTER_SANITIZE_STRING);
    $passwordRetype = filter_var($_POST["pwdR"], FILTER_SANITIZE_STRING);

    $userDataSet = new UserDataSet();
    $userDataSet->registerUser($username,$firstName,$lastName,$email,$phoneNumber,$password);
    $check = true;

}
if($check) require_once('index.php');
else require_once('Views/registration.phtml');