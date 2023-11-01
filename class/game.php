<?php
    class Game {
        // Connection
        private $conn;
        // Table
        private $db_table = "Game";
        // Gameweek table
        private $db_table_gameweek = "Gameweek";
        // CupGame table
        private $db_table_cupgame = "CupGame";
        // Competition table
        private $db_table_competition = "Competition";
        // Season table
        private $db_table_season = "Season";
        // SeasonCompetition table
        private $db_table_season_competition = "SeasonCompetition";
        // Stage table
        private $db_table_stage = "Stage";
        private $db_table_team = "Team";
        // Columns
        public $game_id;
        public $start_time;
        public $gameweek_id;
        public $home_id;
        public $away_id;
        public $competition_id;
        

        // helper column
        public $stage_id;
        public $season_id;
        public $team_id;
        public $gender;
        
        // Db connection
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- HELPER FUNCTIONS ---

        /**
         * This is a helper function for the addCupGame function
         */
        public function addCupGameHelper() {
            // Query to insert a cup game into the database
            $sqlQuery = "INSERT INTO
                        ". $this->db_table_cupgame .
                        "(game_id, stage_id)
                        VALUES(:game_id, :stage_id)";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->bindParam(':stage_id', $this->stage_id);

            if($stmt->execute()){
                return true;
            }
            return false;
        }

 
        // --- CREATE FUNCTIONS ---

        /**
         * This function enables the admin user to create a game
         */
        public function addGame () {

            // Query to insert a game into the database
            $sqlQuery = "INSERT INTO
                        ". $this->db_table .
                        "(game_id, start_time, gameweek_id, home_id, away_id, competition_id)
                        VALUES(:game_id, :start_time, :gameweek_id, :home_id, :away_id, :competition_id)";


            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->start_time=htmlspecialchars(strip_tags($this->start_time));

            // bind data
            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->bindParam(':start_time', $this->start_time);
            $stmt->bindParam(':gameweek_id', $this->gameweek_id);
            $stmt->bindParam(':home_id', $this->home_id);
            $stmt->bindParam(':away_id', $this->away_id);
            $stmt->bindParam(':competition_id', $this->competition_id);

            if($stmt->execute()){
                http_response_code(201);
                return true;
            }
            return false;

        }

        

        /**
         * This function adds a cup game to the database
         */
        public function addCupGame () {

            // Query to insert a game into the database
            $sqlQuery = "INSERT INTO
                        ". $this->db_table .
                        "(game_id, start_time, gameweek_id, home_id, away_id, competition_id)
                        VALUES(:game_id, :start_time, :gameweek_id, :home_id, :away_id, :competition_id)";


            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->start_time=htmlspecialchars(strip_tags($this->start_time));

            // bind data
            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->bindParam(':start_time', $this->start_time);
            $stmt->bindParam(':gameweek_id', $this->gameweek_id);
            $stmt->bindParam(':home_id', $this->home_id);
            $stmt->bindParam(':away_id', $this->away_id);
            $stmt->bindParam(':competition_id', $this->competition_id);


            if ($stmt->execute()) {
                // Get the newly inserted game_id
                $this->game_id = $this->conn->lastInsertId();
                $this->addCupGameHelper();
                http_response_code(201);
                return true;
            }
            return false;
        }

        // --- READ FUNCTIONS ---


        /**
         * This function gets the fixtures for a gameweek
         */
        public function getGameweekFixtures() {
            $sqlQuery = "SELECT 
            ".$this->db_table.".game_id,
            ".$this->db_table.".start_time,
            ".$this->db_table.".home_id,
            ".$this->db_table.".away_id,
            ".$this->db_table.".competition_id,
            ".$this->db_table_competition.".competition_name,
            ".$this->db_table_competition.".competition_abbrev,
            ".$this->db_table_competition.".gender,
            ".$this->db_table_gameweek.".gameweek_id,
            ".$this->db_table_gameweek.".gameweek_number,
            ".$this->db_table_gameweek.".gameweek_date,
            ".$this->db_table_cupgame.".stage_id,
            ".$this->db_table_stage.".stage_name,
             home_team.team_name AS home_team_name,
             home_team.team_name_abbrev AS home_team_name_abbrev,
             home_team.team_logo_url AS home_team_logo_url,
             away_team.team_name AS away_team_name,
             away_team.team_name_abbrev AS away_team_name_abbrev,
             away_team.team_logo_url AS away_team_logo_url
             FROM ". $this->db_table ."
             JOIN ". $this->db_table_competition ." ON ". $this->db_table. ".competition_id = ". $this->db_table_competition. ".competition_id
             JOIN ". $this->db_table_gameweek ." ON ". $this->db_table. ".gameweek_id = ". $this->db_table_gameweek. ".gameweek_id
             LEFT JOIN ". $this->db_table_cupgame ." ON ". $this->db_table. ".game_id = ". $this->db_table_cupgame. ".game_id
             LEFT JOIN ". $this->db_table_stage ." ON ". $this->db_table_cupgame. ".stage_id = ". $this->db_table_stage. ".stage_id
             LEFT JOIN ". $this->db_table_team ." AS home_team ON ". $this->db_table. ".home_id = home_team.team_id
             LEFT JOIN ". $this->db_table_team ." AS away_team ON ". $this->db_table. ".away_id = away_team.team_id
             WHERE ".$this->db_table_gameweek. ".gameweek_id = :gameweek_id";



            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':gameweek_id', $this->gameweek_id);

            $stmt->execute();

            $dataRows =$stmt->fetchAll(PDO::FETCH_ASSOC);

            // if row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if there are no fixtures for the gameweek yet or the gameweek does not exist
            return "";
            
        }

        /**
         * This function gets the men's fixtures for a gameweek
         */
        public function getMensGameweekFixtures () {
            $sqlQuery = "SELECT *,
                        ".$this->db_table.".game_id AS game_id                  
                        FROM  
                        ".$this->db_table."
                        JOIN " .$this->db_table_competition. " ON " .$this->db_table. ".competition_id = " .$this->db_table_competition. ".competition_id
                        JOIN ". $this->db_table_gameweek ." ON ". $this->db_table. ".gameweek_id = ". $this->db_table_gameweek. ".gameweek_id
                        LEFT JOIN ". $this->db_table_cupgame ." ON ". $this->db_table. ".game_id = ". $this->db_table_cupgame. ".game_id
                        LEFT JOIN ". $this->db_table_stage ." ON ". $this->db_table_cupgame. ".stage_id = ". $this->db_table_stage. ".stage_id
                        LEFT JOIN ". $this->db_table_team ." AS home_team ON ". $this->db_table. ".home_id = home_team.team_id
                        LEFT JOIN ". $this->db_table_team ." AS away_team ON ". $this->db_table. ".away_id = away_team.team_id
                        WHERE
                        ".$this->db_table.".gameweek_id = :gameweek_id
                        AND ".$this->db_table_competition. ".gender = 'Male'";
            

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':gameweek_id', $this->gameweek_id);
            
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
        }

        /**
         * This function gets the men's fixtures for a gameweek
         */
        public function getWomensGameweekFixtures () {
            $sqlQuery = "SELECT *,
                        ".$this->db_table.".game_id AS game_id                 
                        FROM  
                        ".$this->db_table."
                        JOIN " .$this->db_table_competition. " ON " .$this->db_table. ".competition_id = " .$this->db_table_competition. ".competition_id
                        JOIN ". $this->db_table_gameweek ." ON ". $this->db_table. ".gameweek_id = ". $this->db_table_gameweek. ".gameweek_id
                        LEFT JOIN ". $this->db_table_cupgame ." ON ". $this->db_table. ".game_id = ". $this->db_table_cupgame. ".game_id
                        LEFT JOIN ". $this->db_table_stage ." ON ". $this->db_table_cupgame. ".stage_id = ". $this->db_table_stage. ".stage_id
                        LEFT JOIN ". $this->db_table_team ." AS home_team ON ". $this->db_table. ".home_id = home_team.team_id
                        LEFT JOIN ". $this->db_table_team ." AS away_team ON ". $this->db_table. ".away_id = away_team.team_id
                        WHERE
                        ".$this->db_table.".gameweek_id = :gameweek_id
                        AND ".$this->db_table_competition. ".gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':gameweek_id', $this->gameweek_id);
            
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
        }

        /**
         * This functions gets the games being played in a particular competition for a particular season
         */
         public function getSeasonCompetitionFixtures () {
            $sqlQuery = "SELECT 
                        *,
                        ".$this->db_table.".game_id AS game_id             
                        FROM " .
                        $this->db_table."
                        JOIN ".$this->db_table_gameweek. " ON ".$this->db_table.".gameweek_id = ".$this->db_table_gameweek. ".gameweek_id 
                        JOIN ".$this->db_table_competition. " ON ".$this->db_table.".competition_id = ".$this->db_table_competition. ".competition_id
                        LEFT JOIN ". $this->db_table_cupgame ." ON ". $this->db_table. ".game_id = ". $this->db_table_cupgame. ".game_id
                        LEFT JOIN ". $this->db_table_stage ." ON ". $this->db_table_cupgame. ".stage_id = ". $this->db_table_stage. ".stage_id
                        LEFT JOIN ". $this->db_table_team ." AS home_team ON ". $this->db_table. ".home_id = home_team.team_id
                        LEFT JOIN ". $this->db_table_team ." AS away_team ON ". $this->db_table. ".away_id = away_team.team_id
                        WHERE
                        ".$this->db_table_gameweek. ".season_id = :season_id
                        AND 
                        ".$this->db_table. ".competition_id = :competition_id
                        ORDER BY ".$this->db_table_gameweek.".gameweek_date ASC
                        ";


            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':season_id', $this->season_id);
            $stmt->bindParam(':competition_id', $this->competition_id);
            
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
         }



        /**
         * This functions gets the cup games in a particular season
         */
         public function getSeasonCupGames () {
            $sqlQuery = "SELECT *                
                        FROM " .
                        $this->db_table.
                        "
                        JOIN ".$this->db_table_gameweek. " ON ".$this->db_table.".gameweek_id = ".$this->db_table_gameweek. ".gameweek_id 
                        JOIN ".$this->db_table_competition. " ON ".$this->db_table.".competition_id = ".$this->db_table_competition. ".competition_id 
                        JOIN ".$this->db_table_cupgame. " ON ".$this->db_table.".game_id = ".$this->db_table_cupgame. ".game_id
                        JOIN ".$this->db_table_stage. " ON ".$this->db_table_cupgame.".stage_id = ".$this->db_table_stage. ".stage_id
                        WHERE
                        ".$this->db_table_gameweek. ".season_id = :season_id
                        AND ".$this->db_table. ".competition_id = :competition_id
                        ORDER BY ".$this->db_table_gameweek.".gameweek_date ASC
                        ";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':season_id', $this->season_id);
            $stmt->bindParam(':competition_id', $this->competition_id);            
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
        }

        /**
         * This function gets the number of games played by a team
         */
        public function getNoOfGamesPlayedByTeam () {
            $sqlQuery = "SELECT COUNT(*) AS no_of_games_played
            FROM " .$this->db_table. "
            JOIN ".$this->db_table_gameweek." ON ".$this->db_table.".gameweek_id = ".$this->db_table_gameweek.".gameweek_id
            WHERE home_id = :team_id OR away_id = :team_id            
            AND DATEDIFF(
                ".$this->db_table_gameweek.".gameweek_date, CURDATE()) < 0";



            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':team_id', $this->team_id);

            $stmt->execute();

            $dataRow =$stmt->fetch(PDO::FETCH_ASSOC);

            // if row is not empty
            if ($dataRow) {
                return json_encode($dataRow);
            }

            // if no games have been played by the team
            return "";
        }

        /**
         * This function gets the number of games played by a men's team
         */
        public function getNoOfGamesPlayedByMensTeam () {
            $sqlQuery = "SELECT COUNT(DISTINCT ".$this->db_table.".game_id) AS no_of_games_played
            FROM " .$this->db_table. "
            JOIN ".$this->db_table_competition." ON ".$this->db_table.".competition_id = ".$this->db_table_competition.".competition_id
            JOIN ".$this->db_table_gameweek." ON ".$this->db_table.".gameweek_id = ".$this->db_table_gameweek.".gameweek_id
            WHERE (".$this->db_table.".home_id = :team_id OR ".$this->db_table.".away_id = :team_id)
            AND
            ".$this->db_table_competition.".gender = 'Male'
            AND
            ".$this->db_table_gameweek.".gameweek_date < CURDATE()";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':team_id', $this->team_id);

            $stmt->execute();

            $dataRow =$stmt->fetch(PDO::FETCH_ASSOC);

            // if row is not empty
            if ($dataRow) {
                return json_encode($dataRow);
            }

            // if no games have been played by the team
            return "";
        }

        /**
         * This function gets the number of games played by a women's team
         */
        public function getNoOfGamesPlayedByWomensTeam () {
            $sqlQuery = "SELECT COUNT(DISTINCT ".$this->db_table.".game_id) AS no_of_games_played
            FROM " .$this->db_table. "
            JOIN ".$this->db_table_competition." ON ".$this->db_table.".competition_id = ".$this->db_table_competition.".competition_id
            JOIN ".$this->db_table_gameweek." ON ".$this->db_table.".gameweek_id = ".$this->db_table_gameweek.".gameweek_id
            WHERE (".$this->db_table.".home_id = :team_id OR ".$this->db_table.".away_id = :team_id)
            AND
            ".$this->db_table_competition.".gender = 'Female'
            AND
            ".$this->db_table_gameweek.".gameweek_date < CURDATE()";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':team_id', $this->team_id);

            $stmt->execute();

            $dataRow =$stmt->fetch(PDO::FETCH_ASSOC);

            // if row is not empty
            if ($dataRow) {
                return json_encode($dataRow);
            }

            // if no games have been played by the team
            return "";
        }


        // --- UPDATE FUNCTIONS ---

        /**
         * This function enables the admin user to edit a game
         */
        public function editGame () {

            // Query to update a game in the database
            $sqlQuery = "UPDATE
                        ". $this->db_table .
                        " SET
                        start_time = :start_time,
                        home_id = :home_id,
                        away_id = :away_id,
                        competition_id = :competition_id
                        WHERE
                        game_id = :game_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->bindParam(':start_time', $this->start_time);
            $stmt->bindParam(':home_id', $this->home_id);
            $stmt->bindParam(':away_id', $this->away_id);
            $stmt->bindParam(':competition_id', $this->competition_id);

            if($stmt->execute()){
                return true;
            }
            return false;

        }


        // --- DELETE FUNCTIONS ---

         /**
         * This function deletes a game
         */
        public function deleteGame() {
            $sqlQuery = "DELETE FROM
                        ". $this->db_table ."
                        WHERE
                        game_id = :game_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':game_id', $this->game_id);

            if($stmt->execute()){
                http_response_code(204);
                return true;
            }
            return false;
        }
        
        
    }
?>