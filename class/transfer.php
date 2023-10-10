<?php

    class Transfer {
    // Connection
        private $conn;
        // Table
        private $db_table = "Transfer";
        // Columns
        public $transfer_id;
        public $transfer_date;
        public $transferred_player_id;
        public $new_team_id;
        public $prev_team_id;
        public $transfer_type;
        public $team_id;

        // helper tables
        private $player_table = "Player";
        private $team_table = "Team";

        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---
        /**
         * This function adds a transfer
         */
        public function addTransfer () {
            $sqlQuery = "
                        INSERT INTO
                        ". $this->db_table ."
                        (
                            transfer_date,
                            transferred_player_id,
                            new_team_id,
                            prev_team_id,
                            transfer_type
                        )
                        VALUES
                        (
                            :transfer_date,
                            :transferred_player_id,
                            :new_team_id,
                            :prev_team_id,
                            :transfer_type
                        )";
            
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":transfer_date", $this->transfer_date);
            $stmt->bindParam(":transferred_player_id", $this->transferred_player_id);
            $stmt->bindParam(":new_team_id", $this->new_team_id);
            $stmt->bindParam(":prev_team_id", $this->prev_team_id);
            $stmt->bindParam(":transfer_type", $this->transfer_type);

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
        public function getAllTransfers() {
            $sqlQuery = "
                        SELECT 
                            ". $this->db_table .".transfer_id,
                            ". $this->db_table .".transfer_date,
                            ". $this->db_table .".transferred_player_id,
                            ". $this->db_table .".new_team_id,
                            ". $this->db_table .".prev_team_id,
                            ". $this->db_table .".transfer_type,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            new_team.team_name AS new_team_name,
                            prev_team.team_name AS prev_team_name
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON ". $this->db_table .".transferred_player_id = ". $this->player_table .".player_id
                        JOIN ". $this->team_table ." AS new_team ON ". $this->db_table .".new_team_id = new_team.team_id
                        JOIN ". $this->team_table ." AS prev_team ON ". $this->db_table .".prev_team_id = prev_team.team_id
                        ORDER BY
                            transfer_date DESC";
            
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no formation is found
            return "";

        }

        /**
         * This function gets all the transfers of a particular player
         */
        public function getTransfersByPlayerId () {
            $sqlQuery = "
                        SELECT 
                            ". $this->db_table .".transfer_id,
                            ". $this->db_table .".transfer_date,
                            ". $this->db_table .".transferred_player_id,
                            ". $this->db_table .".new_team_id,
                            ". $this->db_table .".prev_team_id,
                            ". $this->db_table .".transfer_type,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            new_team.team_logo_url AS new_team_logo_url,
                            prev_team.team_logo_url AS prev_team_logo_url,
                            prev_team.team_name_abbrev AS prev_team_name_abbrev,
                            new_team.team_name_abbrev AS new_team_name_abbrev,
                            new_team.team_name AS new_team_name,
                            prev_team.team_name AS prev_team_name
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON ". $this->db_table .".transferred_player_id = ". $this->player_table .".player_id
                        JOIN ". $this->team_table ." AS new_team ON ". $this->db_table .".new_team_id = new_team.team_id
                        JOIN ". $this->team_table ." AS prev_team ON ". $this->db_table .".prev_team_id = prev_team.team_id
                        WHERE 
                            transferred_player_id = :transferred_player_id
                        ORDER BY
                        transfer_date DESC";
                        


                        
                
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":transferred_player_id", $this->transferred_player_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no news item is found
            return "";
        }

        /**
         * This function gets all the transfers of a particular team
         */
        public function getTransfersByTeamId () {
            $sqlQuery = "
                        SELECT 
                            ". $this->db_table .".transfer_id,
                            ". $this->db_table .".transfer_date,
                            ". $this->db_table .".transferred_player_id,
                            ". $this->db_table .".new_team_id,
                            ". $this->db_table .".prev_team_id,
                            ". $this->db_table .".transfer_type,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            new_team.team_name AS new_team_name,
                            prev_team.team_name AS prev_team_name
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON ". $this->db_table .".transferred_player_id = ". $this->player_table .".player_id
                        JOIN ". $this->team_table ." AS new_team ON ". $this->db_table .".new_team_id = new_team.team_id
                        JOIN ". $this->team_table ." AS prev_team ON ". $this->db_table .".prev_team_id = prev_team.team_id
                        WHERE 
                            new_team_id = :team_id
                        OR
                            prev_team_id = :team_id";
                
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":team_id", $this->team_id);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no news item is found
            return "";
        }

        /**
         * This function gets all transfers of type, "Loan"
         */
        public function getLoans () {
            $sqlQuery = "
                        SELECT 
                            ". $this->db_table .".transfer_id,
                            ". $this->db_table .".transfer_date,
                            ". $this->db_table .".transferred_player_id,
                            ". $this->db_table .".new_team_id,
                            ". $this->db_table .".prev_team_id,
                            ". $this->db_table .".transfer_type,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            new_team.team_name AS new_team_name,
                            prev_team.team_name AS prev_team_name
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON ". $this->db_table .".transferred_player_id = ". $this->player_table .".player_id
                        JOIN ". $this->team_table ." AS new_team ON ". $this->db_table .".new_team_id = new_team.team_id
                        JOIN ". $this->team_table ." AS prev_team ON ". $this->db_table .".prev_team_id = prev_team.team_id
                        WHERE 
                            transfer_type = 'Loan'";
                
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no news item is found
            return "";
        }

        /**
         * This function gets all transfers of type, "Permanent"
         */
        public function getPermanents () {
            $sqlQuery = "
                        SELECT 
                            ". $this->db_table .".transfer_id,
                            ". $this->db_table .".transfer_date,
                            ". $this->db_table .".transferred_player_id,
                            ". $this->db_table .".new_team_id,
                            ". $this->db_table .".prev_team_id,
                            ". $this->db_table .".transfer_type,
                            ". $this->player_table .".fname,
                            ". $this->player_table .".lname,
                            new_team.team_name AS new_team_name,
                            prev_team.team_name AS prev_team_name
                        FROM
                            ". $this->db_table ."
                        JOIN ". $this->player_table ." ON ". $this->db_table .".transferred_player_id = ". $this->player_table .".player_id
                        JOIN ". $this->team_table ." AS new_team ON ". $this->db_table .".new_team_id = new_team.team_id
                        JOIN ". $this->team_table ." AS prev_team ON ". $this->db_table .".prev_team_id = prev_team.team_id
                        WHERE 
                            transfer_type = 'Permanent'";

            
                
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no news item is found
            return "";
        }


        // --- DELETE FUNCTIONS ---
        /**
         * This function deletes a transfer
         */
        public function deleteTransfer () {
            $sqlQuery = "
                        DELETE FROM
                        ". $this->db_table ."
                        WHERE 
                            transfer_id = :transfer_id
                        ";
            
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":transfer_id", $this->transfer_id);

            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }

    }
?>