<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/user.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new user object
    $user = new User($db);

    // get fname and lname
    $user->fname = isset($_GET['fname']) ? $_GET['fname'] : die();
    $user->lname = isset($_GET['lname']) ? $_GET['lname'] : die();

    echo $user->getCoachesByName();

    

?>