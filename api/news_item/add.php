<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/news_item.php';
 
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

    // create a news item object
    $news_item = new NewsItem($db);

    // get parameters
    $news_item->title = $requestBody->title;
    $news_item->subtitle = $requestBody->subtitle;
    $news_item->content = $requestBody->content;
    $news_item->time_published = $requestBody->time_published;
    $news_item->cover_pic = $requestBody->cover_pic;
    $news_item->news_tag_id = $requestBody->news_tag_id;

    // create news item
    echo $news_item->createNewsItem();

    
?>