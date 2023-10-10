<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/user.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow only GET REQUESTS
    header('Access-Control-Allow-Methods: POST');

    // content type
    header('Content-Type: application/json');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // create a new user object
    $user = new User($db);

    // get hashed user id, password reset token, password and confirm password from request body 
    $user->hashed_user_id = $_POST['uid'];
    $user->password_reset_token = $_POST['password_reset_token'];
    $user->user_password = $_POST['password'];


    echo $user->resetPassword();

?>