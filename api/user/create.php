<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/user.php';
    require_once '../../config/email_auth.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../../vendor/autoload.php';

    // new PHPMailer object
    require_once '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once '../../vendor/phpmailer/phpmailer/src/SMTP.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow only POST REQUESTS
    header('Access-Control-Allow-Methods: POST');

    // content type
    header('Content-Type: application/json');

    // connect to database
    $database = new Database();
    $db = $database->getConnection();

    // get request body
    $requestBody = file_get_contents('php://input');
    // decode request body as PHP array
    $requestBody = json_decode($requestBody);

    // create a new user object
    $user = new User($db);

    // get parameters from sign up form and assign them to user object properties
    $user->email_address = $requestBody->email_address;
    $user->user_password = $requestBody->user_password;
    $user->fname = $requestBody->fname;
    $user->lname = $requestBody->lname;
    $user->mobile_number = $requestBody->mobile_number;
    $user->gender = $requestBody->gender;
    $user->date_of_birth = $requestBody->date_of_birth;

    // sign in user
    $result = $user->createUser();

    echo $result;

    if ($result) {
        $mail = new PHPMailer(true);
        setupSMTP($mail);
        setupEmailForAccountActivation($mail, $user->email_address, $user->fname, $user->hashed_user_id, $user->activation_code);
        $mail->send();
    }
    

    
    

?>