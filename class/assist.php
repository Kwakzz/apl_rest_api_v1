<?php
    class Assist {
        // Connection
        private $conn;

        // Table
        private $db_table = "Assist";

        // Columns
        public $assist_id;
        public $goal_id;
        public $player_id;
        public $game_id;
        public $team_id;

        // helper tables
        private $player_table = "Player";
        private $game_table = "Game";
        private $gameweek_table = "Gameweek";
        private $team_table = "Team";
        private $season_table = "Season";
        private $season_competition_table = "SeasonCompetition";
        private $competition_table = "Competition";
        private $goal_table = "Goal";

        // helper columns
        public $competition_id;
        public $season_id;
        public $assist_provider_id;
        

        
        // Db connection
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // assists are associated with goals
        // refer to the goal table for the create assist function


        // READ FUNCTIONS

        /**
         * This function gets the assists from a game. Assists are associated with goals. 
         * @return string json object containing all assists from a game if successful, otherwise empty string.
         */
         public function getAssistsFromGame () {
            // Query to get assists from a game
            $sqlQuery = "SELECT 
                        ". $this->db_table . ".assist_id,
                        ". $this->db_table . ".goal_id,
                        ". $this->db_table . ".player_id,
                        ". $this->player_table . ".fname,
                        ". $this->player_table . ".lname
                        FROM
                        ". $this->db_table . "
                        JOIN ". $this->player_table . " ON ". $this->db_table . ".player_id = ". $this->player_table . ".player_id
                        JOIN ". $this->goal_table . " ON ". $this->db_table . ".goal_id = ". $this->goal_table . ".goal_id
                        JOIN ". $this->game_table . " ON ". $this->goal_table . ".game_id = ". $this->game_table . ".game_id
                        WHERE
                        ". $this->game_table . ".game_id = :game_id";


            $stmt = $this->conn->prepare($sqlQuery);


            // bind data
            $stmt->bindParam(':game_id', $this->game_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            else {
                return "";
            }

        }


        /**
         * Get top 10 top assist providers in a competition during a season.
         * @return string json object containing top 10 assist providers in a competition during a season if successful, otherwise empty string.
         */
        public function getTop10AssistProvidersBySeasonAndCompetition () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->team_table .".team_name,
                            ". $this->team_table .".team_name_abbrev,
                            ". $this->team_table .".team_logo_url,
                            COUNT(". $this->db_table .".player_id) AS no_of_assists
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON ".$this->db_table.".player_id = ". $this->player_table .".player_id
                        JOIN ". $this->goal_table ." ON ". $this->db_table .".goal_id = ". $this->goal_table .".goal_id
                        JOIN ". $this->team_table ." ON ". $this->player_table .".team_id = ". $this->team_table .".team_id
                        JOIN ". $this->game_table ." ON ". $this->goal_table .".game_id = ". $this->game_table .".game_id
                        JOIN ". $this->competition_table ." ON ". $this->game_table .".competition_id = ". $this->competition_table .".
                        competition_id
                        JOIN ". $this->gameweek_table ." ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ". $this->season_table ." ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id
                        WHERE
                        ".$this->game_table.".competition_id = :competition_id
                        AND
                        ".$this->gameweek_table.".season_id = :season_id
                        GROUP BY
                            ". $this->db_table .".player_id
                        ORDER BY
                            no_of_assists DESC
                        LIMIT 10";


            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }


        


    }
?>