<?php
include_once('Views/friendListElement.phtml');
//Display all searched elements -- will suppress some information if user is not logged-in

function displaySearch(){

    if(isset($_COOKIE['searchedTerm']))
    {

        $userDataSet = new UserDataSet();
        $foundUsers = [];
        $foundUsers = $userDataSet->search($_COOKIE['searchedTerm']);

        if(isset($_SESSION["login"])) {
            $welcomeMessage = 'Hi, '.$_SESSION["login"].':)';
            echo $welcomeMessage;
            //Display detailed user information if logged in
            foreach($foundUsers as $user)
            {
                listUserDetails($user, 2);
            }

        }else{

            //Display general user information if not logged in
            foreach($foundUsers as $user)
            {
                listPublicUserDetails($user);
            }
        }
        unset($_COOKIE['searchedTerm']);//delete the cookie after displaying the list
        setcookie('searchedTerm', '', time() - 3600);

    }

}