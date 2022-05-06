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
require_once('search.php');
if(isset($_GET['register'])){
    require_once('registration.php');
}
//$userDataSet = new UserDataSet();
////$userDataSet->rehashPassword(500, 'michal');
//$userDataSet->encryptAllPasswords();
$userDataSet = new UserDataSet();

//$names = $userDataSet->fetchAllUsers();
//file_put_contents('names.json', json_encode($names));


if(isset($_GET['listFriends']))
{
    require_once('listFriends.php');

}

//Change friendship status
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
if(isset($_GET['map'])){
    require_once('Views/map.phtml');
}
if(isset($_GET['searchField'])){
    require_once('search.php');
}
if(isset($_GET['lat'])){
    $userDataSet->updateLocation($_GET['lat'], $_GET['lon'], $_SESSION['uid']);
    echo 'chicken';
}

if(isset($_GET['showAll'])){
    require_once('Views/friendListElement.phtml');


    $usersPerPage = $userDataSet->countUsers();
    $resultsPerPage = 10;
    $view->numberOfPages = ceil($usersPerPage / $resultsPerPage);

    if(!isset($_GET['page'])){
        $page = 1;
    }else {
        $page = $_GET['page'];
    }

    $users = $userDataSet->fetchAllUsersPerPage($page, $resultsPerPage );
    echo '<div class="card-group">';

    //Display detailed data if the user is logged in, otherwise - display basic data
    if(!isset($_SESSION["login"])){
        foreach($users as $user)
        {
            listPublicUserDetails($user);
        }} else {
        foreach($users as $user)
        {
            listUserDetails($user);
        }
    }
    echo '</div>';
    require_once('Views/pagination.phtml');
}


////if the main search box has been used, use the search method to get the data
//if(isset($_POST['mainSearch']) && trim($_POST['mainSearch']) != ""){
//    $view->items = $dataSet->search($_POST['mainSearch']);
//}
//List all friends
require('Views/template/footer.phtml');













