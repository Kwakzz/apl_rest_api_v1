<?php

    class Stage {
    // Connection
        private $conn;
        // Table
        private $db_table = "Stage";
        // Columns
        public $stage_name;
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- READ FUNCTIONS ---

        /**
         * This function gets all stages
         */
        public function getAllStages() {
            $sqlQuery = "SELECT *
                        FROM
                        ". $this->db_table;
            
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no stage is found
            return "";

        }

    }
?>