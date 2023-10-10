<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/game.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // content type
    header('Content-Type: application/json');

    $database = new Database();
    $db = $database->getConnection();

    // create a new game object
    $game = new Game($db);

    // get season and competition ids
    $game->season_id = isset($_GET['season_id']) ? $_GET['season_id'] : die();
    $game->competition_id = isset($_GET['competition_id']) ? $_GET['competition_id'] : die();

    echo $game->getSeasonCupGames();

    

?>