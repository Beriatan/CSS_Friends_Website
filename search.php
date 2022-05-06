<?php
include_once('Views/friendListElement.phtml');
//Display all searched elements -- will suppress some information if user is not logged-in

    if(isset($_GET['searchField'])){
        $userDataSet = new UserDataSet();
        $foundUsers = [];
        if (str_contains($_GET['searchField'], ':')) {
            $word = $_GET['searchField'];
           $words = explode(':', $word);
           $foundUsers = $userDataSet->search($words[1], $words[0], 10);
        }else{
            $foundUsers = $userDataSet->search($_GET['searchField'],null,10);
        }


        echo '<div class="card-group">';
//        echo "Received results: " . count($foundUsers);
        if(isset($_SESSION["login"])) {
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
        echo '</div>';
    }

