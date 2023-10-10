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

    // get gameweek id
    $game->gameweek_id = isset($_GET['gameweek_id']) ? $_GET['gameweek_id'] : die();

    echo $game->getMensGameweekFixtures();

    

?>