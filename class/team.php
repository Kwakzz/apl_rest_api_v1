<?php

    class Team {
    // Connection
        private $conn;
        // Table
        private $db_table = "Team";
        // Columns
        public $team_id;
        public $team_name;
        public $team_logo_url;
        public $team_name_abbrev;

        // helper tables
        private $player_table = "Player";
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function adds a team
         */
        public function addTeam () {
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                        (
                            team_name,         
                            team_name_abbrev
                        )
                    VALUES
                        (
                            :team_name,
                            :team_name_abbrev
                        )";
                        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->team_name=htmlspecialchars(strip_tags($this->team_name));
            $this->team_name_abbrev=htmlspecialchars(strip_tags($this->team_name_abbrev));
        
            // bind data
            $stmt->bindParam(":team_name", $this->team_name);
            $stmt->bindParam(":team_name_abbrev", $this->team_name_abbrev);
        
            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }

        // --- READ FUNCTIONS ---

        /**
         * This function gets a particular team
         */
        public function getTeam () {
            $sqlQuery = "SELECT
                        *
                        FROM
                        ". $this->db_table ."
                        WHERE 
                        team_name = :team_name";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':team_name', $this->team_name);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            // if data row is not empty
            if ($dataRow) {
                return $dataRow;
            }
            // if no team is found
            return "";
            
        }

        /**
         * This function gets teams with only male players.
         * No player in the player table belonging to these teams is a female
         */
        public function getMensTeams () {
            $sqlQuery = "SELECT *
            FROM ". $this->db_table.
            " WHERE EXISTS 
            (SELECT * FROM ". $this->player_table. "
            WHERE ". 
            $this->db_table. ".team_id = ". $this->player_table. ".team_id
            AND "
            .$this->player_table. ".gender = 'Male')";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no team is found
            return "";
            
        }

        /**
         * This function gets teams with only female players.
         * No player in the player table belonging to these teams is a male
         */
        public function getWomensTeams () {
            $sqlQuery = "SELECT *
            FROM ". $this->db_table.
            " WHERE EXISTS 
            (SELECT * FROM ". $this->player_table. "
            WHERE ". 
            $this->db_table. ".team_id = ". $this->player_table. ".team_id
            AND "
            .$this->player_table. ".gender = 'Female')";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no team is found
            return "";
            
        }


        /**
         * This function gets all teams
         */
        public function getAllTeams () {
            $sqlQuery = "SELECT
                        *
                        FROM
                        ". $this->db_table;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $dataRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // if data row is not empty
            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no team is found
            return "";
        }

        /**
         * This function gets the players in a team.
         */
        public function getPlayersInATeam() {

            $sqlQuery = "SELECT * 
            FROM ". $this->player_table. 
            " WHERE team_id = :team_id";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':team_id', $this->team_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no player is found
            return "";         
        }

        /**
         * This function gets the male active players in a team.
         */
         public function getActiveMalePlayersInATeam() {

            $sqlQuery = "SELECT * 
            FROM ". $this->player_table. 
            " WHERE 
            team_id = :team_id
            AND
            gender = 'Male'
            AND
            is_retired = 0
            ";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':team_id', $this->team_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no player is found
            return "";         
        }

        /**
         * This function gets the active female players in a team
         */
         public function getActiveFemalePlayersInATeam() {

            $sqlQuery = "SELECT * 
            FROM ". $this->player_table. 
            " WHERE 
            team_id = :team_id
            AND
            gender = 'Female'
            AND
            is_retired = 0
            ";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':team_id', $this->team_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no player is found
            return "";         
        }

        /**
         * This function gets the retired male players in a team.
         */
        public function getRetiredMalePlayersInATeam() {

            $sqlQuery = "SELECT * 
            FROM ". $this->player_table. 
            " WHERE 
            team_id = :team_id
            AND
            gender = 'Male'
            AND
            is_retired = 1
            ";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':team_id', $this->team_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no player is found
            return "";         
        }

        /**
         * This function gets the retired female players in a team.
         */
        public function getRetiredFemalePlayersInATeam() {

            $sqlQuery = "SELECT * 
            FROM ". $this->player_table. 
            " WHERE 
            team_id = :team_id
            AND
            gender = 'Female'
            AND
            is_retired = 1
            ";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':team_id', $this->team_id);

            // execute query
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no player is found
            return "";         
        }

        // --- UPDATE FUNCTIONS ---

        /**
         * This function edits a team
         */
        public function editTeam () {
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        team_name = :team_name,
                        team_name_abbrev = :team_name_abbrev
                    WHERE 
                        team_id = :team_id";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->team_name=htmlspecialchars(strip_tags($this->team_name));
            $this->team_name_abbrev=htmlspecialchars(strip_tags($this->team_name_abbrev));
            $this->team_id=htmlspecialchars(strip_tags($this->team_id));
        
            // bind data
            $stmt->bindParam(":team_name", $this->team_name);
            $stmt->bindParam(":team_name_abbrev", $this->team_name_abbrev);
            $stmt->bindParam(":team_id", $this->team_id);
        
            if ($stmt->execute()) {
                http_response_code(200);
                return true;
            }
            return false;
        }

        // --- DELETE FUNCTIONS ---

        /**
         * This function deletes a team using the team_id
         */
        public function deleteTeam () {
            $sqlQuery = "DELETE FROM ". 
            $this->db_table .
            " WHERE team_id = ?";

            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->team_id=htmlspecialchars(strip_tags($this->team_id));
        
            // bind data
            $stmt->bindParam(1, $this->team_id);
        
            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }


    }
?>