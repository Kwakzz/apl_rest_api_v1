<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/gameweek.php';

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

    // create a new gameweek object
    $gameweek = new Gameweek($db);

    // get parameters from edit gameweek form and assign them to gameweek object properties
    $gameweek->gameweek_number = $requestBody->gameweek_number;
    $gameweek->gameweek_date = $requestBody->gameweek_date;
    $gameweek->season_id = $requestBody->season_id;
    $gameweek->gameweek_id = $requestBody->gameweek_id;


    // add season
    echo $gameweek->editGameweek();
    

    
    

?>