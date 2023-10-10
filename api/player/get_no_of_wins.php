<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/player.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new player object
    $player = new Player($db);

    // get player id
    $player->player_id = isset($_GET['player_id']) ? $_GET['player_id'] : die();
    

    echo $player->getPlayerTotalNumberOfWins();

    

?>