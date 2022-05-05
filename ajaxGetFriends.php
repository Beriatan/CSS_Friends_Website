<?php
include('autoloader.php');
session_start();


    $userDataSet = new UserDataSet();
    $foundUsers = [];
    $foundUsers = $userDataSet->getFriendshipList($_SESSION['uid'], 3);
    $usersData = [];


    foreach ($foundUsers as $friendship) {
        $friendID = $friendship->getFriend2();
        array_push($usersData, $userDataSet->fetchUserById($friendID));
    }

echo json_encode($usersData);