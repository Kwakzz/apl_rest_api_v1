<?php
    class Gameweek {
        // Connection
        private $conn;
        // Table
        private $db_table = "Gameweek";
        // Gameweek table
        // Columns
        public $gameweek_id;
        public $gameweek_number;
        public $gameweek_date;
        public $season_id;

        
        // Db connection
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function adds a gameweek to a season. It gives a 201 response code if successful.
         * @return boolean true if gameweek is added, otherwise false.
         */
        public function addGameweek () {

            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                        (
                            gameweek_number,
                            gameweek_date,
                            season_id
                        )
                    VALUES
                        (   
                            :gameweek_number,
                            :gameweek_date,
                            :season_id
                        )";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->gameweek_date=htmlspecialchars(strip_tags($this->gameweek_date));
        
            // bind data
            $stmt->bindParam(":gameweek_number", $this->gameweek_number);  
            $stmt->bindParam(":gameweek_date", $this->gameweek_date);
            $stmt->bindParam(":season_id", $this->season_id);

            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }

        // --- READ FUNCTIONS ---

        /**
         * Get gameweeks for season in order of most recent. 
         * @return string json object containing all gameweeks if successful, otherwise empty string.
         */
        public function getSeasonGameweeks () {
            $sqlQuery = "SELECT *
            FROM " .$this->db_table. "
            WHERE 
            season_id = :season_id
            ORDER BY 
            gameweek_date DESC";

            // prepare query statement
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":season_id", $this->season_id);

            // execute query
            $stmt->execute();

            // get all gameweeks
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";

        }

        // --- UPDATE FUNCTIONS ---

        /**
         * This function edits a gameweek. It gives a 200 response code if successful.
         * @return boolean true if gameweek is edited, otherwise false.
         */
        public function editGameweek () {
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        gameweek_number = :gameweek_number,
                        gameweek_date = :gameweek_date,
                        season_id = :season_id
                    WHERE 
                        gameweek_id = :gameweek_id";
            
            // prepare query
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->gameweek_date=htmlspecialchars(strip_tags($this->gameweek_date));
        
            // bind data
            $stmt->bindParam(":gameweek_number", $this->gameweek_number);  
            $stmt->bindParam(":gameweek_date", $this->gameweek_date);
            $stmt->bindParam(":season_id", $this->season_id);
            $stmt->bindParam(":gameweek_id", $this->gameweek_id);

            if ($stmt->execute()) {
                http_response_code(200);
                return true;
            }
            return false;
        }

        // --- DELETE FUNCTIONS ---

        /**
         * This function deletes a gameweek. It gives a 204 response code if successful.
         * @return boolean true if gameweek is deleted, otherwise false.
         */
        public function deleteGameweek () {
            $sqlQuery = "DELETE FROM " 
            .$this->db_table. 
            " WHERE gameweek_id = ?";

            // prepare query
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->gameweek_id=htmlspecialchars(strip_tags($this->gameweek_id));

            // bind data
            $stmt->bindParam(1, $this->gameweek_id);

            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }

        

       
    }
?>