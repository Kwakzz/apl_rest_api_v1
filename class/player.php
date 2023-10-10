<?php

    class Player {
        // Connection
        private $conn;

        // Table
        private $db_table = "Player";
        // Columns
        public $player_id;
        public $user_id;
        public $fname;
        public $lname;
        public $height;
        public $weight;
        public $position_name;
        public $position_id;
        public $is_retired;
        public $year_group;
        public $gender;
        public $date_of_birth;
        public $team_id;

        // Helper Column
        public $team_name;

        
        // Helper tables
        private $user_table = "AppUser";
        private $player_position_table = "PlayerPosition";
        private $goal_table = "Goal";
        private $game_table = "Game";
        private $starting_xi_table = "StartingXI";
        private $starting_xi_player_table = "StartingXIPlayer";
        private $team_table = "Team";
        private $gameweek_table = "Gameweek";
        private $season_table = "Season";
        private $competition_table = "Competition";


        // Helper variables
        public $competition_name;
        public $competition_id;
        public $season_name;
        public $season_id;
        public $game_id;

        // Helper functions

        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- HELPER FUNCTIONS --- 

        /**
         * This function gets a player's position id using the position name
         */
        public function getPositionId () {
            $sqlQuery = "SELECT
                        position_id
                        FROM " .
                        $this->player_position_table.
                        " WHERE 
                        position_name = :position_name";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':position_name', $this->position_name);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            // if data row is not empty
            if ($dataRow){
                $this->position_id = $dataRow['position_id'];
                return $this->position_id;
            }
            // if no position is found
            return -1;
        }

        /**
         * This function gets a player's team id using the team name
         */
        public function getTeamId () {
            $sqlQuery = "SELECT
                        team_id
                        FROM " .
                        $this->team_table.
                        " WHERE 
                        team_name = :team_name";
            $stmt = $this->conn->prepare($sqlQuery);
            $this->team_name = htmlspecialchars(strip_tags($this->team_name));
            $stmt->bindParam(':team_name', $this->team_name);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            // if data row is not empty
            if ($dataRow){
                $this->team_id = $dataRow['team_id'];
                return $this->team_id;
            }
            // if no team is found
            return -1;
        }

     
        /**
         * This function checks if a player has a user account
         */
        public function hasUserAccount () {
            // query to check if player has user account
            $sqlQuery = "SELECT user_id
            FROM ". $this->db_table. " 
            WHERE player_id = :player_id";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':player_id', $this->player_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRow != null){
                return true;
            }
            // if no user account is found
            return false;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function creates a player.
         */
         public function addPlayer() {
            $this->getPositionId();
            $this->getTeamId();

            // query to insert record
            $sqlQuery = "INSERT INTO " . $this->db_table .
            "(fname, lname, date_of_birth, gender, height, weight, position_id, is_retired, year_group, team_id)
            VALUES 
            (:fname, :lname, :date_of_birth, :gender, :height, :weight, :position_id, :is_retired, :year_group, :team_id)";
            ;

            // prepare query
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->fname = htmlspecialchars(strip_tags($this->fname));
            $this->lname = htmlspecialchars(strip_tags($this->lname));
            $this->gender = htmlspecialchars(strip_tags($this->gender));

            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':height', $this->height);
            $stmt->bindParam(':weight', $this->weight);
            $stmt->bindParam(':position_id', $this->position_id);
            $stmt->bindParam(':is_retired', $this->is_retired);
            $stmt->bindParam(':year_group', $this->year_group);
            $stmt->bindParam(':team_id', $this->team_id);

            // execute query
            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;

         }

         // --- READ FUNCTIONS ---

         /**
         * This function gets a player's details.
         */
        public function getPlayer() {
            $sqlQuery = "SELECT 
            ". $this->db_table. ".player_id,
            ". $this->db_table. ".fname,
            ". $this->db_table. ".lname,
            ". $this->db_table. ".height,
            ". $this->db_table. ".year_group,
            ". $this->db_table. ".date_of_birth,
            ". $this->db_table. ".gender,
            ". $this->db_table. ".weight,
            ". $this->player_position_table. ".position_name,
            ". $this->team_table. ".team_name
            FROM ". $this->db_table."
            LEFT JOIN ".$this->player_position_table." ON ". $this->db_table. ".position_id = ". $this->player_position_table. ".position_id
            LEFT JOIN ".$this->team_table." ON ". $this->db_table. ".team_id = ". $this->team_table. ".team_id
            WHERE player_id = :player_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':player_id', $this->player_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no player is found
            return "";
        }

       

        /**
         * This function gets all players.
         */
        public function getPlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no player is found
            return "";
            
        }

        /**
         * This function gets all active men's players.
         */
        public function getActiveMensPlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            is_retired = 0
            AND 
            gender = 'Male'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }       
            // if no player is found     
            return "";
        }

        public function getActiveWomensPlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            is_retired = 0
            AND 
            gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }       
            // if no player is found     
            return "";
            
        }

        /**
         * This function gets all retired players
         */
        public function getRetiredPlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE is_retired = 1";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }       
            // if no player is found     
            return "";
            
        }

        /**
         * This function gets retired men's players
         */
        public function getRetiredMensPlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            is_retired = 1
            AND
            gender = 'Male'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }       
            // if no player is found     
            return "";
            
        }

        /**
         * This function gets retired women's players
         */
        public function getRetiredWomensPlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            is_retired = 1
            AND
            gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }       
            // if no player is found     
            return "";
            
        }

        /**
         * This function gets all active players
         */
        public function getActivePlayers() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE is_retired = 0";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no player is found
            return "";
        }

        /**
         * This function gets all male players with user accounts
         */
        public function getMalePlayersWithUserAccounts() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            user_id IS NOT NULL
            AND
            gender = 'Male'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no player is found
            return "";
        }

        /**
         * This function gets all male players without user accounts
         */
        public function getMalePlayersWithoutUserAccounts() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            user_id IS NULL
            AND 
            gender = 'Male'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no player is found
            return "";
        }

        /**
         * This function gets all female players with user accounts
         */
        public function getFemalePlayersWithUserAccounts() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            user_id IS NOT NULL
            AND
            gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no player is found
            return "";
        }

        /**
         * This function gets all female players without user accounts
         */
        public function getFemalePlayersWithoutUserAccounts() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table. 
            " WHERE 
            user_id IS NULL
            AND 
            gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no player is found
            return "";
        }


        /**
         * This function gets a player's user id. 
         * If the player does not have a user account, it returns -1
         */
        public function getUserId() {
            // query to get user id
            $sqlQuery = "SELECT user_id
            FROM ". $this->db_table. " 
            WHERE player_id = :player_id";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':player_id', $this->player_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRow){
                $this->user_id = $dataRow['user_id'];
                return $this->user_id;
            }
            // if no user account is found
            return -1;
        }

        /**
        * This function gets the total number of games a player has played in.
        */
        public function getNumberOfGamesPlayedByPlayer() {
            $sqlQuery = "SELECT COUNT(*) AS no_of_games_played,
            ". $this->db_table. ".player_id,
            ". $this->db_table. ".fname,
            ". $this->db_table. ".lname
            FROM ". $this->db_table. " 
            JOIN ". $this->starting_xi_player_table. " ON ". $this->db_table. ".player_id = ". $this->starting_xi_player_table. ".player_id
            WHERE ".$this->db_table.".player_id = :player_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':player_id', $this->player_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }       
            // if no player is found     
            return "";
        }

        /**
         * This function gets the number of times a player has won a game. It finds the games where the player was in the starting XI and the team scored more goals than the opposition.
         */
        public function getPlayerTotalNumberOfWins() {

        $sqlQuery = "
            SELECT COUNT(*) AS no_of_wins,
                " . $this->db_table . ".player_id,
                " . $this->db_table . ".fname,
                " . $this->db_table . ".lname
            FROM " . $this->db_table . "
            JOIN " . $this->starting_xi_player_table . " ON " . $this->db_table . ".player_id = " . $this->starting_xi_player_table . ".player_id
            JOIN " . $this->starting_xi_table . " ON " . $this->starting_xi_player_table . ".xi_id = " . $this->starting_xi_table . ".xi_id
            JOIN " . $this->game_table . " ON " . $this->starting_xi_table . ".game_id = " . $this->game_table . ".game_id
            JOIN ". $this->gameweek_table. " ON ". $this->game_table. ".gameweek_id = ". $this->gameweek_table. ".gameweek_id
            WHERE (
                (
                    SELECT COUNT(*)
                    FROM " . $this->goal_table . "
                    JOIN " . $this->game_table . " ON " . $this->goal_table . ".game_id = " . $this->game_table . ".game_id
                    WHERE " . $this->goal_table . ".team_id = " . $this->starting_xi_table . ".team_id
                ) > (
                    SELECT COUNT(*)
                    FROM " . $this->goal_table . "
                    JOIN " . $this->game_table . " ON " . $this->goal_table . ".game_id = " . $this->game_table . ".game_id
                    WHERE " . $this->goal_table . ".team_id != " . $this->starting_xi_table . ".team_id
                )
            )
            AND " . $this->db_table . ".player_id = :player_id
            AND " . $this->gameweek_table. ".gameweek_date < CURDATE()
        ";


       $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(":player_id", $this->player_id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataRow) {
            return json_encode($dataRow);
        }

        return "";
    }


         // --- UPDATE FUNCTIONS ---        

        /**
         * This function updates a player's linked user account details to match their player profile
         * After a player updates their profile, this function is called to update their linked user account
         * details to match their player profile
         * If the player does not have a user account, it returns -1
         * This ensures data integrity
         */
        public function updateLinkedUserAccountDetails () {

            if ($this->hasUserAccount()) {
                $this->getUserId();

                $sqlQuery = "UPDATE
                        ". $this->user_table. "
                        SET
                        fname = :fname,
                        lname = :lname,
                        gender = :gender,
                        date_of_birth = :date_of_birth
                        WHERE
                        user_id = :user_id";

                $stmt = $this->conn->prepare($sqlQuery);

                // sanitize
                $this->fname=htmlspecialchars(strip_tags($this->fname));
                $this->lname=htmlspecialchars(strip_tags($this->lname));
                $this->gender=htmlspecialchars(strip_tags($this->gender));

                // bind data
                $stmt->bindParam(':fname', $this->fname);
                $stmt->bindParam(':lname', $this->lname);
                $stmt->bindParam(':gender', $this->gender);
                $stmt->bindParam(':date_of_birth', $this->date_of_birth);
                $stmt->bindParam(':user_id', $this->user_id);

                // execute query
                if ($stmt->execute()) {
                    return true;
                }
                return false;
            }
            return -1;
        }


       
        /**
         * This function updates a player's details.
         */
        public function editPlayer() {

            $this->getPositionId();
            $this->getTeamId();


            $sqlQuery = $sqlQuery = "UPDATE 
                    ". $this->db_table. "
                    SET
                    fname = :fname,
                    lname = :lname,
                    team_id = :team_id,
                    height = :height,
                    weight = :weight,
                    gender = :gender,
                    date_of_birth = :date_of_birth,
                    position_id = :position_id,
                    year_group = :year_group,
                    is_retired = :is_retired
                    WHERE
                    player_id = :player_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));
            $this->gender=htmlspecialchars(strip_tags($this->gender));

        
            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':team_id', $this->team_id);
            $stmt->bindParam(':height', $this->height);
            $stmt->bindParam(':weight', $this->weight);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':position_id', $this->position_id);
            $stmt->bindParam(':year_group', $this->year_group);
            $stmt->bindParam(':is_retired', $this->is_retired);
            $stmt->bindParam(':player_id', $this->player_id);
        
            if($stmt->execute()){
                $this->updateLinkedUserAccountDetails();
                http_response_code(200);
                return true;
            } 
            return false;
        }

        /**
         * This function changes a player's team.
         */
        public function changeTeam() {
            $sqlQuery = "UPDATE 
                    ". $this->db_table. "
                    SET
                    team_id = :team_id
                    WHERE
                    player_id = :player_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->team_id=htmlspecialchars(strip_tags($this->team_id));
        
            // bind data
            $stmt->bindParam(':team_id', $this->team_id);
            $stmt->bindParam(':player_id', $this->player_id);
        
            if($stmt->execute()){
                return true;
            } 
            return false;
        }
        
        /**
         * This function deletes a player.
         */
        function deletePlayer() {
            $sqlQuery = "DELETE FROM " . 
            $this->db_table . 
            " WHERE player_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->player_id);
            if($stmt->execute()){
                http_response_code(204);
                return true;
            }
            return false;
        }

        

    }

?>