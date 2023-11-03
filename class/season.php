<?php

    class Season {
    // Connection
        private $conn;
        // Table
        private $db_table = "Season";
        // Columns
        public $season_id;
        public $season_name;
        public $start_date;
        public $end_date;

        // helper tables
        private $competition_table = "Competition";
        private $season_competition_table = "SeasonCompetition";

        // helper columns
        public $competition_id;
        public $competition_name;
        public $gender;
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function adds a season.
         * It gives a 201 response code if successful.
         * @return boolean true if season is added, otherwise false.
         */
        public function addSeason () {
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                        (season_name, start_date, end_date)
                    VALUES
                        (:season_name, :start_date, :end_date)";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->season_name=htmlspecialchars(strip_tags($this->season_name));
            $this->start_date=htmlspecialchars(strip_tags($this->start_date));
            $this->end_date=htmlspecialchars(strip_tags($this->end_date));
        
            // bind data
            $stmt->bindParam(":season_name", $this->season_name);
            $stmt->bindParam(":start_date", $this->start_date);
            $stmt->bindParam(":end_date", $this->end_date);
        
            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }

        /**
         * This function adds a competition for a season. It gives a 201 response code if successful.
         * For example, the 2022/2023 season has the following competitions: Ashesi FA Cup. This makes Ashesi FA Cup a competition for the 2022/2023 season. This function adds Ashesi FA Cup as a competition for the 2022/2023 season.
         * It can be for men or women.
         * @uses getCompetitionId()
         * @return boolean true if competition is added to a season, otherwise false.
         */
        public function addSeasonCompetition () {

            $this->getCompetitionId();

            $sqlQuery = "INSERT INTO
                        ". $this->season_competition_table ."
                        (season_id, competition_id)
                    VALUES
                        (:season_id, :competition_id)";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // bind data
            $stmt->bindParam(":season_id", $this->season_id);
            $stmt->bindParam(":competition_id", $this->competition_id);
        
            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;

        }

        // --- READ FUNCTIONS ---


        /**
         * This function gets all seasons in order of most recent to least recent.
         * @return string json object containing all seasons if successful, otherwise empty string.
         */
        public function getSeasons () {
            $sqlQuery = "SELECT * FROM
                        ". $this->db_table ."
                        ORDER BY
                        start_date DESC";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no season is found
            return "";
        }

        /**
         * This function gets the competitions that were played in a season.
         * @return string json object containing all competitions played in a season if successful, otherwise empty string.
         */
        public function getSeasonCompetitions () {
            $sqlQuery = "SELECT                
                        FROM " .
                        $this->season_competition_table.
                        ", " .
                        $this->competition_table."
                        WHERE 
                        season_id = :season_id";
            $stmt = $this->conn->prepare($sqlQuery);
            $this->season_id = htmlspecialchars(strip_tags($this->season_id));
            $stmt->bindParam(':season_id', $this->season_id);
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
        }

        /**
         * This functions gets the men's competitions being played in a season.
         * @return string json object containing all men's competitions played in a season if successful, otherwise empty string.
         */
        public function getMensSeasonCompetitions () {
            $sqlQuery = "SELECT *                
                        FROM " .
                        $this->season_competition_table.
                        " JOIN " .
                        $this->competition_table." ON ".$this->season_competition_table. ".competition_id = ".$this->competition_table.".competition_id
                        WHERE
                        season_id = :season_id
                        AND ".$this->competition_table. ".gender = 'Male'";
            $stmt = $this->conn->prepare($sqlQuery);
            $this->season_id = htmlspecialchars(strip_tags($this->season_id));
            $stmt->bindParam(':season_id', $this->season_id);
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
        }

        /**
         * This functions gets the women's competitions being played in a season.
         * @return string json object containing all women's competitions played in a season if successful, otherwise empty string.
         */
        public function getWomensSeasonCompetitions () {
            $sqlQuery = "SELECT *                
                        FROM " .
                        $this->season_competition_table.
                        " JOIN " .
                        $this->competition_table." ON ".$this->season_competition_table. ".competition_id = ".$this->competition_table.".competition_id
                        WHERE
                        season_id = :season_id
                        AND ".$this->competition_table. ".gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);
            $this->season_id = htmlspecialchars(strip_tags($this->season_id));
            $stmt->bindParam(':season_id', $this->season_id);
            $stmt->execute();
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no competition is found
            return "";
        }

        /**
         * This function gets a competition's id by its name and gender. Competition names are duplicated for different genders.
         * @return string json object containing the competition id if successful, otherwise empty string.
         */
        private function getCompetitionId () {
            $sqlQuery = "SELECT competition_id FROM
                        ". $this->competition_table ."
                        WHERE
                        competition_name = :competition_name
                        AND
                        gender = :gender";

            // prepare data
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->competition_name=htmlspecialchars(strip_tags($this->competition_name));
            $this->gender=htmlspecialchars(strip_tags($this->gender));

            // bind data
            $stmt->bindParam(':competition_name', $this->competition_name);
            $stmt->bindParam(':gender', $this->gender);

            // execute query
            $stmt->execute();

            // get data
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                $this->competition_id = $dataRow['competition_id'];
            }
            // if no competition with such an is found
            return "";

        }


        // --- UPDATE FUNCTIONS ---
        
        /**
         * This function edits a season. It gives a 200 response code if successful.
         * @return boolean true if season is edited, otherwise false.
         */
        public function editSeason () {
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        season_name = :season_name,
                        start_date = :start_date,
                        end_date = :end_date
                    WHERE 
                        season_id = :season_id";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->season_name=htmlspecialchars(strip_tags($this->season_name));
            $this->start_date=htmlspecialchars(strip_tags($this->start_date));
            $this->end_date=htmlspecialchars(strip_tags($this->end_date));
        
            // bind data
            $stmt->bindParam(":season_name", $this->season_name);
            $stmt->bindParam(":start_date", $this->start_date);
            $stmt->bindParam(":end_date", $this->end_date);
            $stmt->bindParam(":season_id", $this->season_id);
        
            if ($stmt->execute()) {
                http_response_code(200);
                return true;
            }
            return false;
        }


        // --- DELETE FUNCTIONS ---

        /**
         * This function deletes a competition from a season. It gives a 204 response code if successful.
         * @return boolean true if competition is deleted from a season, otherwise false.
         */
        public function deleteSeasonCompetition () {
            $sqlQuery = "DELETE FROM " . 
            $this->season_competition_table . " 
            WHERE season_id = ?
            AND competition_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $stmt->bindParam(1, $this->season_id);
            $stmt->bindParam(2, $this->competition_id);
        
            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }


        /**
         * This function deletes a season. It gives a 204 response code if successful.
         * @return boolean true if season is deleted, otherwise false.
         */
        public function deleteSeason () {
            $sqlQuery = "DELETE FROM " . 
            $this->db_table . " 
            WHERE season_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->season_id=htmlspecialchars(strip_tags($this->season_id));
        
            $stmt->bindParam(1, $this->season_id);
        
            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }


       
    }
?>