<?php
include 'autoloader.php';
$view = new stdClass();
$view->pageTitle = 'Homepage';
if(session_id() == ""){
    session_start();
}

require_once('Views/template/header.phtml');
require_once('Views/navbar.phtml');
require_once('Views/index.phtml');
require_once('login.php');
require_once('Views/index.phtml');
require_once('Views/friendListElement.phtml');


if(isset($_POST['listFriends']))
{

    $userDataSet = new UserDataSet();
    $foundUsers = [];
    $foundUsers = $userDataSet->getFriendshipList($_SESSION['uid']);
    $usersData = [];

    foreach($foundUsers as $friendship)
    {
        $friendID = $friendship->getFriend2();
        array_push($usersData, $userDataSet->fetchUserById($friendID));
    }
    if($foundUsers != null){
        foreach($usersData as $user)
        {
            listUserDetails($user);
        }
    }
}

if (isset($_POST["changeFriend"])) {

    $userID = filter_var($_POST["userID"]);
    $friendID = filter_var($_POST["friendID"]);
    $status = filter_var($_POST["changeFriend"]);

    //Controller for the method Update Friendship in User Data Set - changes friendship status or adds a new one
    $dataset = new UserDataSet();
    //Status 0 - no friends, 1 -pending, 2-accept/deny, 3-friends
    if ($status == "invite") {
        $dataset->updateFriendship($userID, $friendID, 1);
        $dataset->updateFriendship($friendID, $userID, 2);
    } else if ($status == "accept") {
        $dataset->updateFriendship($userID, $friendID, 3);
        $dataset->updateFriendship($friendID, $userID, 3);
    } else if ($status == "deny" || $status == "remove") {
        $dataset->updateFriendship($userID, $friendID, 0);
        $dataset->updateFriendship($friendID, $userID, 0);
    }
}

if(isset($_POST["registrationButton"])){
    require_once('registration.php');
}


////if the main search box has been used, use the search method to get the data
//if(isset($_POST['mainSearch']) && trim($_POST['mainSearch']) != ""){
//    $view->items = $dataSet->search($_POST['mainSearch']);
//}
//List all friends
require('Views/template/footer.phtml');













