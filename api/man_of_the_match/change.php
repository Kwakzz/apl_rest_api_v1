<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/man_of_the_match.php';

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

    // create a new man of the match object object
    $man_of_the_match = new ManOfTheMatch($db);

    $man_of_the_match->game_id = $requestBody->game_id;
    $man_of_the_match->player_id = $requestBody->player_id;

    // change man of the match
    echo $man_of_the_match->changeManOfTheMatch();
    

    
    

?>