<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/starting_xi.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a new starting xi object
    $starting_xi = new StartingXI($db);

    // get game id
    $starting_xi->xi_id = isset($_GET['xi_id']) ? $_GET['xi_id'] : die();
    

    echo $starting_xi->getTeamStartingXIPlayers();

    

?>