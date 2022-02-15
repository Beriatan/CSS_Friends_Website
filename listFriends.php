<?php
include_once('friendListElement.php');
include_once('Models/UserDataSet.php');
include_once('Models/UserData.php');

 if(isset($_POST['listFriends']))
    {
        $userDataSet = new UserDataSet();
        $foundUsers = [];

        $foundUsers = $userDataSet->getFriendshipList($_SESSION['uid']);
        $usersData = [];

        foreach($foundUsers as $friendship)
        {
            $usersData = new UserData($userDataSet->fetchUserById($friendship->getFriend2()), $userDataSet->getFriendshipList($friendship->getFriend2()));
        }
            $welcomeMessage = 'Hi, '.$_SESSION["login"].':)';
            echo $welcomeMessage;
            //Display detailed user information if logged in


        if($foundUsers != null){
            foreach($usersData as $user)
            {
                listUserDetails($user);
            }
        }


    }
