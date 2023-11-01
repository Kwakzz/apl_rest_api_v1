<?php

    class Coach {
        // Connection
        private $conn;

        // Table
        private $db_table = "Coach";
        // Columns
        public $coach_id;
        public $fname;
        public $lname;
        public $is_retired;
        public $year_group;
        public $gender;
        public $date_of_birth;
        public $team_id;

        // Helper Column
        public $team_name;

        
        // Helper tables
        private $team_table = "Team";


        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }


        // CREATE FUNCTIONS
        /**
         * This function adds a coach.
         */
         public function addCoach() {
            $this->getTeamId();

            // query to insert record
            $sqlQuery = "INSERT INTO " . $this->db_table .
            "(fname, lname, date_of_birth, gender, is_retired, year_group, team_id)
            VALUES (:fname, :lname, :date_of_birth, :gender, :is_retired, :year_group, :team_id)";
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

        // READ FUNCTIONS
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
         * This function gets a coach's details
         */
        public function getCoach () {
            $sqlQuery = "SELECT 
            ".$this->db_table.".coach_id,
            ".$this->db_table.".fname,
            ".$this->db_table.".lname,
            ".$this->db_table.".date_of_birth,
            ".$this->db_table.".year_group,
            ".$this->db_table.".gender,
            ".$this->team_table.".team_name
            FROM ". $this->db_table. " 
            JOIN ". $this->team_table. " ON ". $this->db_table. ".team_id = ". $this->team_table. ".team_id
            WHERE coach_id = :coach_id";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(':coach_id', $this->coach_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no coach is found
            return "";
        }
        
    

        /**
         * This function gets all coaches.
         */
        public function getCoaches() {
            $sqlQuery = "SELECT * 
            FROM ". $this->db_table;

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no coach is found
            return "";
            
        }

    

        
        // UPDATE FUNCTIONS


        /**
         * This function updates a coach's details.
         */
        public function editCoach() {

            $this->getTeamId();


            $sqlQuery = $sqlQuery = "UPDATE 
                    ". $this->db_table. "
                    SET
                    fname = :fname,
                    lname = :lname,
                    team_id = :team_id,
                    gender = :gender,
                    date_of_birth = :date_of_birth,
                    year_group = :year_group,
                    is_retired = :is_retired
                    WHERE
                    coach_id = :coach_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));
            $this->gender=htmlspecialchars(strip_tags($this->gender));

        
            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':team_id', $this->team_id);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':year_group', $this->year_group);
            $stmt->bindParam(':is_retired', $this->is_retired);
            $stmt->bindParam(':coach_id', $this->coach_id);
        
            if($stmt->execute()){
                http_response_code(200);
                return true;
            } 
            return false;
        }

        // DELETE FUNCTIONS

        /**
         * This function deletes a player.
         */
        function deleteCoach() {
            $sqlQuery = "DELETE FROM " . 
            $this->db_table . 
            " WHERE coach_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->coach_id);
            if($stmt->execute()){
                http_response_code(204);
                return true;
            }
            return false;
        }

       

    }

?>