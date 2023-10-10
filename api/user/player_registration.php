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

    // request body contains user details (fname, lname, gender, date of birth)
    // the other fields are left blank and can be updated later
    // it also contains the user's category (whether fan, player, or coach)
    $user->email_address = $requestBody->email_address;
    $user->fname = $requestBody->fname;
    $user->lname = $requestBody->lname;
    $user->gender = $requestBody->gender;
    $user->date_of_birth = $requestBody->date_of_birth;
    $user->team_name = $requestBody->team_name;

    // check if player has a profile
    if ($user->playerProfileExists()) {
        echo $user->linkUserToPlayerProfile();
    }
    else {
        echo $user->createPlayerProfile();
    }
    

    

    
    

?>