<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/team.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow only GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // create a new team object
    $team = new Team($db);

    // get all teams
    echo $team->getMensTeams();


?>