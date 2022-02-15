<?php
require_once('Models/UserData.php');
require_once('changeFriendshipStatus.php');

//Displays different buttons depending on type of friendship
function displayFriendshipStatus($userID,$friendID)
{
    //Values - 0-not friends 1- pending, 2-accept/deny - 3 -friends
    $temp = new UserDataSet();
    $friendshipStatus = $temp->getFriendshipStatus($userID,$friendID);
    if($friendshipStatus == 0) echo '<form class="form-inline" action="changeFriendshipStatus.php" method="post"><button class="btn btn-success float-right" name="changeFriend" value="invite" type="submit">Invite</button><input type="hidden" id="userID" name="userID" value="'.$userID.'"><input type="hidden" id="friendID" name="friendID" value="'.$friendID.'"></form>';
    else if($friendshipStatus == 1) echo 'Invitation pending';
    else if($friendshipStatus == 2) echo '<form class="form-inline" action="changeFriendshipStatus.php" method="post"><button class="btn btn-success float-right" name="changeFriend" value="accept" type="submit">Accept</button><button class="btn btn-success float-right" name="changeFriend" value="deny" type="submit">Deny<input type="hidden" id="userID" name="userID" value="'.$userID.'"><input type="hidden" id="friendID" name="friendID" value="'.$friendID.'"></form></button>';
    else if($friendshipStatus == 3) echo '<form class="form-inline" action="changeFriendshipStatus.php" method="post"><button class="btn btn-success float-right" name="changeFriend" value="remove" type="submit">Remove Friend<input type="hidden" id="userID" name="userID" value="'.$userID.'"><input type="hidden" id="friendID" name="friendID" value="'.$friendID.'"></form></button>';
    //.displayFriendshipStatus($friendshipStatus) .
}

function listUserDetails($userData){
    $element = '

    <li class="list-group-item p-3 ">
        <div class="d-inline-block row">
            <img src="'.$userData->getPhoto().'" class="img-fluid float-left p-3" style="width: 10rem;">
            <div class="p-2 d-inline-block align-middle">'.$userData->getFirstName(). '</div>
            <div class="p-2 d-inline-block">'.$userData->getLastName().'</div>
            <div class="p-2 d-inline-block">'.$userData->getEmail().'</div>
            <div class="p-2 d-inline-block">'.$userData->getPhoneNumber().'</div>
            <div class="p-2 d-inline-block">'.$userData->getUsername().'</div>
            <div class="p-2 d-inline-block">'.$userData->getLatitude().'</div>
            <div class="p-2 d-inline-block">'.$userData->getLongitude().'</div>
        </div>
        '.displayFriendshipStatus($_SESSION["uid"],$userData->getID()).'


    </li>';

    echo $element;


}

 function listPublicUserDetails($userData){

     $element = '

    <li class="list-group-item p-3 ">
        <div class="d-inline-block row">
            <img src="'.$userData->getPhoto().'" class="img-fluid float-left p-3" style="width: 10rem;">
            <div class="p-2 d-inline-block align-middle">'.$userData->getFirstName(). '</div>
            <div class="p-2 d-inline-block">'.$userData->getLastName().'</div>
            <div class="p-2 d-inline-block">'.$userData->getEmail().'</div>
            <div class="p-2 d-inline-block">'.$userData->getUsername().'</div>
        </div>


    </li>';

     echo $element;

 }
