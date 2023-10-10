<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/user.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow only GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // content type
    header('Content-Type: application/json');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // create a new user object
    $user = new User($db);

    // get email_address and activation code from request body
    $user->hashed_user_id = $_GET['uid'];
    $user->activation_code = $_GET['activation_code'];

    echo $user->activateAccount();

?>