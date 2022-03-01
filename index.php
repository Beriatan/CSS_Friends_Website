<?php
require_once('Models/UserDataSet.php');
$view = new stdClass();
$view->pageTitle = 'Homepage';

require_once('Views/friendListElement.phtml');
require_once('Views/loginForm.phtml');

require_once('Models/UserDataSet.php');
require_once ('Models/UserData.php');


require_once('login.php');
require_once('Views/index.phtml');











