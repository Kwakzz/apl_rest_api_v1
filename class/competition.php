<?php

    class Competition {
    // Connection
        private $conn;
        // Table
        private $db_table = "Competition";
        // Columns
        public $competition_id;
        public $competition_name;
        public $competition_abbrev;
        public $gender;

    
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // READ FUNCTIONS

        /**
         * This function retrieves all competitions.
         * Competition names in the database are duplicated for each gender.
         * But this function only returns one of each competition name.
         */
        public function getDistinctCompNames () {
            $sqlQuery = "
            SELECT DISTINCT competition_name 
            FROM " . $this->db_table;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function retrieves all competitions.
         */
        public function getCompetitions () {
            $sqlQuery = "
            SELECT * 
            FROM " . $this->db_table;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function gets men's competitions
         */
        public function getMensCompetitions () {
            $sqlQuery = "
            SELECT * 
            FROM " . $this->db_table.
            " WHERE gender = 'Male'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }

        /**
         * This function gets women's competitions
         */
        public function getWomensCompetitions () {
            $sqlQuery = "
            SELECT * 
            FROM " . $this->db_table.
            " WHERE gender = 'Female'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            return "";
        }
       
    }
?>