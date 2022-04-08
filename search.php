<?php
include_once('Views/friendListElement.phtml');
//Display all searched elements -- will suppress some information if user is not logged-in

    if(isset($_POST['searchField']))
    {
        $userDataSet = new UserDataSet();
        $foundUsers = [];
        $foundUsers = $userDataSet->search($_POST['searchField']);

        echo '<div class="card-group">';
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