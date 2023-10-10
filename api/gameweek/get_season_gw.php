<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/gameweek.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // content type
    header('Content-Type: application/json');

    $database = new Database();
    $db = $database->getConnection();

    // create a new gameweek object
    $gameweek = new Gameweek($db);

    // get season id
    $gameweek->season_id = isset($_GET['season_id']) ? $_GET['season_id'] : die();

    echo $gameweek->getSeasonGameweeks();

    

?>