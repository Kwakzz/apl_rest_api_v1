<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/user.php';

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

    // get parameters from edit fan form and assign them to user object properties
    $user->user_id = $requestBody->user_id;
    $user->fname = $requestBody->fname;
    $user->lname = $requestBody->lname;
    $user->mobile_number = $requestBody->mobile_number;
    $user->date_of_birth = $requestBody->date_of_birth;
    $user->is_active = $requestBody->is_active;
    $user->is_admin = $requestBody->is_admin;
    $user->team_name = $requestBody->team_name;

    // edit user
    echo $user->editFan();
    

    
    

?>