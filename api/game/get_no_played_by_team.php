<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/game.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new game object
    $game = new Game($db);

    // get team id
    $game->team_id = isset($_GET['team_id']) ? $_GET['team_id'] : die();
    

    echo $game->getNoOfGamesPlayedByTeam();

    

?>