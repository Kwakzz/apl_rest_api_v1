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

    // create a new assist object
    $assist = new Assist($db);

    // get competition and season id
    $assist->season_id = isset($_GET['season_id']) ? $_GET['season_id'] : die();
    $assist->competition_id = isset($_GET['competition_id']) ? $_GET['competition_id'] : die();
    
    
    echo $assist->getTop10AssistProvidersBySeasonAndCompetition();

    

?>