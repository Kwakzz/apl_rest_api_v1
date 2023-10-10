<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/news_item.php';
    require_once '../img.php';
 
    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // allow only POST REQUESTS
    header('Access-Control-Allow-Methods: POST');

    // create an image object
    $img = new Img();

    // Get the uploaded file
    $coverPicFile = $_FILES['file'];

    // Check for errors
    if ($coverPicFile['error'] !== UPLOAD_ERR_OK) {
        // Handle the error
        echo json_encode(array(
            'message' => 'Error uploading cover pic',
        ));
    }

    // Temporary path of the uploaded file
    $coverPicTempPath = $coverPicFile['tmp_name'];

    // generate unique id
    $uniqueId = uniqid();

    // name of the uploaded file
    $cover_pic_name = $uniqueId;
    
    // obtain cloudinary url
    $cloudinary_url = $img->newsCoverPicCloudinaryUpload($coverPicTempPath, $cover_pic_name);

    echo json_encode(
        array(
        'cloudinary_url' => $cloudinary_url,
        )
    );

    
?>