<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/player.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // content type
    header('Content-Type: application/json');

    $database = new Database();
    $db = $database->getConnection();

    // create a new player object
    $player = new Player($db);

    echo $player->getMalePlayersWithUserAccounts();

    

?>