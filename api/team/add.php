<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/team.php';
    require_once '../../config/cloudinary.php';

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

    // create a new team object
    $team = new Team($db);


    // get parameters 
    $team->team_name = $requestBody->team_name;
    $team->team_name_abbrev = $requestBody->team_name_abbrev;


    // add team
    echo $team->addTeam();
    

    
    

?>