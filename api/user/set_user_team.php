<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/user.php';
    require_once '../../config/email_auth.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow only POST REQUESTS
    header('Access-Control-Allow-Methods: POST');

    // content type
    header('Content-Type: application/json');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // get request body
    $requestBody = file_get_contents('php://input');
    // decode request body as PHP array
    $requestBody = json_decode($requestBody);

    // create a new user object
    $user = new User($db);

    // get email address and team name from request body
    $user->email_address = $requestBody->email_address;
    $user->team_name = $requestBody->team_name;
    

    // set user's team
    echo $user->setUserTeamId();

    

    
    

?>