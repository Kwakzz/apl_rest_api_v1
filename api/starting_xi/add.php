<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/starting_xi.php';

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

    // create a new starting XI object
    $starting_xi = new StartingXI($db);

    // get game_id and team_id from request body
    $starting_xi->game_id = $requestBody->game_id;
    $starting_xi->team_id = $requestBody->team_id;


    // add starting xi
    echo $starting_xi->addStartingXI();
    

    
    

?>