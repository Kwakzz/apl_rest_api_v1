<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/coach.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // content type
    header('Content-Type: application/json');

    $database = new Database();
    $db = $database->getConnection();

    // create a new coach object
    $coach = new Coach($db);

    // get coach id
    $coach->coach_id = isset($_GET['coach_id']) ? $_GET['coach_id'] : die();

    echo $coach->getCoach();

    

?>