<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/season.php';

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

    // create a new season object
    $season = new Season($db);

    // get parameters from add season form and assign them to season object properties
    $season->season_name = $requestBody->season_name;
    $season->start_date = $requestBody->start_date;
    $season->end_date = $requestBody->end_date;

    // add season
    echo $season->addSeason();
    

    
    

?>