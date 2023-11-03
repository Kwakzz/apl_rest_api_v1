<?php

    require_once '../../config/email_auth.php';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    class User {
        // Connection
        protected $conn;
        // Table
        protected $db_table = "AppUser";
        // Columns
        public $user_id;
        public $hashed_user_id;
        public $fname;
        public $lname;
        public $email_address;
        public $user_password;
        public $gender;
        public $date_of_birth;
        public $mobile_number;
        public $is_admin;
        public $is_active = false;
        public $activation_code;
        private $activation_expiry;
        public $activated_at;
        public $created_at;
        public $updated_at;
        public $last_login_at;
        public $team_id;
        public $user_category;


        // Helper columns
        public $team_name;
        private $reset_token_id;
        public $password_reset_token;
        public $password_reset_expiry;


        // Helper tables
        private $db_table_team = "Team";
        private $password_reset_table = "PasswordResetTemp";

        // Db connection
        /**
         * This function creates a connection to the database. It's the constructor.
         */
        public function __construct($db){
            $this->conn = $db;
        }

        // --- HELPER FUNCTIONS ---

        /**
         * This function gets a user's id
         */
        private function getUserId(){
            $sqlQuery = "SELECT user_id
             FROM " . $this->db_table . 
             " WHERE email_address = :email_address";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->user_id = $dataRow['user_id'];
                return $this->user_id;
            }
            // Return -1 if no user id found 
            return -1;
        }

        /**
         * This function inserts a user's hashed id into the database. The hashed id is used for account activation. We don't want to expose the user's id in the activation link.
         */
        private function insertHashedUserId(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        hashed_user_id = :hashed_user_id
                    WHERE 
                        user_id = :user_id";
            $this->hashed_user_id = password_hash($this->user_id, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':hashed_user_id', $this->hashed_user_id);
            $stmt->bindParam(':user_id', $this->user_id);
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }

        /**
         * This function checks if a user has a hashed id
         */
        private function checkIfUserHasHashedId(){
            $sqlQuery = "SELECT hashed_user_id
             FROM " . $this->db_table . 
             " WHERE email_address = :email_address";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->hashed_user_id = $dataRow['hashed_user_id'];
                return true;
            }
            // Return false if no user id found 
            return false;
        }


        /**
         * Check if a user exists in the database.
         * @return bool true if user exists, false if user does not exist
         */
         public function checkIfUserExists(){
            $sqlQuery = "SELECT user_id
             FROM " . $this->db_table . 
             " WHERE email_address = :email_address";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                return true;
            } 
            return false;
         }

         /**
          * This function gets a user's email address using their hashed id
          */
        private function getEmailAddressByHashedId(){
            $sqlQuery = "SELECT email_address
            FROM " . $this->db_table . 
            " WHERE hashed_user_id = :hashed_user_id";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':hashed_user_id', $this->hashed_user_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->email_address = $dataRow['email_address'];
                return $this->email_address;
            }
            // Return an empty string if no email address found 
            return "";
        }

        /**
         * This function gets a user's first name using their email address
         */
        private function getFirstNameByEmail(){
            $sqlQuery = "SELECT fname
            FROM " . $this->db_table . 
            " WHERE email_address = :email_address";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->fname = $dataRow['fname'];
                return $this->fname;
            }
            // Return an empty string if no first name found 
            return "";
        }
            
        

         /**
          * This function generates an expiry date for the activation code. It is generated when the user signs up and expires after 24 hours. 
          */
        private function generateActivationExpiry(){
            $date = new DateTime();
            $date->add(new DateInterval('PT24H'));
            return $date->format('Y-m-d H:i:s');
        }  

        /**
         * This function generates an expiry date for the password reset token. It is generated when the user requests a password reset and expires after 1 hour. 
         */
        private function generatePasswordResetExpiry(){
            $date = new DateTime();
            $date->add(new DateInterval('PT1H'));
            return $date->format('Y-m-d H:i:s');
        }                   

        /**
         * This function gets the activation code's expiry time. It is used to check if the activation code has expired.
         */
        private function getActivationExpiryTime(){
            $sqlQuery = "SELECT
                            activation_expiry
                        FROM
                            ". $this->db_table ."
                        WHERE 
                            hashed_user_id = :hashed_user_id
                        ";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':hashed_user_id', $this->hashed_user_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->activation_expiry = $dataRow['activation_expiry'];
                return $this->activation_expiry;
            } 
            // if no such user exists
            return "";
            
        }

        /**
         * This function sets the user's activation status to true. It is called when a user clicks on the activation link and the activation code is correct and has not expired.
         */
        private function setActivationStatus(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        is_active = true,
                        activated_at = NOW()
                    WHERE 
                        hashed_user_id = :hashed_user_id";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':hashed_user_id', $this->hashed_user_id);
            if ($stmt->execute()) {    
                return true;
            }
            return false;
        }
         
         
        /**
         * This function activates a user's account. It checks if the activation code is correct and if it has expired. If the activation code is correct and has not expired, the user's account is activated. If the activation code is incorrect, the user's account is deleted. If the activation code has expired, the user's account is deleted.
         */
        public function activateAccount () {

            // check if account is already activated
            if ($this->checkActivationStatus()) {
                return "Account already activated";
            }

            // check if activation code has expired
            if (strtotime('now') > strtotime($this->getActivationExpiryTime())) {
                $this->deleteUserByHashedId();
                return "Activation code expired";
            }

            // check if activation code is correct
            if ($this->checkActivationCode($this->activation_code)) {
                $this->setActivationStatus();
                return "Account activated. Return to the APL app and sign in";
            }

            // if activation code is incorrect
            $this->deleteUserByHashedId();

            return "Activation failed";                      
        }   

        /**
         * This function compares the activation code sent to the user with the one in the database
         */
        private function checkActivationCode($activation_code){
            $sqlQuery = "SELECT
                            ".$this->db_table.".activation_code
                        FROM
                            ". $this->db_table ."
                        WHERE 
                            hashed_user_id = :hashed_user_id
                        AND is_active = false
                        ";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':hashed_user_id', $this->hashed_user_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                // if the activation code matches
                if ($dataRow['activation_code'] == $activation_code) {
                    return true;
                }
                // if the activation code does not match
                return false;                
            }
            // if no such user exists
            return false;
        }

        /**
         * This function checks a user's activation status
         */
        private function checkActivationStatus(){
            $sqlQuery = "SELECT
                            is_active
                        FROM
                            ". $this->db_table ."
                        WHERE 
                            hashed_user_id = :hashed_user_id
                        ";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':hashed_user_id', $this->hashed_user_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            // check if the user exists and is active
            if ($dataRow && array_key_exists('is_active', $dataRow)) {
                // if the user is active
                if ($dataRow['is_active'] == 1) {
                    return true;
                }
                // if the user is not active
                return false;
            } 
            // if no such user exists
            return false;
            
        }

        /**
         * This function gets the password reset token's expiry time. It is used to check if the password reset token has expired.
         */
        private function getPasswordResetExpiryTime(){
            $sqlQuery = "SELECT
                            ".$this->password_reset_table.".password_reset_expiry
                        FROM
                            ". $this->password_reset_table ."
                        WHERE 
                            email_address = :email_address
                        ORDER BY
                            reset_token_id DESC  
                        LIMIT 1; 
                        ";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->execute();
            
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->password_reset_expiry = $dataRow['password_reset_expiry'];
                return $this->password_reset_expiry;
            } 
            // if no such user exists
            return "";
        }

        /**
         * This function compares the password reset token sent to the user with the one in the database
         */
        private function checkPasswordResetToken($password_reset_token){
            $sqlQuery = "SELECT
                            ".$this->password_reset_table.".password_reset_token
                        FROM
                            ". $this->password_reset_table ."
                        WHERE 
                            email_address = :email_address
                        ORDER BY
                            reset_token_id DESC
                        LIMIT 1;
                        ";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                // if the password reset token matches
                if ($dataRow['password_reset_token'] == $password_reset_token) {
                    return true;
                }
                // if the password reset token does not match
                return false;                
            }
            // if no such user exists
            return false;
        }

        /**
         * This function checks if the user has an existing valid password token. It's called in another function to prevent users from requesting multiple password reset tokens.
         */

        /**
         * This function calls on generateActivationCode() in email_auth.php to generate a password reset token. 
         * It also calls on the generatePasswordResetExpiry() function to generate the password reset token's expiry time. 
         * This function is called when the user sends a request (containing their email address as a JSON) by clicking on the "Password Reset" button. 
         * It also checks if the user has a hashed id. If the user doesn't have one, it creates one. This hashed id and the reset token are attached to the password reset link sent to the user's email address. 
         * Finally, this function checks if a user is active. If the user is active, it sends the password reset link to the user's email address. If the user is not active, it responds with a code 401 and returns false.
         */
        public function insertPasswordResetDetailsIntoDb () {

            // check if user has hashed id
            if (!$this->checkIfUserHasHashedId()) {
                $this->insertHashedUserId();
            }

            $this->getFirstNameByEmail();

            // check if user is active
            if (!$this->checkActivationStatus()) {
                http_response_code(401);
                return false;
            }

            // generate password reset token
            $this->password_reset_token = generateToken();

            // generate password reset token expiry
            $this->password_reset_expiry = $this->generatePasswordResetExpiry();

            // insert password reset token and expiry into database
            $sqlQuery = "INSERT INTO
                        ". $this->password_reset_table .
                        "(email_address, password_reset_token, password_reset_expiry)
                        VALUES(:email_address, :password_reset_token, :password_reset_expiry)";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->email_address=htmlspecialchars(strip_tags($this->email_address));
            $this->password_reset_token=htmlspecialchars(strip_tags($this->password_reset_token));
            $this->password_reset_expiry=htmlspecialchars(strip_tags($this->password_reset_expiry));
        
            // bind data
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->bindParam(':password_reset_token', $this->password_reset_token);
            $stmt->bindParam(':password_reset_expiry', $this->password_reset_expiry);

            
            if($stmt->execute()){
                // set http response code to 201 created
                http_response_code(201);
                return true;
            }
            else {
                http_response_code(400);
                return false;
            }
            
        }

        /**
         * This function resets a user's password. It checks if the password reset token is correct and if it has expired. If the password reset token is correct and has not expired, the user's password is reset. If the password reset token is incorrect, the user isn't allowed to reset their password. If the password reset token has expired, the user isn't allowed to reset their password.
         */
        public function resetPassword () {

            // the email address is used to get the password reset token and password reset token expiry in the getPasswordResetExpiryTime() and checkPasswordResetToken() functions
            $this->getEmailAddressByHashedId();

            // check if password reset token has expired
            if (strtotime('now') > strtotime($this->getPasswordResetExpiryTime())) {
                return "Password reset token expired";
            }

            // check if password reset token is correct
            if ($this->checkPasswordResetToken($this->password_reset_token)) {
                if ($this->setPassword()) {
                    return "Password reset successful. Return to the APL app and sign in";
                }
                return "Password reset failed";
            }

            // if password reset token is incorrect
            return "Password reset failed";
        }
             


        // --- CREATE FUNCTIONS ---

        /**
         * This function enables a user to sign up
         */
        public function signUp(){

            // check if there are inactive users and delete them
            if ($this->checkInactiveUsers()) {
                $this->deleteInactiveUsers();
            }
            

            $this->activation_code = generateToken();
            $this->activation_expiry = $this->generateActivationExpiry();

            $sqlQuery = "INSERT INTO
                        ". $this->db_table .
                        "(fname, lname, gender, date_of_birth, email_address, user_password, mobile_number, activation_code, activation_expiry)
                        VALUES(:fname, :lname, :gender, :date_of_birth, :email_address, :user_password, :mobile_number, :activation_code, :activation_expiry)";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));
            $this->email_address=htmlspecialchars(strip_tags($this->email_address));
            $this->gender=htmlspecialchars(strip_tags($this->gender));
            $this->date_of_birth=htmlspecialchars(strip_tags($this->date_of_birth));
            $this->mobile_number=htmlspecialchars(strip_tags($this->mobile_number));
            $this->activation_code=htmlspecialchars(strip_tags($this->activation_code));
            $this->activation_expiry=htmlspecialchars(strip_tags($this->activation_expiry));
            // encrypt password
            $this->user_password=htmlspecialchars(password_hash($this->user_password, PASSWORD_DEFAULT));

        
            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':user_password', $this->user_password);
            $stmt->bindParam(':mobile_number', $this->mobile_number);
            $stmt->bindParam(':activation_code', $this->activation_code);
            $stmt->bindParam(':activation_expiry', $this->activation_expiry);

            if ($this->checkIfUserExists()){
                http_response_code((409));
                return false;
            }
        
            if($stmt->execute()){
                $this->getUserId();
                $this->insertHashedUserId();
                // set http response code to 201 created
                http_response_code(201);
                return true;
            }

            
            return false;
            
        }


        // --- READ FUNCTIONS ---


        /**
         * This function enables a user to sign in
         */
        public function signIn(){

            // check if there are inactive users and delete them
            if ($this->checkInactiveUsers()){
                $this->deleteInactiveUsers();
            }

            // get user details
            $sqlQuery = "SELECT
                        ".$this->db_table.".user_id, 
                        ".$this->db_table.".fname, 
                        ".$this->db_table.".lname,
                        user_password,
                        email_address,
                        ".$this->db_table.".date_of_birth, 
                        ".$this->db_table.".gender,
                        mobile_number,
                        is_active,
                        is_admin,
                        ".$this->db_table.".team_id
                      FROM
                        ". $this->db_table ."
                      WHERE 
                       email_address = :email_address";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(":email_address", $this->email_address);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($dataRow) {
                // decrypt password in database and compare with user input. Also check if user is active
                if(password_verify($this->user_password, $dataRow['user_password']) && $dataRow['is_active'] == 1) {
                    http_response_code(200);
                    $this->updateLoginTime();
                    return json_encode($dataRow);
                }
                // if password is incorrect or user is inactive
                else {
                    http_response_code(401);
                    return "";
                }
            // if no such user exists
            } 
            http_response_code(404);
            return "";
          
        }

         /**
         *  This function checks if there are inactive users whose activation links have expired
        */
        public function checkInactiveUsers(){
            $sqlQuery = "SELECT * 
                        FROM " . $this->db_table . "
                        WHERE is_active = 0 
                        AND activation_expiry < NOW()";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            // get data
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                return true;
            }
            // if no such user exists
            return false;
        }    

        /**
         * This function gets a team's id using the team name
         */
        private function getTeamId(){
            $sqlQuery = "SELECT
                        team_id
                      FROM
                        ". $this->db_table_team ."
                    WHERE 
                       team_name = :team_name
                       ";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':team_name', $this->team_name);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                $this->team_id = $dataRow['team_id'];
                return $this->team_id;
            }
            // if no such team exists
            return -1;
        }


        /**
         * This function checks returns a player with a given first and last name from the user table
         */
        public function getUserByName() {
            $sqlQuery = "SELECT
            *
            FROM
            ". $this->db_table ."
            WHERE
            fname = :fname
            AND
            lname = :lname";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));

            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);

            // execute query
            $stmt->execute();

            // get data
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dataRow) {
                return json_encode($dataRow);
            }
            // if no such user exists
            return "";

        }

      
        // --- UPDATE FUNCTIONS ---

         /**
         * This function allows a user to set their team id
         * After the user signs up, they can pick a team
         * The email address is sent to the page where the user picks a team
         * So, the parameters for the query are the email address and the team name
         */
        public function setUserTeamId(){

            // get team id
            $this->getTeamId();

            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        team_id = :team_id,
                        updated_at = NOW() 
                    WHERE 
                        email_address = :email_address";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->email_address=htmlspecialchars(strip_tags($this->email_address));
        
            // bind data
            $stmt->bindParam(':team_id', $this->team_id);
            $stmt->bindParam(':email_address', $this->email_address);
        
            if($stmt->execute()){
                http_response_code(200);
                return true;
            }
            return false;
        }
  


        /**
         * This function allows the user to change their phone number.
         * Other details cannot be changed.
         */
        public function changeMobileNumber(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        mobile_number = :mobile_number,
                        updated_at = NOW() 
                    WHERE 
                        user_id = ?";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->mobile_number=htmlspecialchars(strip_tags($this->mobile_number));
        
            // bind data
            $stmt->bindParam(1, $this->mobile_number);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        /**
         * This function updates a user's password
         */
        public function setPassword(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        user_password = :user_password,
                        updated_at = NOW() 
                    WHERE 
                        email_address = :email_address";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->user_password=htmlspecialchars(strip_tags($this->user_password));
        
            // encrypt password
            $this->user_password=htmlspecialchars(password_hash($this->user_password, PASSWORD_DEFAULT));
        
            // bind data
            $stmt->bindParam(':user_password', $this->user_password);
            $stmt->bindParam(':email_address', $this->email_address);
        
            if($stmt->execute()){
                http_response_code(200);
                return true;
            }
            return false;
        }

        /**
         * This function updates a user's login time
         */
        private function updateLoginTime () {
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        last_login_at = NOW()
                    WHERE 
                        email_address = :email_address";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(':email_address', $this->email_address);
            if ($stmt->execute()) {    
                return true;
            }
            return false;
        }
         
        // --- DELETE FUNCTIONS ---

        /**
         * This function allows a user to delete their account using their id
         */
        public function deleteUserById(){
            $sqlQuery = "DELETE FROM " . 
            $this->db_table . 
            " WHERE user_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->user_id);
            if($stmt->execute()){
                http_response_code(204);
                return true;
            }
            return false;
        }

        /**
         * This function allows a user to delete their account using their hashed id
         */
        public function deleteUserByHashedId(){
            $sqlQuery = "DELETE FROM " . 
            $this->db_table . 
            " WHERE hashed_user_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $stmt->bindParam(1, $this->hashed_user_id);
        
            if($stmt->execute()){
                http_response_code(204);
                return true;
            }
            return false;
        }

       

        /**
         * This function deletes the accounts of inactive users whose activation links have expired
         * It runs whenever a user tries to log in or sign up
         * However, it only deletes the accounts of users who have not created a player or coach profile
         */
        public function deleteInactiveUsers() {
            $sqlQuery = "DELETE FROM " .
                $this->db_table .
                " WHERE is_active = 0 
                AND activation_expiry < NOW()";
            
            $stmt = $this->conn->prepare($sqlQuery);
            if ($stmt->execute()) {
                http_response_code(204);
                return true;
            }
            return false;
        }

        // --- ADMIN FUNCTIONS ---

        // --- CREATE FUNCTIONS ---

        
        /**
         * This function adds a new user to the database.
         * This done by the admin.
         */
        public function addUser(){

            // check if there are inactive users and delete them
            if ($this->checkInactiveUsers()) {
                $this->deleteInactiveUsers();
            }

            // get team id from team name
            $this->getTeamId();
            

            $this->activation_code = generateToken();
            $this->activation_expiry = $this->generateActivationExpiry();

            $sqlQuery = "INSERT INTO
                        ". $this->db_table .
                        "(fname, lname, gender, date_of_birth, email_address, user_password, mobile_number, activation_code, activation_expiry, is_admin, team_id)
                        VALUES(:fname, :lname, :gender, :date_of_birth, :email_address, :user_password, :mobile_number, :activation_code, :activation_expiry, :is_admin, :team_id)";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));
            $this->email_address=htmlspecialchars(strip_tags($this->email_address));
            $this->gender=htmlspecialchars(strip_tags($this->gender));
            $this->date_of_birth=htmlspecialchars(strip_tags($this->date_of_birth));
            $this->mobile_number=htmlspecialchars(strip_tags($this->mobile_number));
            $this->activation_code=htmlspecialchars(strip_tags($this->activation_code));
            $this->activation_expiry=htmlspecialchars(strip_tags($this->activation_expiry));

            // encrypt password
            $this->user_password=htmlspecialchars(password_hash($this->user_password, PASSWORD_DEFAULT));

        
            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':email_address', $this->email_address);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':user_password', $this->user_password);
            $stmt->bindParam(':mobile_number', $this->mobile_number);
            $stmt->bindParam(':activation_code', $this->activation_code);
            $stmt->bindParam(':activation_expiry', $this->activation_expiry);
            $stmt->bindParam(':is_admin', $this->is_admin);
            $stmt->bindParam(':team_id', $this->team_id);

            if ($this->checkIfUserExists()){
                http_response_code((409));
                return false;
            }
        
            if($stmt->execute()){
                $this->getUserId();
                $this->insertHashedUserId();
                http_response_code(201);
                return true;
            }

            return false;
            
        }

        // --- READ FUNCTIONS ---

        /**
         * This function gets all regular users from the database. 
         * Regular users don't have admin status.
         */
        public function getAllRegularUsers() {
            $sqlQuery ="SELECT *
                        FROM " . $this->db_table . "
                        WHERE is_admin = 0";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no such user exists
            return false;
        }

        public function getAllAdmins() {
            $sqlQuery ="SELECT *
                        FROM " . $this->db_table . "
                        WHERE is_admin = 1";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();

            // get data
            $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($dataRows) {
                return json_encode($dataRows);
            }
            // if no such user exists
            return false;
        }

        // --- UPDATE FUNCTIONS ---

        /**
         * This function edits a user's details.
         * This is done by the admin.
         */
        public function editUser () {

            // get team id from team name
            $this->getTeamId();

            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        fname = :fname,
                        lname = :lname,
                        mobile_number = :mobile_number,
                        is_active = :is_active,
                        is_admin = :is_admin,
                        team_id = :team_id
                    WHERE
                        user_id = :user_id";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->fname=htmlspecialchars(strip_tags($this->fname));
            $this->lname=htmlspecialchars(strip_tags($this->lname));
            $this->mobile_number=htmlspecialchars(strip_tags($this->mobile_number));

            // bind data
            $stmt->bindParam(':fname', $this->fname);
            $stmt->bindParam(':lname', $this->lname);
            $stmt->bindParam(':mobile_number', $this->mobile_number);
            $stmt->bindParam(':is_active', $this->is_active);
            $stmt->bindParam(':is_admin', $this->is_admin);
            $stmt->bindParam(':team_id', $this->team_id);
            $stmt->bindParam(':user_id', $this->user_id);

            if($stmt->execute()){
                http_response_code(200);
                return true;
            }
            return false;
        }

        
        

        /**
         * This function activates or deactivates a user's account
         */
        public function activateOrDeactivateUser() {

            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        is_active = :is_active
                    WHERE
                        user_id = :user_id";

            $stmt = $this->conn->prepare($sqlQuery);

            // bind data
            $stmt->bindParam(':is_active', $this->is_active);
            $stmt->bindParam(':user_id', $this->user_id);

            if($stmt->execute()){
                return true;
            }
            return false;
        }


        // --- DELETE FUNCTIONS ---

        /**
         * This function deletes a user's account
         */
        public function deleteUser() {

            $sqlQuery = "DELETE FROM " . 
            $this->db_table . 
            " WHERE user_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->user_id);
            if($stmt->execute()){
                return true;
            }
            return false;
        }


        

        



        



    }
?>