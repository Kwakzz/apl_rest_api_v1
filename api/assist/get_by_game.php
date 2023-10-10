<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/assist.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new goal object
    $assist = new Assist($db);

    // get game id
    $assist->game_id = isset($_GET['game_id']) ? $_GET['game_id'] : die();
    

    echo $assist->getAssistsFromGame();

    

?>