<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/transfer.php';
    require_once '../../class/player.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow only POST REQUESTS
    header('Access-Control-Allow-Methods: POST');

    // content type
    header('Content-Type: application/json');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // get request body
    $requestBody = file_get_contents('php://input');
    // decode request body as PHP array
    $requestBody = json_decode($requestBody);

    // create a new transfer object
    $transfer = new Transfer($db);


    // get parameters 
    $transfer->transferred_player_id = $requestBody->transferred_player_id;
    $transfer->prev_team_id = $requestBody->prev_team_id;
    $transfer->new_team_id = $requestBody->new_team_id;
    $transfer->transfer_date = $requestBody->transfer_date;
    $transfer->transfer_type = $requestBody->transfer_type;

    // create a new player object
    $player = new Player($db);

    $player->player_id = $requestBody->transferred_player_id;
    $player->team_id = $requestBody->new_team_id;

    // update player team
    $player->changeTeam();

    // add transfer    
    echo $transfer->addTransfer();



?>