<?php
$view = new stdClass();
$view->pageTitle = 'Homepage';
if(session_id() == ""){
    session_start();
}
require('Views/template/header.phtml');
require_once ('Views/navbar.phtml');
require_once('Views/index.phtml');
require_once('Models/UserDataSet.php');


////if the main search box has been used, use the search method to get the data
//if(isset($_POST['mainSearch']) && trim($_POST['mainSearch']) != ""){
//    $view->items = $dataSet->search($_POST['mainSearch']);
//}

require_once('login.php');
require_once('Views/index.phtml');
require_once('Views/friendListElement.phtml');
require_once('Models/UserDataSet.php');
require_once('Models/UserData.php');

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
    echo '<div class="card-group">';
    if($foundUsers != null){
        foreach($usersData as $user)
        {
            listUserDetails($user);
        }
    }
    echo '</div>';
}

if(isset($_POST["registrationButton"])){
    require_once('registration.php');
}

require('Views/template/footer.phtml');













