<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/player_position.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow only GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // create a new player positiion object
    $player_position = new PlayerPosition($db);

    // get all positions
    echo $player_position->getAllPositions();


?>