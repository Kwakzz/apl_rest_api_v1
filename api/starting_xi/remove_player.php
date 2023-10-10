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

    // get parameters from request body
    $starting_xi->xi_id = $requestBody->xi_id;
    $starting_xi->player_id = $requestBody->player_id;

    // remove player from starting xi
    echo $starting_xi->removePlayerFromStartingXI();
    

    
    

?>