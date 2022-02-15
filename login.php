<?php
require_once('Models/UserDataSet.php');
require_once('Models/UserData.php');
session_start();


if (isset($_POST["login"])) {
    $username = filter_var($_POST["loginInput"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["passwordInput"], FILTER_SANITIZE_STRING);
    $userDataSet = new UserDataSet();
    $isLoggedIn = $userDataSet->authenticateCredentials($username, $password);



    if (! $isLoggedIn) {
        echo "Incorrect username or password";
    }
    else {
        echo "logged in successfuly";
        $_SESSION["login"] = $username;
        $_SESSION["uid"] = $isLoggedIn[0]->getId();
    }
}
if(isset($_POST["logout"]))
{
    unset($_COOKIE['searchedTerm']);//delete the cookie after displaying the list
    setcookie('searchedTerm', '', time() - 3600);
    echo "Logout user";
    unset($_SESSION["login"]);
    $userDataSet = null;
    session_destroy();
}

