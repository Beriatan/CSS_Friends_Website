<?php

    $userDataSet = new UserDataSet();
    $foundUsers = [];
    $foundUsers = $userDataSet->getFriendshipList($_SESSION['uid'], 3);
    $usersData = [];

    foreach($foundUsers as $friendship)
    {
    $friendID = $friendship->getFriend2();
    array_push($usersData, $userDataSet->fetchUserById($friendID));
    }

    if($foundUsers != null){
    echo '<div class="card-group">';
        foreach($usersData as $user)
        {
            listUserDetails($user);
        }
        echo '</div>';
    }