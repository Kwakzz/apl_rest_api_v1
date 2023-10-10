<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/stage.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow only GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // create a new stage object
    $stage = new Stage($db);

    // get all stages
    echo $stage->getAllStages();


?>