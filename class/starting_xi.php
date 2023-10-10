<?php
    class StartingXI {
        // Connection
        private $conn;
        // Tables
        private $db_table = "StartingXI";
        private $starting_xi_player_table = "StartingXIPlayer";
        private $player_table = "Player";
        private $position_table = "PlayerPosition";
        private $game_table = "Game";
        // Starting XI table columns
        public $xi_id;
        public $game_id;
        public $team_id;
        // Starting XI Player table columns
        public $player_id;
        public $position_id;

        
        // Db connection
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function adds a starting XI to a game
         */
        public function addStartingXI () {
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                        (
                            game_id,
                            team_id
                        )
                    VALUES
                        (   
                            :game_id,
                            :team_id
                        )";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // bind data
            $stmt->bindParam(":game_id", $this->game_id);  
            $stmt->bindParam(":team_id", $this->team_id);

            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }


        /**
         * This function adds a player to a starting XI
         */
        public function addPlayerToStartingXI () {
            $sqlQuery = "INSERT INTO
                        ". $this->starting_xi_player_table ."
                        (
                            xi_id,
                            player_id,
                            position_id
                        )
                    VALUES
                        (   
                            :xi_id,
                            :player_id,
                            :position_id
                        )";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // bind data
            $stmt->bindParam(":xi_id", $this->xi_id);  
            $stmt->bindParam(":player_id", $this->player_id);
            $stmt->bindParam(":position_id", $this->position_id);

            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }

        // --- READ FUNCTIONS ---


        /**
         * This function gets the starting XIs for a game
         */
        function getGameStartingXIs () {
            $sqlQuery = "SELECT
                        *
                        FROM ".
                        $this->db_table."
                        WHERE 
                        game_id = :game_id";
                    
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->execute();
            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * This function gets a team's starting XI for a particular game
         */
        function getTeamStartingXI () {
            $sqlQuery = "SELECT
                        *
                        FROM ".
                        $this->db_table."
                        WHERE 
                        game_id = :game_id
                        AND
                        team_id = :team_id";
                    
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->bindParam(':team_id', $this->team_id);
            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dataRow) {
                return json_encode($dataRow);
            }
            return "";
        }

        /**
         * This function gets a team's starting XI players for a particular game
         */
        function getTeamStartingXIPlayers () {
            $sqlQuery = "SELECT
                        ".$this->starting_xi_player_table.".xi_id,
                        ".$this->starting_xi_player_table.".player_id,
                        ".$this->starting_xi_player_table.".position_id,
                        ".$this->player_table.".date_of_birth,
                        ".$this->player_table.".fname,
                        ".$this->player_table.".lname,
                        ".$this->position_table.".position_name
                        FROM
                        ".$this->db_table."
                        JOIN " .$this->starting_xi_player_table. " ON ".$this->db_table.".xi_id = ".$this->starting_xi_player_table.".xi_id
                        JOIN " .$this->position_table. " ON ".$this->starting_xi_player_table.".position_id = ".$this->position_table.".position_id
                        JOIN " .$this->player_table. " ON ".$this->starting_xi_player_table.".player_id = ".$this->player_table.".player_id
                        WHERE ". 
                        $this->starting_xi_player_table.".xi_id = :xi_id
                        ORDER BY
                        ".$this->starting_xi_player_table.".position_id ASC";

                    
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':xi_id', $this->xi_id);

            $stmt->execute();
            
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function gets the starting XIs for a game
         */
        function getGameStartingXIPlayers () {
            $sqlQuery = "SELECT
                        ".$this->starting_xi_player_table.".xi_id,
                        ".$this->starting_xi_player_table.".player_id,
                        ".$this->starting_xi_player_table.".position_id,
                        ".$this->player_table.".fname,
                        ".$this->player_table.".lname,
                        ".$this->position_table.".position_name
                        FROM 
                        ".$this->game_table."
                        JOIN ".$this->starting_xi_player_table." ON ". $this->db_table.".xi_id = ".$this->starting_xi_player_table.".xi_id
                        JOIN ".$this->player_table." ON ".$this->db_table.".player_id = ".$this->player_table.".player_id
                        JOIN ".$this->position_table." ON ". $this->starting_xi_player_table.".position_id = ".$this->position_table.".position_id
                        WHERE ".
                        $this->db_table.".game_id = :game_id
                        ORDER BY
                        ".$this->starting_xi_player_table.".position_id ASC";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':game_id', $this->game_id);
            $stmt->execute();
            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($dataRow);
        }


        // --- DELETE FUNCTIONS ---

        /**
         * This function removes a player from a starting XI
         */
        public function removePlayerFromStartingXI () {
            $sqlQuery = "DELETE FROM
                        ". $this->starting_xi_player_table ."
                    WHERE 
                        xi_id = :xi_id
                    AND
                        player_id = :player_id";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // bind data
            $stmt->bindParam(":xi_id", $this->xi_id);  
            $stmt->bindParam(":player_id", $this->player_id);

            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }

        /**
         * This function deletes a starting XI
         */
        public function deleteStartingXI () {
            $sqlQuery = "DELETE FROM
                        ". $this->db_table ."
                    WHERE 
                        xi_id = :xi_id";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // bind data
            $stmt->bindParam(":xi_id", $this->xi_id);  

            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }
    }

    
