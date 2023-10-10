<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/season.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new season object
    $season = new Season($db);

    // get season id
    $season->season_id = isset($_GET['season_id']) ? $_GET['season_id'] : die();

    echo $season->getWomensSeasonCompetitions();

    

?>