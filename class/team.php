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
        private $player_position_table = "PlayerPosition";
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function adds a team. It gives a 201 response code if successful.
         * It's done by the admin.
         * @return boolean true if team is added, otherwise false.
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
         * This function gets a particular team.
         * @return string JSON encoded string of team if found, empty string otherwise.
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
         * This function gets teams with male players.
         * A men's team is a team with at least one male player.
         * @return string JSON encoded string of men's team if found, empty string otherwise.
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
         * This function gets teams with female players.
         * A men's team is a team with at least one female player.
         * @return string JSON encoded string of women's team if found, empty string otherwise.
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
         * This function gets all teams.
         * @return string JSON encoded string of all teams if found, empty string otherwise.
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
         * This function gets the players in a team, both male and female.
         * @return string JSON encoded string of players in a team if found, empty string otherwise.
         */
        public function getPlayersInATeam() {

            $sqlQuery = "SELECT 
            ". $this->player_table . ".fname,
            ". $this->player_table . ".lname,
            ". $this->player_table . ".player_id,
            ". $this->player_table . ".weight,
            ". $this->player_table . ".height,
            ". $this->player_table . ".year_group,
            ". $this->player_table . ".is_retired,
            ". $this->player_table . ".player_image_url,
            ". $this->player_table . ".gender,
            ". $this->player_table . ".date_of_birth,
            ". $this->player_position_table . ".position_name,
            ". $this->db_table. ".color_code
            FROM ". $this->player_table. 
            " JOIN ". $this->player_position_table. " ON ". $this->player_table. ".position_id = ". $this->player_position_table. ".position_id
            JOIN ". $this->db_table. " ON ". $this->player_table. ".team_id = ". $this->db_table. ".team_id
            WHERE ". $this->db_table. ".team_id = :team_id";

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
         * Active players are players whose is_retired column is 0.
         * @return string JSON encoded string of active male players in a team if found, empty string otherwise.
         */
         public function getActiveMalePlayersInATeam() {

            $sqlQuery = "SELECT 
            ". $this->player_table . ".fname,
            ". $this->player_table . ".lname,
            ". $this->player_table . ".player_id,
            ". $this->player_table . ".weight,
            ". $this->player_table . ".height,
            ". $this->player_table . ".year_group,
            ". $this->player_table . ".is_retired,
            ". $this->player_table . ".player_image_url,
            ". $this->player_table . ".gender,
            ". $this->player_table . ".date_of_birth,
            ". $this->player_position_table . ".position_name,
            ". $this->db_table. ".color_code
            FROM ". $this->player_table. 
            " JOIN ". $this->player_position_table. " ON ". $this->player_table. ".position_id = ". $this->player_position_table. ".position_id
            JOIN ". $this->db_table. " ON ". $this->player_table. ".team_id = ". $this->db_table. ".team_id
            WHERE ". $this->db_table. ".team_id = :team_id
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
         * This function gets the active female players in a team.
         * Active players are players whose is_retired column is 0.
         * @return string JSON encoded string of active female players in a team if found, empty string otherwise.
         */
         public function getActiveFemalePlayersInATeam() {

            $sqlQuery = "SELECT 
            ". $this->player_table . ".fname,
            ". $this->player_table . ".lname,
            ". $this->player_table . ".player_id,
            ". $this->player_table . ".weight,
            ". $this->player_table . ".height,
            ". $this->player_table . ".year_group,
            ". $this->player_table . ".is_retired,
            ". $this->player_table . ".player_image_url,
            ". $this->player_table . ".gender,
            ". $this->player_table . ".date_of_birth,
            ". $this->player_position_table . ".position_name,
            ". $this->db_table. ".color_code
            FROM ". $this->player_table. 
            " JOIN ". $this->player_position_table. " ON ". $this->player_table. ".position_id = ". $this->player_position_table. ".position_id
            JOIN ". $this->db_table. " ON ". $this->player_table. ".team_id = ". $this->db_table. ".team_id
            WHERE ". $this->db_table. ".team_id = :team_id
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
         * Retired players are players whose is_retired column is 1.
         * @return string JSON encoded string of retired male players in a team if found, empty string otherwise.
         */
        public function getRetiredMalePlayersInATeam() {

            $sqlQuery = "SELECT 
            ". $this->player_table . ".fname,
            ". $this->player_table . ".lname,
            ". $this->player_table . ".player_id,
            ". $this->player_table . ".weight,
            ". $this->player_table . ".height,
            ". $this->player_table . ".year_group,
            ". $this->player_table . ".is_retired,
            ". $this->player_table . ".player_image_url,
            ". $this->player_table . ".gender,
            ". $this->player_table . ".date_of_birth,
            ". $this->player_position_table . ".position_name,
            ". $this->db_table. ".color_code
            FROM ". $this->player_table. 
            " JOIN ". $this->player_position_table. " ON ". $this->player_table. ".position_id = ". $this->player_position_table. ".position_id
            JOIN ". $this->db_table. " ON ". $this->player_table. ".team_id = ". $this->db_table. ".team_id
            WHERE ". $this->db_table. ".team_id = :team_id
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
         * Retired players are players whose is_retired column is 1.
         * @return string JSON encoded string of retired female players in a team if found, empty string otherwise.
         */
        public function getRetiredFemalePlayersInATeam() {

            $sqlQuery = "SELECT
            ". $this->player_table . ".fname,
            ". $this->player_table . ".lname,
            ". $this->player_table . ".player_id,
            ". $this->player_table . ".weight,
            ". $this->player_table . ".height,
            ". $this->player_table . ".year_group,
            ". $this->player_table . ".is_retired,
            ". $this->player_table . ".player_image_url,
            ". $this->player_table . ".gender,
            ". $this->player_table . ".date_of_birth,
            ". $this->player_position_table . ".position_name,
            ". $this->db_table. ".color_code
            FROM ". $this->player_table. 
            " JOIN ". $this->player_position_table. " ON ". $this->player_table. ".position_id = ". $this->player_position_table. ".position_id
            JOIN ". $this->db_table. " ON ". $this->player_table. ".team_id = ". $this->db_table. ".team_id
            WHERE ". $this->db_table. ".team_id = :team_id
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
         * This function edits a team. It gives a 200 response code if successful.
         * It's done by the admin.
         * @return boolean true if team is edited, otherwise false.
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
         * This function deletes a team using the team_id. It gives a 204 response code if successful.
         * It's done by the admin.
         * @return boolean true if team is deleted, otherwise false.
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