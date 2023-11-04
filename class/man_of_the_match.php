<?php

    class ManOfTheMatch {
    // Connection
        private $conn;
        // Table
        private $db_table = "ManOfTheMatch";
        // Columns
        public $player_id;
        public $game_id;

        // helper tables
        private $player_table = "Player";
        private $game_table = "Game";
        private $team_table = "Team";
        
    
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // CREATE FUNCTIONS

        /**
         * This function sets a player as the man of the match for a game.
         * @return bool true if the man of the match is set successfully and false if not.
         */
        public function setManOfTheMatch () {

            $sqlQuery = "
            INSERT INTO
                " . $this->db_table . "
            (
                player_id = :player_id,
                game_id = :game_id
            )
            VALUES
            (
                :player_id,
                :game_id
            )";
                

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->player_id = htmlspecialchars(strip_tags($this->player_id));
            $this->game_id = htmlspecialchars(strip_tags($this->game_id));

            // bind data
            $stmt->bindParam(":player_id", $this->player_id);
            $stmt->bindParam(":game_id", $this->game_id);

            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }

        // READ FUNCTIONS

        /**
         * This function gets the man of the match for a game.
         * @return string a JSON object containing details about the man of the match if successful, otherwise an empty string.
         */
        public function getManOfTheMatch () {

            $sqlQuery = "SELECT 
                        ". $this->db_table . ".player_id,
                        ". $this->db_table . ".game_id,
                        ". $this->player_table . ".fname,
                        ". $this->player_table . ".lname
                        FROM
                        ". $this->db_table . "
                        JOIN ". $this->player_table . " ON ". $this->db_table . ".player_id = ". $this->player_table . ".player_id
                        JOIN ". $this->game_table . " ON ". $this->db_table . ".game_id = ". $this->game_table . ".game_id
                        JOIN ". $this->team_table . " ON ". $this->player_table . ".team_id = ". $this->team_table . ".team_id
                        WHERE
                        ". $this->game_table . ".game_id = :game_id";

            $stmt = $this->conn->prepare($sqlQuery);
            
            // bind data
            $stmt->bindParam(":game_id", $this->game_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                // Encode the result as JSON
                return json_encode($dataRow);
            }
            // if no man of the match is found
            return "";


        }

        // UPDATE FUNCTIONS

        /**
         * This function changes the man of the match for a game.
         * It's done by the admin.
         * It updates the player_id column in the ManOfTheMatch table.
         * It gives a response code of 200 if successful.
         * @return bool true if man of the match is changed successfully, otherwise false.
         */
        public function changeManOfTheMatch () {
                
                $sqlQuery = "UPDATE
                            ". $this->db_table . "
                            SET
                            player_id = :player_id
                            WHERE
                            game_id = :game_id";
    
                $stmt = $this->conn->prepare($sqlQuery);
    
                // bind data
                $stmt->bindParam(":player_id", $this->player_id);
                $stmt->bindParam(":game_id", $this->game_id);
    
                if ($stmt->execute()) {
                    http_response_code(200);
                    return true;
                }
                return false;
        }


    }