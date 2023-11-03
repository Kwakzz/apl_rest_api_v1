<?php

    class NewsItem {
    // Connection
        private $conn;
        // Table
        private $db_table = "NewsItem";
        // Columns
        public $news_item_id;
        public $title;
        public $subtitle;
        public $content;
        public $cover_pic;
        public $time_published;

        // helper columns
        public $news_tag_id;
        // helper tables
        private $news_tag_table = "NewsTag";

        // helper variables
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- CREATE FUNCTIONS ---

        /**
         * This function creates a news item. It gives a 201 response code if successful.
         * @return boolean true if news item is created, otherwise false.
         */
        public function createNewsItem() {
            $sqlQuery = "
                        INSERT INTO
                        ". $this->db_table ."
                        (
                            title,
                            subtitle,
                            content,
                            cover_pic,
                            time_published,
                            news_tag_id
                        )
                        VALUES
                        (
                            :title,
                            :subtitle,
                            :content,
                            :cover_pic,
                            :time_published,
                            :news_tag_id
                        )";
            
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->cover_pic=htmlspecialchars(strip_tags($this->cover_pic));

            // bind data
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":subtitle", $this->subtitle);
            $stmt->bindParam(":content", $this->content);
            $stmt->bindParam(":cover_pic", $this->cover_pic);
            $stmt->bindParam(":time_published", $this->time_published);
            $stmt->bindParam(":news_tag_id", $this->news_tag_id);

            if ($stmt->execute()) {
                http_response_code(201);
                return true;
            }
            return false;
        }


        // --- READ FUNCTIONS ---

        /**
         * This function gets a news item by id. It also gets the tags associated with the news item.
         * @return string JSON encoded object containing the news item and its tag if found, otherwise an empty string.
         */
        public function getNewsItemById () {
            $sqlQuery = "SELECT *
                        FROM
                        ". $this->db_table ."
                        JOIN
                            ". $this->news_tag_table ."
                        ON
                            ". $this->db_table .".news_tag_id = ". $this->news_tag_table .".news_tag_id
                        WHERE 
                            ". $this->db_table .".news_item_id = :news_item_id
                        ";
                
            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(":news_item_id", $this->news_item_id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                // Encode the result as JSON
                return json_encode($dataRow);
            }
            // if no news item is found
            return "";
            

        }

        /**
        * This function gets all news items and sorts them by time published.
        * @return string JSON encoded object containing all news items if found, otherwise an empty string.
        */ 
        public function getAllNewsItems () {
            $sqlQuery = "SELECT *
                        FROM
                        ". $this->db_table."
                        JOIN
                            ". $this->news_tag_table."
                        ON
                            ". $this->db_table.".news_tag_id = ". $this->news_tag_table.".news_tag_id
                        ORDER BY time_published DESC";
                
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no news items are found
            return "";
        }

        /**
         * This function gets all the news item tags.
         * @return string JSON encoded object containing all news item tags if found, otherwise an empty string.
         */
        public function getNewsItemTags () {
            $sqlQuery = "SELECT *
                        FROM
                        ". $this->news_tag_table."
                        ORDER BY news_tag_name ASC";
                
            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // if data row is not empty
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no news items are found
            return "";
        }


            
        // --- UPDATE FUNCTIONS ---
        /**
        * This function edits a news item. It gives a 200 response code if successful.
        * @return boolean true if news item is edited, otherwise false.
        */
        public function editNewsItem () {
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                        SET
                            title = :title,
                            subtitle = :subtitle,
                            content = :content,
                            cover_pic = :cover_pic,
                            news_tag_id = :news_tag_id
                        WHERE 
                            news_item_id = :news_item_id";
                
            $stmt = $this->conn->prepare($sqlQuery);
            
            // bind data
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":subtitle", $this->subtitle);
            $stmt->bindParam(":content", $this->content);
            $stmt->bindParam(":cover_pic", $this->cover_pic);
            $stmt->bindParam(":news_item_id", $this->news_item_id);
            $stmt->bindParam(":news_tag_id", $this->news_tag_id);

            if ($stmt->execute()) {
                http_response_code(200);
                return true;
            }
            return false;
        }

        // --- DELETE FUNCTIONS ---
        /**
         * This function deletes a news item. It gives a 204 response code if successful.
         * @return boolean true if news item is deleted, otherwise false.
         */
        public function deleteNewsItem () {
            $sqlQuery = "DELETE FROM
                        ". $this->db_table ."
                    WHERE 
                        news_item_id = :news_item_id";
                    
            $stmt = $this->conn->prepare($sqlQuery);
        
            // bind data
            $stmt->bindParam(":news_item_id", $this->news_item_id);  

            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }



    }
?>