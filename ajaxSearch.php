<?php
include 'autoloader.php';


$userDataSet = new UserDataSet();
if(isset($_REQUEST["q"])){
    $userData = $userDataSet->search($_REQUEST["q"],null,10);
    echo json_encode($userData);
}
if(isset($_REQUEST["name"])){
    $suggestedNames = $userDataSet->search($_REQUEST["name"],'first_name', 6);
    echo json_encode($suggestedNames);
}



