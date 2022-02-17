<?php

require_once('Models/UserDataSet.php');



if (isset($_POST["changeFriend"])) {

    $userID = filter_var($_POST["userID"]);
    $friendID = filter_var($_POST["friendID"]);
    $status = filter_var($_POST["changeFriend"]);

    //Controller for the method Update Friendship in User Data Set - changes friendship status or adds a new one
    $dataset = new UserDataSet();
    //Status 0 - no friends, 1 -pending, 2-accept/deny, 3-friends
    if($status=="invite"){
        $dataset->updateFriendship($userID,$friendID,1);
        $dataset->updateFriendship($friendID,$userID,2);
    }
    else if($status =="accept"){
        $dataset->updateFriendship($userID,$friendID,3);
        $dataset->updateFriendship($friendID,$userID,3);
    }
    else if($status == "deny"|| $status == "remove"){
        $dataset->updateFriendship($userID,$friendID,0);
        $dataset->updateFriendship($friendID,$userID,0);
    }



}

require_once('index.php');
