<?php

    class Foul {
    // Connection
        private $conn;
        // Table
        private $db_table = "Foul";
        // Columns
        public $foul_id;
        public $player_id;
        public $game_id;

        // helper tables
        private $player_table = "Player";
        private $game_table = "Game";
        private $red_card_table = "RedCard";
        private $yellow_card_table = "YellowCard";
        
    
        
        // constructor
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }
    }