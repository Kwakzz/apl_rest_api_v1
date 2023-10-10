<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/player.php';

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
    $player = new Player($db);

    // get parameters
    $player->fname = $requestBody->fname;
    $player->lname = $requestBody->lname;
    $player->gender = $requestBody->gender;
    $player->date_of_birth = $requestBody->date_of_birth;
    $player->height = $requestBody->height;
    $player->weight = $requestBody->weight;
    $player->year_group = $requestBody->year_group;
    $player->team_name = $requestBody->team_name;
    $player->position_name = $requestBody->position_name;
    $player->is_retired = $requestBody->is_retired;


    // edit user
    echo $player->addPlayer();
    

    
    

?>