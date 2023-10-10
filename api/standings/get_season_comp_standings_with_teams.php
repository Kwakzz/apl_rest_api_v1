<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/standings.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new standings object
    $standings = new Standings($db);

    $standings->season_id = isset($_GET['season_id']) ? $_GET['season_id'] : die();
    $standings->competition_id = isset($_GET['competition_id']) ? $_GET['competition_id'] : die();
    

    echo $standings->getSeasonCompStandingsWithTeams();

    

?>