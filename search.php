<?php
//Passes the searched term to the list generator
if (isset($_POST["search"])) {
$cookieValue = filter_var($_POST["searchField"], FILTER_SANITIZE_STRING);
$cookieName = "searchedTerm";
setcookie($cookieName, $cookieValue);

}
require_once('index.php');