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

    // get competition id and season id from "add competition to season" form
    $season->competition_name = $requestBody->competition_name;
    $season->season_id = $requestBody->season_id;
    $season->gender = $requestBody->gender;

    // add season
    echo $season->addSeasonCompetition();
    

    
    

?>