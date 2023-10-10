<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/standings.php';

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

    // create a new standings object
    $standings = new Standings($db);

    // get parameters 
    $standings->standings_name = $requestBody->standings_name;
    $standings->season_id = $requestBody->season_id;
    $standings->competition_id = $requestBody->competition_id;

    // add team
    echo $standings->createStandings();


    
    

?>