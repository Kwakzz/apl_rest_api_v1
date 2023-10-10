<?php

    class Standings {
    // Connection
        private $conn;
        // Table
        private $db_table = "Standings";
        // Columns
        public $standings_id;
        public $standings_name;
        public $competition_id;
        public $season_id;

        // Helper tables
        public $competition_table = "Competition";
        public $standings_team_table = "StandingsTeam";
        public $team_table = "Team";
        public $season_table = "Season";

        // StandingsTeam columns
        public $points;
        public $wins;
        public $draws;
        public $losses;
        public $goals_scored;
        public $goals_conceded;
        public $no_played;
        public $goal_difference;

        // Helper columns
        public $team_id;
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }


        // CREATE FUNCTIONS

        /**
         * This function creates a standing for a competition
         */
        public function createStandings() {

            $sqlQuery = "
                        INSERT INTO
                        ". $this->db_table ."
                        (
                            standings_name,
                            competition_id,
                            season_id
                        )
                        VALUES
                        (
                            :standings_name,
                            :competition_id,
                            :season_id
                        )";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->standings_name=htmlspecialchars(strip_tags($this->standings_name));

            // bind data
            $stmt->bindParam(":standings_name", $this->standings_name);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            if($stmt->execute()){
                http_response_code(201);
                return true;
            }

            return false;
        }

        /**
         * This function adds a team to a table
         */
        public function addTeamToTable () {

            $sqlQuery = "
                        INSERT INTO
                        ". $this->standings_team_table ."
                        (
                            standings_id,
                            team_id
                        )
                        VALUES
                        (
                            :standings_id,
                            :team_id
                        )";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":standings_id", $this->standings_id);
            $stmt->bindParam(":team_id", $this->team_id);

            if($stmt->execute()){
                http_response_code(201);
                return true;
            }

            return false;
        }


        // --- READ FUNCTIONS ---

        /**
         * This function gets the table for a competition including the teams and their stats
         */

        public function getSeasonCompStandingsWithoutTeams () {

            $sqlQuery = "
                SELECT 
                    ". $this->db_table .".standings_id,
                    ". $this->db_table .".standings_name,
                    ". $this->db_table .".competition_id,
                    ". $this->competition_table .".competition_name,
                    ". $this->competition_table .".competition_abbrev,
                    ". $this->competition_table .".gender,
                    ". $this->db_table .".season_id,
                    ". $this->season_table .".season_name
                FROM
                    ". $this->db_table ."
                JOIN ". $this->season_table ." ON ". $this->db_table .".season_id = ". $this->season_table .".season_id
                JOIN ". $this->competition_table ." ON ". $this->db_table .".competition_id = ". $this->competition_table .".competition_id
                WHERE
                    ". $this->db_table .".competition_id = :competition_id
                AND
                    ". $this->db_table .".season_id = :season_id
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            } else {
                return "";
            }

        
        }


        /**
         * This function gets the table for a competition including the teams and their stats
         */

        public function getSeasonCompStandingsWithTeams () {

            $sqlQuery = "
                SELECT 
                    ". $this->db_table .".standings_id,
                    ". $this->db_table .".standings_name,
                    ". $this->db_table .".competition_id,
                    ". $this->competition_table .".competition_name,
                    ". $this->competition_table .".competition_abbrev,
                    ". $this->competition_table .".gender,
                    ". $this->db_table .".season_id,
                    ". $this->season_table .".season_name,
                    ". $this->team_table .".team_id,
                    ". $this->team_table .".team_name,
                    ". $this->team_table .".team_name_abbrev,
                    ". $this->team_table .".team_logo_url,
                    ". $this->standings_team_table .".points,
                    ". $this->standings_team_table .".wins,
                    ". $this->standings_team_table .".draws,
                    ". $this->standings_team_table .".losses,
                    ". $this->standings_team_table .".goals_scored,
                    ". $this->standings_team_table .".goals_conceded,
                    ". $this->standings_team_table .".no_played,
                    ". $this->standings_team_table .".goal_difference
                FROM
                    ". $this->db_table ."
                JOIN ". $this->standings_team_table ." ON ". $this->db_table .".standings_id = ". $this->standings_team_table .".standings_id
                JOIN ". $this->team_table ." ON ". $this->standings_team_table .".team_id = ". $this->team_table .".team_id
                JOIN ". $this->season_table ." ON ". $this->db_table .".season_id = ". $this->season_table .".season_id
                JOIN ". $this->competition_table ." ON ". $this->db_table .".competition_id = ". $this->competition_table .".competition_id
                WHERE
                    ". $this->db_table .".competition_id = :competition_id
                AND
                    ". $this->db_table .".season_id = :season_id
                ORDER BY
                    ". $this->standings_team_table .".points DESC,
                    ". $this->standings_team_table .".goal_difference DESC,
                    ". $this->standings_team_table .".goals_scored DESC,
                    ". $this->standings_team_table .".wins DESC,
                    ". $this->standings_team_table .".draws DESC,
                    ". $this->standings_team_table .".losses DESC
                
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            } else {
                return "";
            }
        }

        /**
         * This function gets only the standings teams in a table in a season competition
         */
        public function getStandingsTeams () {

            $sqlQuery = "
                SELECT 
                    ". $this->team_table .".team_id,
                    ". $this->team_table .".team_name,
                    ". $this->team_table .".team_logo_url,
                    ". $this->standings_team_table .".points,
                    ". $this->standings_team_table .".wins,
                    ". $this->standings_team_table .".draws,
                    ". $this->standings_team_table .".losses,
                    ". $this->standings_team_table .".goals_scored,
                    ". $this->standings_team_table .".no_played,
                    ". $this->standings_team_table .".goal_difference,
                    ". $this->standings_team_table .".goals_conceded

                FROM
                    ". $this->db_table ."
                JOIN ". $this->standings_team_table ." ON ". $this->db_table .".standings_id = ". $this->standings_team_table .".standings_id
                JOIN ". $this->team_table ." ON ". $this->standings_team_table .".team_id = ". $this->team_table .".team_id
                JOIN ". $this->season_table ." ON ". $this->db_table .".season_id = ". $this->season_table .".season_id
                JOIN ". $this->competition_table ." ON ". $this->db_table .".competition_id = ". $this->competition_table .".competition_id
                WHERE
                    ". $this->db_table .".standings_id = :standings_id
                ORDER BY
                    ". $this->standings_team_table .".points DESC,
                    ". $this->standings_team_table .".goal_difference DESC,
                    ". $this->standings_team_table .".goals_scored DESC,
                    ". $this->standings_team_table .".wins DESC,
                    ". $this->standings_team_table .".draws DESC,
                    ". $this->standings_team_table .".losses DESC
                
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":standings_id", $this->standings_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            } else {
                return "";
            }
        }



        /**
         * This function gets all the standings since the start of recorded history including all the teams and their stats
         */
        public function getAllStandings () {

            $sqlQuery = "
                SELECT 
                    ". $this->db_table .".standings_id,
                    ". $this->db_table .".standings_name,
                    ". $this->db_table .".competition_id,
                    ". $this->competition_table .".competition_name,
                    ". $this->competition_table .".competition_abbrev,
                    ". $this->db_table .".season_id,
                    ". $this->season_table .".season_name,
                    ". $this->team_table .".team_id,
                    ". $this->team_table .".team_name,
                    ". $this->team_table .".team_logo_url,
                    ". $this->standings_team_table .".points,
                    ". $this->standings_team_table .".wins,
                    ". $this->standings_team_table .".draws,
                    ". $this->standings_team_table .".losses,
                    ". $this->standings_team_table .".no_played,
                    ". $this->standings_team_table .".goal_difference,
                    ". $this->standings_team_table .".goals_scored,
                    ". $this->standings_team_table .".goals_conceded
                FROM
                    ". $this->db_table ."
                JOIN ". $this->standings_team_table ." ON ". $this->db_table .".standings_id = ". $this->standings_team_table .".standings_id
                JOIN ". $this->team_table ." ON ". $this->standings_team_table .".team_id = ". $this->team_table .".team_id
                JOIN ". $this->season_table ." ON ". $this->db_table .".season_id = ". $this->season_table .".season_id
                JOIN ". $this->competition_table ." ON ". $this->db_table .".competition_id = ". $this->competition_table .".competition_id
                ORDER BY
                    ". $this->db_table .".standings_id ASC,
                    ". $this->standings_team_table .".points DESC,
                    ". $this->standings_team_table .".goal_difference DESC,
                    ". $this->standings_team_table .".goals_scored DESC,
                    ". $this->standings_team_table .".wins DESC,
                    ". $this->standings_team_table .".draws DESC,
                    ". $this->standings_team_table .".losses DESC
                
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";


        }

        /**
         * This function gets a standings' id using its season id and competition id
         */
        public function getStandingsId () {

            $sqlQuery = "
                SELECT 
                    ". $this->db_table .".standings_id
                FROM
                    ". $this->db_table ."
                JOIN 
                    ". $this->standings_team_table ." ON ". $this->db_table .".standings_id = ". $this->standings_team_table .".standings_id
                JOIN
                    ". $this->team_table ." ON ". $this->standings_team_table .".team_id = ". $this->team_table .".team_id
                WHERE
                    ". $this->db_table .".season_id = :season_id
                    AND
                    ". $this->db_table .".competition_id = :competition_id
                    AND
                    ". $this->team_table .".team_id = :team_id
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":season_id", $this->season_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRows = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRows) {
                $this->standings_id = $dataRows['standings_id'];
                return json_encode($dataRows);
            }
            return 0;
        }


        // --- UPDATE FUNCTIONS ---

        /**
         * This function updates the teams details in a table
         */
        public function updateStandingsTeam () {

            $sqlQuery = "
                UPDATE
                    ". $this->standings_team_table ."
                SET
                    ". $this->standings_team_table .".points = :points,
                    ". $this->standings_team_table .".wins = :wins,
                    ". $this->standings_team_table .".draws = :draws,
                    ". $this->standings_team_table .".losses = :losses,
                    ". $this->standings_team_table .".goals_scored = :goals_scored,
                    ". $this->standings_team_table .".goal_difference = :goal_difference,
                    ". $this->standings_team_table .".no_played = :no_played,
                    ". $this->standings_team_table .".goals_conceded = :goals_conceded
                WHERE
                    ". $this->standings_team_table .".standings_id = :standings_id
                    AND
                    ". $this->standings_team_table .".team_id = :team_id
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":standings_id", $this->standings_id);
            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":points", $this->points);
            $stmt->bindParam(":wins", $this->wins);
            $stmt->bindParam(":draws", $this->draws);
            $stmt->bindParam(":losses", $this->losses);
            $stmt->bindParam(":goals_scored", $this->goals_scored);
            $stmt->bindParam(":goals_conceded", $this->goals_conceded);
            $stmt->bindParam(":goal_difference", $this->goal_difference);
            $stmt->bindParam(":no_played", $this->no_played);

            if($stmt->execute()){
                http_response_code(200);
                return true;
            }

            return false;
        }

        // --- DELETE FUNCTIONS ---
        /**
         * This function deletes a table
         */
        public function deleteStandings () {

            $sqlQuery = "
                DELETE FROM
                    ". $this->db_table ."
                WHERE
                    ". $this->db_table .".standings_id = :standings_id
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":standings_id", $this->standings_id);

            if($stmt->execute()){
                http_response_code(204);
                return true;
            }

            return false;
        }

        /**
         * This function deletes a team from a table.
         */
        public function deleteStandingsTeam () {

            $sqlQuery = "
                DELETE FROM
                    ". $this->standings_team_table ."
                WHERE
                    ". $this->standings_team_table .".standings_id = :standings_id
                    AND
                    ". $this->standings_team_table .".team_id = :team_id
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":standings_id", $this->standings_id);
            $stmt->bindParam(":team_id", $this->team_id);

            if($stmt->execute()){
                http_response_code(204);
                return true;
            }

            return false;
        }



    }
?>