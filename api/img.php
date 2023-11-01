<?php

    require '../../vendor/autoload.php';

    // Use the Configuration class 
    use Cloudinary\Configuration\Configuration;

    // Configure an instance of your Cloudinary cloud
    Configuration::instance('cloudinary://758874648176679:427xuqTdqBtXIEt1RgmxpSkR6Qg@dvghxq3ba?secure=true');

    // Use the UploadApi class for uploading assets
    use Cloudinary\Api\Upload\UploadApi;

    class Img {

        // --- ATTRIBUTES ---

        // --- OPERATIONS ---

        /// This function uploads an image to the Team Logos folder in Cloudinary.
        /// It returns the cloudinary url of the uploaded image.
        /// It's for uploading the logos of teams.
        public function teamLogoCloudinaryUpload ($logoTempPath, $logo_name) {
            $cloudinary_upload = new UploadApi();

            $upload_options = array(
                "folder" => "Team Logos", 
                "public_id" => $logo_name,
                "user_filename" => true,
                "overwrite" => true
            );

            $cloudinary_upload = $cloudinary_upload->upload($logoTempPath, $upload_options);

            // get cloudinary url
            return $cloudinary_upload['url'];
        }

        /// This function uploads an image to the News Cover Pics folder in Cloudinary.
        /// It returns the cloudinary url of the uploaded image.
        /// It's for uploading the cover images of news items.
        public function newsCoverPicCloudinaryUpload ($coverPicTempPath, $cover_pic_name) {

            $cloudinary_upload = new UploadApi();

            $upload_options = array(
                "folder" => "News Cover Pics", 
                "public_id" => $cover_pic_name,
                "user_filename" => true,
                "overwrite" => true
            );

            $cloudinary_upload = $cloudinary_upload->upload($coverPicTempPath, $upload_options);

            // get cloudinary url
            return $cloudinary_upload['url'];
            
        }

    }



?>