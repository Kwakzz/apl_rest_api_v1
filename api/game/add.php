<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/game.php';

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

    // create a new game object
    $game = new Game($db);

    // get parameters from request body
    $game->gameweek_id = $requestBody->gameweek_id;
    $game->home_id = $requestBody->home_id;
    $game->away_id = $requestBody->away_id;
    $game->competition_id = $requestBody->competition_id;
    $game->start_time = $requestBody->start_time;


    // add game
    echo $game->addGame();
    

    
    

?>