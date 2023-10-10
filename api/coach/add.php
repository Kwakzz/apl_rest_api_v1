<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/coach.php';

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

    // create a new coach object
    $coach = new Coach($db);

    // get parameters from edit coach form and assign them to coach object properties
    $coach->fname = $requestBody->fname;
    $coach->lname = $requestBody->lname;
    $coach->gender = $requestBody->gender;
    $coach->date_of_birth = $requestBody->date_of_birth;
    $coach->year_group = $requestBody->year_group;
    $coach->team_name = $requestBody->team_name;
    $coach->is_retired = $requestBody->is_retired;


    // edit user
    echo $coach->addCoach();
    

    
    

?>