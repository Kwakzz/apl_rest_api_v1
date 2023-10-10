<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/goal.php';

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

    // create a new goal object
    $goal = new goal($db);

    // get parameters from request body
    $goal->player_id = $requestBody->player_id;
    $goal->game_id = $requestBody->game_id;
    $goal->team_id = $requestBody->team_id;
    $goal->minute_scored = $requestBody->minute_scored;

    // if assist provider is set, add goal and assist
    if (isset($requestBody->assist_provider_id)) {
        $goal->assist_provider_id = $requestBody->assist_provider_id;
        echo $goal->addGoalAndAssist();
    }

    // else, add only goal
    else {
        echo $goal->addGoal();
    }
    

    
    

?>