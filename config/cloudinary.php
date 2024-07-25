<?php

    use Cloudinary\Configuration\Configuration;
    require '../../vendor/autoload.php';
    require_once '../../vendor/cloudinary/cloudinary_php/src/Cloudinary.php';

    // Use the SearchApi class for searching assets
    use Cloudinary\Api\Search\SearchApi;
    // Use the AdminApi class for managing assets
    use Cloudinary\Api\Admin\AdminApi;
    // Use the UploadApi class for uploading assets
    use Cloudinary\Api\Upload\UploadApi;


    Configuration::instance([
    'cloud' => [
        'cloud_name' => '', 
        'api_key' => '', 
        'api_secret' => ''
        ],
        'url' => [
            'secure' => true
            ]
    ]);






    // Configuration::instance('cloudinary://758874648176679:427xuqTdqBtXIEt1RgmxpSkR6Qg@dvghxq3ba?secure=true');

?>
