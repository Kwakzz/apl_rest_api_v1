<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../vendor/autoload.php';

/**
 * This function sets up a PHPMailer object to send email
 */
function setupSMTP($mail)
{
    $mail->SMTPDebug = 2;
    // Set mailer to use SMTP 
    $mail->isSMTP();
    // Specify SMTP server
    $mail->Host = 'smtp.gmail.com';
    // Enable SMTP authentication
    $mail->SMTPAuth = true;
    // SMTP username
    $mail->Username = 'ashesipremierleague01@gmail.com';
    // SMTP password
    $mail->Password = '';
    // Enable TLS encryption, `ssl` also accepted
    $mail->SMTPSecure = 'tls';
    // TCP port to connect to
    $mail->Port = 587;
}

/**
 * This function sets up the email body for account activation
 */
function setupEmailForAccountActivation($mail, $recipient_address, $fname, $hashed_id, $activation_code)
{
    $activation_base_url = "http://3.8.171.188/backend/api/user/activate_account.php?";

    $activationUrl = $activation_base_url . http_build_query(array('uid' => $hashed_id, 'activation_code' => $activation_code));

    // Sender info
    $mail->setFrom('ashesipremierleague01@gmail.com', 'APL');

    // Add a recipient
    $mail->addAddress($recipient_address);

    // Mail subject
    $mail->Subject = "Activate your APL account";

    // Mail body content with HTML styling
    $bodyContent = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            /* Add your custom CSS styles here */
            body {
                font-family: Arial, sans-serif;
                background-color: #f2f2f2;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            h1 {
                color: #333;
            }
            p {
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Welcome to APL</h1>
            <p>Hi ' . $fname . ',</p>
            <p>Thank you for signing up to the APL app. Please click on the link below to activate your account:</p>
            <p><a href="' . $activationUrl . '">Activate Your Account</a></p>
            <p>If you did not sign up to the APL app, please ignore this email.</p>
            <p>Kind regards,</p>
            <p>The APL Team</p>
        </div>
    </body>
    </html>
    ';

    $mail->isHTML(true); // Set email format to HTML
    $mail->Body = $bodyContent;
}

/**
 * This function sets up the email body for password reset
 */
function setupEmailForPasswordReset($mail, $recipient_address, $fname, $hashed_id, $password_reset_token)
{
    $activation_base_url = "http://3.8.171.188/backend/api/user/reset_password_form.php?";

    $password_reset_url = $activation_base_url . http_build_query(array('uid' => $hashed_id, 'password_reset_token' => $password_reset_token));

    // Sender info
    $mail->setFrom('ashesipremierleague01@gmail.com', 'APL');

    // current year
    $current_year = date("Y");

    // Add a recipient
    $mail->addAddress($recipient_address);

    // Mail subject
    $mail->Subject = "Password Reset";

    // Mail body content with HTML styling
    $bodyContent = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td style="padding: 20px 0 30px 0;">
                    <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="border: 1px solid #cccccc; border-collapse: collapse;">
                        <tr>
                            <td align="center" bgcolor="#007bff" style="padding: 40px 0 30px 0; color: #ffffff; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                                APL Password Reset
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                    <tr>
                                        <td style="color: #333333; font-size: 20px;">
                                            Hi ' . $fname . ',
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 20px 0 30px 0; color: #333333; font-size: 16px;">
                                            Thank you for using the APL app. We received a request to reset your password. Please click the button below to reset your password:
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <a href='.$password_reset_url.' style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; font-size: 16px; border-radius: 5px;">Reset Your Password</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 20px 0 30px 0; color: #333333; font-size: 16px;">
                                            If you did not request a password reset, please ignore this email. Your account is secure.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #333333; font-size: 16px;">
                                            Kind regards,<br>
                                            The APL Team
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td bgcolor="#007bff" style="padding: 10px 0 10px 0; color: #ffffff; font-size: 12px; text-align: center; font-family: Arial, sans-serif;">
                                &copy; '.$current_year.' APL. All rights reserved.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>

    ';

    $mail->isHTML(true); // Set email format to HTML
    $mail->Body = $bodyContent;
}


/**
 * This function generates a random code for a user to activate their account. The activation code is 32 bits long. This code is generated when a user signs up to the APL app. It is also generated when a user requests a password reset.
 */
function generateToken()
{
    return bin2hex(random_bytes(32));
}

?>
