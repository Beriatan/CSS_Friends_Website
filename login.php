<?php
//include 'autoloader.php';
//require_once('Models/UserDataSet.php');
//require_once('Models/UserData.php');
require_once('Views/loginForm.phtml');


if (isset($_POST["login"])) {
    $username = filter_var($_POST["loginInput"]);
    $password = filter_var($_POST["passwordInput"]);
    $userDataSet = new UserDataSet();
    $isLoggedIn = $userDataSet->authenticateCredentials($username, $password);


    if ($isLoggedIn == null) {
       // echo "Incorrect username or password";
        ?><script>window.alert("Incorrect username or password");
        </script><?php
    }
    else {
        //echo "logged in successfuly";

        $_SESSION["login"] = $username;
        $_SESSION["uid"] = $isLoggedIn[0]->getId();
        ?><script></script><?php

    }
}
if(isset($_POST["logout"]))
{
    //echo "Logout user";
    unset($_SESSION["login"]);
    $userDataSet = null;
    session_destroy();
}




