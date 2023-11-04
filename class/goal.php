<?php
    class Goal {
        // Connection
        private $conn;
        // Table
        private $db_table = "Goal";
        // Columns
        public $goal_id;
        public $player_id;
        public $game_id;
        public $team_id;
        public $minute_scored;

        // helper tables
        private $player_table = "Player";
        private $game_table = "Game";
        private $cup_game_table = "CupGame";
        private $gameweek_table = "Gameweek";
        private $team_table = "Team";
        private $season_table = "Season";
        private $season_competition_table = "SeasonCompetition";
        private $competition_table = "Competition";
        private $assist_table = "Assist";
        private $stage_table = "Stage";

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

        // --- HELPER FUNCTIONS ---

        /**
         * This is a helper function for the addGoalAndAssist function.
         * It adds an assist to the Assist table.
         * This assist references the goal.
         * @return boolean true if assist is added, otherwise false.
         */
        public function addAssist() {
            // Query to insert a cup game into the database
            $sqlQuery = "INSERT INTO
                        ". $this->assist_table .
                        "(goal_id, player_id)
                        VALUES(:goal_id, :player_id)";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':goal_id', $this->goal_id);
            $stmt->bindParam(':player_id', $this->assist_provider_id);

            if($stmt->execute()){
                return true;
            }
            return false;
        }

        /**
         * This function adds a goal to the Goal table.
         * This function is called when the assist provider id is not set.
         * @return boolean true if goal is added, otherwise false.
         */
        public function addGoal () {
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                        (
                            goal_id,
                            player_id,
                            game_id,
                            team_id,
                            minute_scored
                        )
                    VALUES
                        (   
                            :goal_id,
                            :player_id,
                            :game_id,
                            :team_id,
                            :minute_scored
                        )";
                    
            $stmt = $this->conn->prepare($sqlQuery);
                
            // bind data
            $stmt->bindParam(":goal_id", $this->goal_id);  
            $stmt->bindParam(":player_id", $this->player_id);
            $stmt->bindParam(":game_id", $this->game_id);
            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":minute_scored", $this->minute_scored);

            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }

        /**
         * This function adds a goal and an assist to the Goal and Assist tables respectively.
         * This function is called when the assist provider id is set.
         * It has a response code of 201 if successful.
         * @uses addAssist(). This function is called to add an assist to the Assist table. The assist references the goal.
         * @return boolean true if goal and assist are added, otherwise false.
         */
        public function addGoalAndAssist () {
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                        (
                            goal_id,
                            player_id,
                            game_id,
                            team_id,
                            minute_scored
                        )
                    VALUES
                        (   
                            :goal_id,
                            :player_id,
                            :game_id,
                            :team_id,
                            :minute_scored
                        )";
                    
            $stmt = $this->conn->prepare($sqlQuery);
                
            // bind data
            $stmt->bindParam(":goal_id", $this->goal_id);  
            $stmt->bindParam(":player_id", $this->player_id);
            $stmt->bindParam(":game_id", $this->game_id);
            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":minute_scored", $this->minute_scored);

            if ($stmt->execute()) {
                // Get the newly inserted goal
                $this->goal_id = $this->conn->lastInsertId();
                if ($this->addAssist()) {
                    http_response_code(201);
                    return true;
                }
            }
            return false;
        }

        // --- READ FUNCTIONS ---

        
        /**
         * This function gets all goals scored in a game.
         * @return string json object containing all goals scored in a game if successful, otherwise empty string.
         */
        public function getGoalsByGame () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".goal_id,
                            ". $this->db_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->db_table .".game_id,
                            ". $this->db_table .".team_id,
                            ". $this->db_table .".minute_scored
                        FROM
                            ". $this->db_table ."
                        JOIN
                            ". $this->player_table ." 
                        ON 
                            ". $this->db_table .".player_id = ". $this->player_table .".player_id
                        WHERE 
                            game_id = :game_id                
                        ORDER BY minute_scored DESC";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":game_id", $this->game_id);

            $stmt->execute();

            // get data rows
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function gets goals scored by a team in a game.
         * @return string json object containing all goals scored by a team in a game if successful, otherwise empty string.
         */
        public function getGoalsByTeamAndGame () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".goal_id,
                            ". $this->db_table .".player_id,
                            ". $this->db_table .".team_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->db_table .".game_id,
                            ". $this->team_table .".team_name,
                            ". $this->db_table .".minute_scored
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->player_table." ON ".$this->db_table." .player_id = ".$this->player_table.".player_id
                        JOIN ".$this->team_table." ON ".$this->db_table." .team_id = ".$this->team_table.".team_id
                        WHERE 
                            ". $this->db_table .".team_id = :team_id
                        AND 
                            game_id = :game_id
                        ORDER BY minute_scored DESC";


            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":game_id", $this->game_id);

            $stmt->execute();

            // get data rows
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function gets the goals scored by a player in a season.
         * For example, the number of goals scored by Daniel in the 2023 Fall Season.
         * @return string json object containing all goals scored by a player in a season if successful, otherwise empty string.
         */
        public function getGoalsByPlayerAndSeason () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".goal_id,
                            ". $this->db_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->db_table .".game_id,
                            ". $this->db_table .".team_id,
                             ". $this->team_table .".team_name,
                            ". $this->db_table .".minute_scored                        
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->game_table." ON ".$this->db_table." .game_id = ".$this->game_table.".game_id
                        JOIN ".$this->gameweek_table." ON ".$this->game_table." .gameweek_id = ".$this->gameweek_table.".gameweek_id
                        JOIN ".$this->season_table." ON ".$this->gameweek_table." .season_id = ".$this->season_table.".season_id
                        JOIN ".$this->team_table." ON ".$this->db_table." .team_id = ".$this->team_table.".team_id
                        JOIN ".$this->player_table." ON ".$this->db_table." .player_id = ".$this->player_table.".player_id
                        WHERE 
                            ".$this->db_table.".player_id = :player_id
                            AND
                            ".$this->gameweek_table.".season_id = :season_id
                        ORDER BY minute_scored DESC";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":player_id", $this->player_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";

        }

        /**
         * This functions gets the goals scored by a player in a competition during a season.
         * For example, the number of goals scored by Daniel in the Premier League during the 2023 Fall Season.
         * @return string json object containing all goals scored by a player in a competition during a season if successful, otherwise empty string.
         */
        public function getGoalsByPlayerAndCompetitionAndSeason () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->db_table .".game_id,
                            ". $this->db_table .".team_id,
                            ". $this->team_table .".team_name,
                            ". $this->db_table .".minute_scored,
                            ". $this->competition_table .".competition_name,
                            ". $this->competition_table .".gender,
                            ". $this->season_table .".season_name,
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->game_table." ON ".$this->db_table." .game_id = ".$this->game_table.".game_id
                        JOIN ".$this->gameweek_table." ON ".$this->game_table." .gameweek_id = ".$this->gameweek_table.".gameweek_id
                        JOIN ".$this->season_table." ON ".$this->gameweek_table." .season_id = ".$this->season_table.".season_id
                        JOIN ".$this->team_table." ON ".$this->db_table." .team_id = ".$this->team_table.".team_id
                        JOIN ".$this->player_table." ON ".$this->db_table." .player_id = ".$this->player_table.".player_id
                        JOIN ".$this->competition_table." ON ".$this->game_table." .competition_id = ".$this->competition_table.".competition_id
                        WHERE 
                            ".$this->db_table.".player_id = :player_id
                            AND
                            ".$this->gameweek_table.".season_id = :season_id
                            AND
                            ".$this->game_table.".competition_id = :competition_id
                        ORDER BY ".$this->db_table.".game_id DESC";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":player_id", $this->player_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * Get the total number of goals scored by a player since the start of records.
         * @return string json object containing the total number of goals scored by a player since the start of records if successful, otherwise empty string.
         */
        public function getTotalGoalsByPlayer () {
            $sqlQuery = "SELECT
                            ". $this->player_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->team_table .".team_name,
                            COUNT(*) as total_goals
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->player_table." ON ".$this->db_table.".player_id = ".$this->player_table.".player_id
                        JOIN ".$this->team_table." ON ".$this->db_table.".team_id = ".$this->team_table.".team_id
                        WHERE 
                            ". $this->player_table .".player_id = :player_id
                        GROUP BY
                            ". $this->player_table .".player_id";
                        

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":player_id", $this->player_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }

            return "";
        }

        /**
         * This function gets the goals scored in a competition during a season
         * For example, the number of goals scored in the Premier League during the 2023 Fall Season.
         * @return string json object containing all goals scored in a competition during a season if successful, otherwise empty string.
         */
        public function getGoalsByCompetitionAndSeason () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".goal_id,
                            ". $this->db_table .".player_id,
                            ". $this->db_table .".minute_scored,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->db_table .".game_id,
                            ". $this->db_table .".team_id,
                            ". $this->competition_table .".competition_name,
                            ". $this->team_table .".team_name,
                            ". $this->season_table .".season_name
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->player_table." ON ". $this->db_table .".player_id = ". $this->player_table .".player_id
                        JOIN ".$this->game_table." ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->competition_table." ON ". $this->game_table .".competition_id = ". $this->competition_table .".competition_id
                        JOIN ".$this->gameweek_table." ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ".$this->season_table." ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id 
                        JOIN ".$this->team_table." ON ".$this->db_table.".team_id = ".$this->team_table.".team_id
                        WHERE
                        ". $this->game_table .".competition_id = :competition_id
                        AND
                        ". $this->gameweek_table .".season_id = :season_id
                        GROUP BY
                        ". $this->db_table .".goal_id";
   

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

        /**
         * Get top 10 goal scorers in a competition during a season.
         * For example, the top 10 goal scorers in the Premier League during the 2023 Fall Season.
         * @return string json object containing top 10 goal scorers in a competition during a season if successful, otherwise empty string.
         */
        public function getTop10GoalScorersBySeasonAndCompetition () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->team_table .".team_name,
                            ". $this->team_table .".team_name_abbrev,
                            ". $this->team_table .".team_logo_url,
                            ". $this->season_table .".season_name,
                            ". $this->competition_table .".competition_name,
                            COUNT(". $this->db_table .".player_id) AS no_of_goals
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->player_table." ON ". $this->db_table .".player_id = ". $this->player_table .".player_id
                        JOIN ".$this->game_table." ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->competition_table." ON ". $this->game_table .".competition_id = ". $this->competition_table .".competition_id
                        JOIN ".$this->gameweek_table." ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ".$this->season_table." ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id
                        JOIN ".$this->team_table." ON ".$this->db_table.".team_id = ".$this->team_table.".team_id
                        WHERE 
                            ". $this->game_table .".competition_id = :competition_id
                        AND
                             ". $this->gameweek_table .".season_id = :season_id
                        GROUP BY
                            ". $this->db_table .".player_id
                        ORDER BY
                            no_of_goals DESC
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

        /**
         * Get top 10 goal scorers in a season.
         * For example, the top 10 goal scorers in the 2023 Fall Season.
         * It includes goals scored in all competitions.
         * It also includes both men and women.
         * @return string json object containing top 10 goal scorers in a season if successful, otherwise empty string.
         */
        public function getTop10GoalScorersBySeason () {
            $sqlQuery = "SELECT
                            ". $this->db_table .".player_id,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            ". $this->team_table .".team_name,
                            ". $this->team_table .".team_name_abbrev,
                            ". $this->team_table .".team_logo_url,
                            ". $this->season_table .".season_name,
                            COUNT(". $this->db_table .".player_id) AS no_of_goals
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON " .$this->db_table.".player_id = ". $this->player_table .".player_id
                        JOIN ". $this->team_table ." ON ". $this->db_table .".team_id = ". $this->team_table .".team_id
                        JOIN ". $this->game_table ." ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ". $this->gameweek_table ." ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ". $this->season_table ." ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id
                        WHERE ".
                        $this->gameweek_table .".season_id = :season_id
                        GROUP BY
                            ". $this->db_table .".player_id
                        ORDER BY
                            no_of_goals DESC
                        LIMIT 10";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function returns the list of teams who have kept the most clean sheets in a season competition.
         * We're looking for games where the opponent did not score. We're looking for games in a particular competition and season. So the season and competition are parameters. We join the goal table. We find the cases where a team was the home team and the opponent did not score. We also find the cases where a team was the away team and the opponent did not score. We group by the team id. We order by the number of clean sheets in descending order. 
         * @return string json object containing the list of teams who have kept the most clean sheets in a season competition if successful, otherwise empty string.
         */     
        public function getTop10CleanSheetsBySeasonAndCompetition() {

            $sqlQuery = "
            (
                SELECT
                COUNT(*) as no_of_clean_sheets,
                ".$this->team_table.".team_name,
                ".$this->team_table.".team_name_abbrev,
                ".$this->team_table.".team_logo_url
                FROM 
                ".$this->game_table."
                JOIN ".$this->gameweek_table." ON ".$this->game_table.".gameweek_id = ".$this->gameweek_table.".gameweek_id
                JOIN ".$this->season_table." ON ".$this->gameweek_table.".season_id = ".$this->season_table.".season_id
                JOIN ".$this->team_table." ON ".$this->game_table.".home_id = ".$this->team_table.".team_id
                WHERE
                (
                    SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = away_id
                    AND game_id = " . $this->game_table . ".game_id
                ) = 0
                AND
                ".$this->game_table.".competition_id = :competition_id
                AND
                ".$this->season_table.".season_id = :season_id
                AND
                ".$this->gameweek_table.".gameweek_date < CURDATE()
                GROUP BY ".$this->team_table.".team_id
                ORDER BY no_of_clean_sheets DESC
            )
            UNION 
            (
                SELECT
                COUNT(*) as no_of_clean_sheets,
                ".$this->team_table.".team_name,
                ".$this->team_table.".team_name_abbrev,
                ".$this->team_table.".team_logo_url
                FROM 
                ".$this->game_table."
                JOIN ".$this->gameweek_table." ON ".$this->game_table.".gameweek_id = ".$this->gameweek_table.".gameweek_id
                JOIN ".$this->season_table." ON ".$this->gameweek_table.".season_id = ".$this->season_table.".season_id
                JOIN ".$this->team_table." ON ".$this->game_table.".away_id = ".$this->team_table.".team_id
                WHERE
                (
                    SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                ) = 0
                AND
                ".$this->game_table.".competition_id = :competition_id
                AND
                ".$this->season_table.".season_id = :season_id
                AND
                ".$this->gameweek_table.".gameweek_date < CURDATE()
                GROUP BY ".$this->team_table.".team_id
                ORDER BY no_of_clean_sheets DESC
            )";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":season_id", $this->season_id);
            $stmt->bindParam(":competition_id", $this->competition_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }

            return "";
        }


        /**
         * Get all goals scored by a team.
         * @return string json object containing the number of goals scored by a team, the team name and team name's abbreviation, if successful, otherwise empty string.
         */
        public function getTotalGoalsByTeam () {
            $sqlQuery = "SELECT ".
                            $this->team_table.".team_name,".
                            $this->team_table.".team_name_abbrev,
                            COUNT(*) as total_goals
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->team_table ." ON ". $this->db_table .".team_id = ". $this->team_table .".team_id
                        WHERE 
                        ".$this->db_table.".team_id = :team_id";                            

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";

        }

        /**
         * This function gets the total number of goals scored by a men's team. It finds the number of goals scored by a team in all competitions whose gender value is "Male."
         * @return string json object containing the number of goals scored by a team, the team name and team name's abbreviation, if successful, otherwise empty string.
         */
        public function getTotalGoalsByMensTeam () {
            $sqlQuery = "SELECT ".
                            $this->team_table.".team_name,".
                            $this->team_table.".team_name_abbrev,
                            COUNT(*) as total_goals
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->team_table." ON ".$this->db_table .".team_id = ". $this->team_table .".team_id
                        JOIN ".$this->game_table." ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->competition_table." ON ". $this->game_table .".competition_id = ". $this->competition_table .".competition_id
                        WHERE 
                            ".$this->db_table.".team_id = :team_id
                        AND
                            ".$this->competition_table.".gender = 'Male'";
                           

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * This function gets the total number of goals scored by a women's team. It finds the number of goals scored by a team in all competitions whose gender value is "Female."
         * @return string json object containing the number of goals scored by a team, the team name and team name's abbreviation, if successful, otherwise empty string.
         */
        public function getTotalGoalsByWomensTeam () {
            $sqlQuery = "SELECT ".
                            $this->team_table.".team_name,".
                            $this->team_table.".team_name_abbrev,
                            COUNT(*) as total_goals
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->team_table." ON ".$this->db_table .".team_id = ". $this->team_table .".team_id
                        JOIN ".$this->game_table." ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->competition_table." ON ". $this->game_table .".competition_id = ". $this->competition_table .".competition_id
                        WHERE 
                            ".$this->db_table.".team_id = :team_id
                        AND
                            ".$this->competition_table.".gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * Get all the goals conceded by a team. These areq goals scored by the opponent. So you search games where the team was the home team and the opponent scored. You also search games where the team was the away team and the opponent scored.
         * @return string json object containing the number of goals conceded by a team, the team name and team name's abbreviation, if successful, otherwise empty string.
         */
        public function getTotalGoalsConcededByTeam () {
            $sqlQuery = "SELECT
                        COUNT(*) as total_goals_conceded
                        FROM
                            " . $this->db_table . "
                        JOIN ".$this->game_table. " ON " . $this->db_table . ".game_id = " . $this->game_table . ".game_id
                        WHERE
                        (" . $this->game_table . ".away_id = :team_id OR " . $this->game_table . ".home_id = :team_id)
                        AND
                        " . $this->db_table . ".team_id != :team_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * Get all the goals conceded by the men's team.
         * For example, Elite has a men's and women's team.
         * If the team_id is say 23020, the function will return the number of goals conceded by the Elite men's team since the beginning of records.
         * @return string json object containing the number of goals conceded by a team, the team name and team name's abbreviation, if successful, otherwise empty string.
         */
        public function getTotalGoalsConcededByMensTeam () {
            $sqlQuery = "SELECT
                        COUNT(*) as total_goals_conceded
                        FROM
                            " . $this->db_table . "
                        JOIN ".$this->game_table. " ON " . $this->db_table . ".game_id = " . $this->game_table . ".game_id
                        JOIN ".$this->competition_table. " ON " . $this->game_table . ".competition_id = " . $this->competition_table . ".competition_id
                        WHERE
                        (" . $this->game_table . ".away_id = :team_id OR " . $this->game_table . ".home_id = :team_id)
                        AND
                        " . $this->db_table . ".team_id != :team_id
                        AND
                        ". $this->competition_table .".gender = 'Male'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * Get all the goals conceded by the men's team.
         * For example, Elite has a men's and women's team.
         * If the team_id is say 23020, the function will return the number of goals conceded by the Elite women's team since the beginning of records.
         * @return string json object containing the number of goals conceded by a team, the team name and team name's abbreviation, if successful, otherwise empty string.
         */
        public function getTotalGoalsConcededByWomensTeam () {
            $sqlQuery = "SELECT
                        COUNT(*) as total_goals_conceded
                        FROM
                            " . $this->db_table . "
                        JOIN ".$this->game_table. " ON " . $this->db_table . ".game_id = " . $this->game_table . ".game_id
                        JOIN ".$this->competition_table. " ON " . $this->game_table . ".competition_id = " . $this->competition_table . ".competition_id
                        WHERE
                        (" . $this->game_table . ".away_id = :team_id OR " . $this->game_table . ".home_id = :team_id)
                        AND
                        " . $this->db_table . ".team_id != :team_id
                        AND
                        ". $this->competition_table .".gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * Get all goals scored by a team in a competition in a season.
         * For example, the number of goals scored by Elite in the Premier League during the 2023 Fall Season.
         * @return string json object containing all goals scored by a team in a competition in a season if successful, otherwise empty string.
         */
        public function getTotalGoalsByTeamInSeasonCompetition() {
            $sqlQuery = "SELECT
                            ".$this->db_table.".goal_id,
                            ".$this->db_table.".player_id,
                            ".$this->player_table.".fname,
                            ".$this->player_table.".lname,
                            ".$this->db_table.".game_id,
                            ".$this->db_table.".team_id,
                            ".$this->db_table.".minute_scored,
                            ".$this->team_table.".team_name,
                            ".$this->team_table.".team_name_abbrev,
                            ".$this->competition_table.".competition_name,
                            ".$this->season_table.".season_name,
                            ".$this->competition_table.".gender,
                            COUNT(*) as total_goals_scored
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->team_table. " ON " . $this->db_table . ".team_id = " . $this->team_table . ".team_id
                        JOIN ".$this->player_table. " ON " . $this->db_table . ".player_id = " . $this->player_table . ".player_id
                        JOIN ".$this->game_table. " ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->gameweek_table. " ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ".$this->season_table. " ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id
                        JOIN ".$this->competition_table. " ON ". $this->game_table .".competition_id = ". $this->competition_table .".competition_id
                        WHERE
                        ".$this->db_table.".team_id = :team_id
                        AND
                        ". $this->competition_table .".competition_id = :competition_id
                        AND 
                        ". $this->season_table .".season_id = :season_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }
        

        /**
         * Get all goals scored by a team in a season. 
         * For example, the number of goals scored by Elite in the 2023 Fall Season.
         * @return string json object containing all goals scored by a team in a season if successful, otherwise empty string.
         */
        public function getTotalGoalsByTeamAndSeason () {
            $sqlQuery = "SELECT
                            ".$this->db_table.".goal_id,
                            ".$this->db_table.".player_id,
                            ".$this->player_table.".fname,
                            ".$this->player_table.".lname,
                            ".$this->db_table.".game_id,
                            ".$this->db_table.".team_id,
                            ".$this->db_table.".minute_scored,
                            ".$this->team_table.".team_name,
                            ".$this->team_table.".team_name_abbrev,
                            ".$this->season_table.".season_name
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->team_table. " ON " . $this->db_table . ".team_id = " . $this->team_table . ".team_id
                        JOIN ".$this->player_table. " ON " . $this->db_table . ".player_id = " . $this->player_table . ".player_id
                        JOIN ".$this->game_table. " ON " . $this->db_table . ".game_id = " . $this->game_table . ".game_id
                        JOIN ".$this->gameweek_table. " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
                        JOIN ".$this->season_table. " ON " . $this->gameweek_table . ".season_id = " . $this->season_table . ".season_id
                        WHERE 
                        ".$this->db_table.".team_id = :team_id
                        AND
                        ". $this->season_table .".season_id = :season_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

    /**
    * This function gets the total number of wins a team has accrued in history.
    * It finds the count of all games the team played in where the number of goals scored by the team is greater than the number of goals scored by the opposing team.
    * Find count of games where home_id = :team_id and the count of the home team's goals is greater than the count of the away team's goals, or
    * find count of games where away_id = :team_id and the count of the away team's goals is greater than the count of the home team's goals
    * @return string JSON object containing the number of wins a team has had in history if successful, otherwise empty string.
    */

    public function getTeamTotalNumberOfWins() {
       $sqlQuery = "
        SELECT
            COUNT(*) AS no_of_wins
        FROM
            " . $this->game_table . "
        WHERE
            (
                (home_id = :team_id AND (
                    SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id AND game_id = " . $this->game_table . ".game_id
                ) > (
                    SELECT COUNT(*)
                    FROM " . $this->db_table . "
                    WHERE team_id = away_id AND game_id = " . $this->game_table . ".game_id
                ))
                OR
                (away_id = :team_id AND (
                    SELECT COUNT(*)
                    FROM " . $this->db_table . "
                    WHERE team_id = away_id AND game_id = " . $this->game_table . ".game_id
                ) > (
                    SELECT COUNT(*)
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id AND game_id = " . $this->game_table . ".game_id
                ))
            )
    ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

    /**
     * Get total number of wins by a men's team. 
     * @see getTeamTotalNumberOfWins(). It shows the logic of this function.
     * @return string JSON object containing the number of wins a men's team has had in history if successful, otherwise empty string.
     * @see getMensTeam(). getMensTeam() is in team.php. It tells what a men's team is.
     */
    public function getMensTeamTotalNumberOfWins() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_wins
        FROM
            " . $this->game_table . "
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) > (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) > (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND " . $this->game_table . ".competition_id IN (
                SELECT competition_id
                FROM " . $this->competition_table . "
                WHERE gender = 'Male'
            )
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

     /**
     * Get total number of wins by a women's team. 
     * @see getTeamTotalNumberOfWins(). It shows the logic of this function.
     * @return string JSON object containing the number of wins a women's team has had in history if successful, otherwise empty string.
     * @see getWomensTeam(). getWomensTeam() is in team.php. It tells what a women's team is.
     */
    public function getWomensTeamTotalNumberOfWins() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_wins
        FROM
            " . $this->game_table . "
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) > (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) > (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND " . $this->game_table . ".competition_id IN (
                SELECT competition_id
                FROM " . $this->competition_table . "
                WHERE gender = 'Female'
            )
";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }


    /**
     * Get total number of losses by a men's team. 
     * @see getTeamTotalNumberOfWins(). It shows the logic of this function.
     * @return string JSON object containing the number of losses a men's team has had in history if successful, otherwise empty string.
     * @see getMensTeam(). getMensTeam() is in team.php. It tells what a men's team is.
     */
    public function getMensTeamTotalNumberOfLosses() {
       $sqlQuery = "
        SELECT
        COUNT(*) AS no_of_losses
        FROM
            " . $this->game_table . "
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) < (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) < (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND " . $this->game_table . ".competition_id IN (
                SELECT competition_id
                FROM " . $this->competition_table . "
                WHERE gender = 'Male'
            )
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }


     /**
     * Get total number of losses by a women's team. 
     * @see getTeamTotalNumberOfWins(). It shows the logic of this function.
     * @return string JSON object containing the number of losses a women's team has had in history if successful, otherwise empty string.
     * @see getWomensTeam(). getWomensTeam() is in team.php. It tells what a women's team is.
     */
    public function getWomensTeamTotalNumberOfLosses() {
       $sqlQuery = "
        SELECT
        COUNT(*) AS no_of_losses
        FROM
            " . $this->game_table . "
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) < (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) < (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND " . $this->game_table . ".competition_id IN (
                SELECT competition_id
                FROM " . $this->competition_table . "
                WHERE gender = 'Female'
            )
";
        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

     /**
     * Get total number of draws by a women's team. 
     * @see getTeamTotalNumberOfWins(). It shows the logic of this function.
     * @return string JSON object containing the number of draws a women's team has had in history if successful, otherwise empty string.
     * @see getWomensTeam(). getWomensTeam() is in team.php. It tells what a women's team is.
     */
    public function getWomensTeamTotalNumberOfDraws() {
        $sqlQuery = "
        SELECT
        COUNT(*) AS no_of_draws
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) = (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) = (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND " . $this->game_table . ".competition_id IN (
                SELECT competition_id
                FROM " . $this->competition_table . "
                WHERE gender = 'Female'
            )
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()";
            

            

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

    /**
     * Get total number of draws by a men's team. 
     * @see getTeamTotalNumberOfWins(). It shows the logic of this function.
     * @return string JSON object containing the number of draws a men's team has had in history if successful, otherwise empty string.
     * @see getMensTeam(). getMensTeam() is in team.php. It tells what a men's team is.
     */
    public function getMensTeamTotalNumberOfDraws() {
       $sqlQuery = "
        SELECT
        COUNT(*) AS no_of_draws
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) = (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) = (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND " . $this->game_table . ".competition_id IN (
                SELECT competition_id
                FROM " . $this->competition_table . "
                WHERE gender = 'Male'
            )

            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()";


        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

    
    // --- FUNCTIONS FOR LEAGUE TABLE --- //


    /**
     * This function gets the team number of wins in a season's competition.
     * This will also be used in the calculation of points and other stats like goals scored and goals conceded in a league table.
     * @return string JSON object containing the number of wins by a team in a season's competition if successful, otherwise empty string.
     */
    public function getTeamNumberOfWinsInSeasonCompetition() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_wins
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) > (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) > (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND ". $this->gameweek_table . ".season_id = :season_id
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()
            AND " .$this->game_table.".competition_id = :competition_id
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);
        $stmt->bindParam(":season_id", $this->season_id);
        $stmt->bindParam(":competition_id", $this->competition_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

     /**
     * This function gets a team's number of losses in a season's competition.
     * This will also be used in the calculation of points and other stats like goals scored and goals conceded in a league table.
     * @return string JSON object containing the number of losses by a team in a season's competition if successful, otherwise empty string.
     */
    public function getTeamNumberOfLossesInSeasonCompetition() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_losses
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) < (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) < (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND ". $this->gameweek_table . ".season_id = :season_id
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()
            AND " .$this->game_table.".competition_id = :competition_id
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);
        $stmt->bindParam(":season_id", $this->season_id);
        $stmt->bindParam(":competition_id", $this->competition_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

    /**
     * This function gets a team's number of draws in a season's competition.
     * This will also be used in the calculation of points and other stats like goals scored and goals conceded in a league table.
     * @return string JSON object containing the number of draws by a team in a season's competition if successful, otherwise empty string.
     */
    public function getTeamNumberOfDrawsInSeasonCompetition() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_draws
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) = (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) = (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND ". $this->gameweek_table . ".season_id = :season_id
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()
            AND " .$this->game_table.".competition_id = :competition_id
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);
        $stmt->bindParam(":season_id", $this->season_id);
        $stmt->bindParam(":competition_id", $this->competition_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

    /**
    * Get all goals scored by a team in a competition in a season. 
    * @see addSeasonCompetition(). It's in in season.php. It tells what a season competition is.
    * @return string JSON object containing the number of goals scored by a team in a competition in a season if successful, otherwise empty string.
    */
    public function getGoalsByTeamInSeasonCompetition() {
        $sqlQuery = "SELECT
                            COUNT(*) as total_goals_scored
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->team_table. " ON " . $this->db_table . ".team_id = " . $this->team_table . ".team_id
                        JOIN ".$this->player_table. " ON " . $this->db_table . ".player_id = " . $this->player_table . ".player_id
                        JOIN ".$this->game_table. " ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->gameweek_table. " ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ".$this->season_table. " ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id
                        JOIN ".$this->competition_table. " ON ". $this->game_table .".competition_id = ". $this->competition_table .".competition_id
                        WHERE
                        ".$this->db_table.".team_id = :team_id
                        AND
                        ". $this->competition_table .".competition_id = :competition_id
                        AND 
                        ". $this->season_table .".season_id = :season_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
    }

    /**
     * Get all goals conceded by a team in a competition in a season. 
     * @see addSeasonCompetition() in season.php for what a season competition is.
     * @return string JSON object containing the number of goals conceded by a team in a competition in a season
     */
    public function getGoalsConcededByTeamInSeasonCompetition () {
            $sqlQuery = "SELECT
                        COUNT(*) as total_goals_conceded
                        FROM
                            " . $this->db_table . "
                        JOIN ".$this->game_table. " ON " . $this->db_table . ".game_id = " . $this->game_table . ".game_id
                        JOIN ".$this->gameweek_table. " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
                        JOIN ".$this->competition_table. " ON " . $this->game_table . ".competition_id = " . $this->competition_table . ".competition_id
                        WHERE
                        (" . $this->game_table . ".away_id = :team_id OR " . $this->game_table . ".home_id = :team_id)
                        AND
                        " . $this->db_table . ".team_id != :team_id
                        AND
                        " . $this->competition_table . ".competition_id = :competition_id
                        AND
                        " . $this->gameweek_table . ".season_id = :season_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }




    // --- FOR CUP GROUP STAGE GAMES ---

    // these functions are for the cup group stage games. they'll be used for cup standings
    
    /**
     * This function gets the men's team number of wins in the group stage of season's cup competition.
     * This will also be used in the calculation of points and other stats in a table for a cup competition.
     * @return string JSON object containing the number of wins by a team in the group stage of a cup competition in a season
     */
    public function getTeamNumberOfGroupStageWinsInSeasonCompetition() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_wins
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        JOIN
            " . $this->cup_game_table . " ON " . $this->game_table . ".game_id = " . $this->cup_game_table . ".game_id
        JOIN
            " . $this->stage_table . " ON " . $this->cup_game_table . ".stage_id = " . $this->stage_table . ".stage_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) > (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) > (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND ". $this->gameweek_table . ".season_id = :season_id
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()
            AND " .$this->game_table.".competition_id = :competition_id
            AND " . $this->stage_table . ".stage_name = 'Group Stage'
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);
        $stmt->bindParam(":season_id", $this->season_id);
        $stmt->bindParam(":competition_id", $this->competition_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

       /**
     * This function gets the men's team number of losses in the group stage of a season's cup competition.
     * This will also be used in the calculation of points and other stats in a table for a cup competition.
     * @return string JSON object containing the number of losses by a team in the group stage of a cup competition in a season
     */
    public function getTeamNumberOfGroupStageLossesInSeasonCompetition() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_losses
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        JOIN
            " . $this->cup_game_table . " ON " . $this->game_table . ".game_id = " . $this->cup_game_table . ".game_id
        JOIN
            " . $this->stage_table . " ON " . $this->cup_game_table . ".stage_id = " . $this->stage_table . ".stage_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) < (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) < (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND ". $this->gameweek_table . ".season_id = :season_id
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()
            AND " .$this->game_table.".competition_id = :competition_id
            AND " . $this->stage_table . ".stage_name = 'Group Stage'
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);
        $stmt->bindParam(":season_id", $this->season_id);
        $stmt->bindParam(":competition_id", $this->competition_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

     /**
     * This function gets the men's team number of draws in the group stage of a season's competition.
     * This will also be used in the calculation of points and other stats in a table for a cup competition.
     * @return string JSON object containing the number of draws by a team in the group stage of a cup competition in a season
     */
    public function getTeamNumberOfGroupStageDrawsInSeasonCompetition() {
       $sqlQuery = "
       SELECT
        COUNT(*) AS no_of_draws
        FROM
            " . $this->game_table . "
        JOIN
            " . $this->gameweek_table . " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
        JOIN
            " . $this->cup_game_table . " ON " . $this->game_table . ".game_id = " . $this->cup_game_table . ".game_id
        JOIN
            " . $this->stage_table . " ON " . $this->cup_game_table . ".stage_id = " . $this->stage_table . ".stage_id
        WHERE
            (
                (home_id = :team_id AND (
                    (SELECT COUNT(*) 
                    FROM " . $this->db_table . "
                    WHERE team_id = home_id
                    AND game_id = " . $this->game_table . ".game_id
                    ) = (
                        SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                    ))
                )
                OR
                (
                    away_id = :team_id AND (
                        (SELECT COUNT(*) 
                        FROM " . $this->db_table . "
                        WHERE team_id = away_id
                        AND game_id = " . $this->game_table . ".game_id
                        ) = (
                            SELECT COUNT(*) 
                            FROM " . $this->db_table . "
                            WHERE team_id = home_id
                            AND game_id = " . $this->game_table . ".game_id
                        )
                    )
                )
            )
            AND ". $this->gameweek_table . ".season_id = :season_id
            AND " . $this->gameweek_table . ".gameweek_date < CURDATE()
            AND " .$this->game_table.".competition_id = :competition_id
            AND " . $this->stage_table . ".stage_name = 'Group Stage'
        ";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":team_id", $this->team_id);
        $stmt->bindParam(":season_id", $this->season_id);
        $stmt->bindParam(":competition_id", $this->competition_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }

    /**
    * Get all goals scored by a team in the group stage of a cup competition in a season.
    * This will also be used in the calculation of points and other stats in a table for a cup competition.
    * @return string json object of the number of goals scored by a team in the group stage of a season's cup competition
    */
    public function getGroupStageGoalsByTeamInSeasonCompetition() {
        $sqlQuery = "SELECT
                            COUNT(*) as total_goals_scored
                        FROM
                            ". $this->db_table ."
                        JOIN ".$this->team_table. " ON " . $this->db_table . ".team_id = " . $this->team_table . ".team_id
                        JOIN ".$this->player_table. " ON " . $this->db_table . ".player_id = " . $this->player_table . ".player_id
                        JOIN ".$this->game_table. " ON ". $this->db_table .".game_id = ". $this->game_table .".game_id
                        JOIN ".$this->gameweek_table. " ON ". $this->game_table .".gameweek_id = ". $this->gameweek_table .".gameweek_id
                        JOIN ".$this->season_table. " ON ". $this->gameweek_table .".season_id = ". $this->season_table .".season_id
                        JOIN ".$this->competition_table. " ON ". $this->game_table .".competition_id = ". $this->competition_table .".
                        competition_id
                        JOIN ".$this->cup_game_table. " ON ". $this->game_table .".game_id = ". $this->cup_game_table .".game_id
                        JOIN ".$this->stage_table. " ON " .$this->cup_game_table. ".stage_id = ".$this->stage_table.".stage_id
                        WHERE
                        ".$this->db_table.".team_id = :team_id
                        AND
                        ". $this->competition_table .".competition_id = :competition_id
                        AND 
                        ". $this->season_table .".season_id = :season_id
                        AND
                        ".$this->stage_table.".stage_name = 'Group Stage'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
    }

     /**
     * Get all goals conceded by a men's team in the group stage of a cup competition in a season.
     * This will also be used in the calculation of points and other stats in a table for a cup competition.
     * @return string json object of the number of goals conceded by a team in a season's cup competition
     */
    public function getGroupStageGoalsConcededByTeamInSeasonCompetition () {
            $sqlQuery = "SELECT
                        COUNT(*) as total_goals_conceded
                        FROM
                            " . $this->db_table . "
                        JOIN ".$this->game_table. " ON " . $this->db_table . ".game_id = " . $this->game_table . ".game_id
                        JOIN ".$this->gameweek_table. " ON " . $this->game_table . ".gameweek_id = " . $this->gameweek_table . ".gameweek_id
                        JOIN ".$this->competition_table. " ON " . $this->game_table . ".competition_id = " . $this->competition_table . ".competition_id
                        JOIN ".$this->cup_game_table. " ON ". $this->game_table .".game_id = ". $this->cup_game_table .".game_id
                        JOIN ".$this->stage_table. " ON " .$this->cup_game_table. ".stage_id = ".$this->stage_table.".stage_id
                        WHERE
                        (" . $this->game_table . ".away_id = :team_id OR " . $this->game_table . ".home_id = :team_id)
                        AND
                        " . $this->db_table . ".team_id != :team_id
                        AND
                        " . $this->competition_table . ".competition_id = :competition_id
                        AND
                        " . $this->gameweek_table . ".season_id = :season_id
                        AND
                        " . $this->stage_table . ".stage_name = 'Group Stage'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(":team_id", $this->team_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
            $stmt->bindParam(":season_id", $this->season_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }




    // --- DELETE FUNCTIONS ---
    /**
     * This function deletes a goal from the Goal table. It gives a 204 response code if successful.
     * @return boolean true if goal was successfully deleted or, false if not
     */
    public function deleteGoal () {
        $sqlQuery = "DELETE FROM 
                    ". $this->db_table .
                    " WHERE goal_id = :goal_id";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":goal_id", $this->goal_id);

        if ($stmt->execute()) {
            http_response_code(204);
            return true;
        }
        return false;
    }


    


  
       
    }
?>