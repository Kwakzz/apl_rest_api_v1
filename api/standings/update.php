<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/goal.php';
    require_once '../../class/standings.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: POST');

    $database = new Database();
    $db = $database->getConnection();

     // get request body
    $requestBody = file_get_contents('php://input');
    // decode request body as PHP array
    $requestBody = json_decode($requestBody);

    // create a new goal object
    $team = new Goal($db);

    // create a new standings object
    $standings = new Standings($db);

    $team->team_id = $requestBody->team_id;
    $team->season_id = $requestBody->season_id;
    $team->competition_id = $requestBody->competition_id;

    $standings->team_id = $team->team_id;
    $standings->season_id = $team->season_id;
    $standings->competition_id = $team->competition_id;

    $standings->getStandingsId();

    $points = 0;
    $goals_for = 0;
    $goals_against = 0;
    $wins = 0;
    $draws = 0;
    $losses = 0;
    $goal_difference = 0;
    $no_played = 0;

    if ($standings->competition_id != 1 && $standings->competition_id!=4) {

        $points = (json_decode($team->getTeamNumberOfGroupStageWinsInSeasonCompetition())->no_of_wins) *3 + json_decode($team->getTeamNumberOfGroupStageDrawsInSeasonCompetition())->no_of_draws;
        $goals_for = json_decode($team->getGroupStageGoalsByTeamInSeasonCompetition())->total_goals_scored;
        $goals_against = json_decode($team->getGroupStageGoalsConcededByTeamInSeasonCompetition())->total_goals_conceded;
        $wins = json_decode($team->getTeamNumberOfGroupStageWinsInSeasonCompetition())->no_of_wins;
        $draws = json_decode($team->getTeamNumberOfGroupStageDrawsInSeasonCompetition())->no_of_draws;
        $losses = json_decode($team->getTeamNumberOfGroupStageLossesInSeasonCompetition())->no_of_losses;
        $goal_difference = $goals_for - $goals_against;
        $no_played = $wins + $draws + $losses;
       
    }

    else {

         $points = (json_decode($team->getTeamNumberOfWinsInSeasonCompetition())->no_of_wins) *3 + json_decode($team->getTeamNumberOfDrawsInSeasonCompetition())->no_of_draws;
        $goals_for = json_decode($team->getGoalsByTeamInSeasonCompetition())->total_goals_scored;
        $goals_against = json_decode($team->getGoalsConcededByTeamInSeasonCompetition())->total_goals_conceded;
        $wins = json_decode($team->getTeamNumberOfWinsInSeasonCompetition())->no_of_wins;
        $draws = json_decode($team->getTeamNumberOfDrawsInSeasonCompetition())->no_of_draws;
        $losses = json_decode($team->getTeamNumberOfLossesInSeasonCompetition())->no_of_losses;
        $goal_difference = $goals_for - $goals_against;
        $no_played = $wins + $draws + $losses;
        
    }


    $standings->points = $points;
    $standings->goals_scored = $goals_for;
    $standings->goals_conceded = $goals_against;
    $standings->wins = $wins;
    $standings->draws = $draws;
    $standings->losses = $losses;
    $standings->goal_difference = $goal_difference;
    $standings->no_played = $no_played;

    echo $standings->updateStandingsTeam();
     

?>