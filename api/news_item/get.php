<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once '../../config/database.php';
    require_once '../../class/news_item.php';

    // allow all kinds of request
    header('Access-Control-Allow-Origin: *');

    // content type
    header('Content-Type: application/json');

    // allow GET REQUESTS
    header('Access-Control-Allow-Methods: GET');

    $database = new Database();
    $db = $database->getConnection();

    // create a news item object
    $news_item = new NewsItem($db);

    // get news item id
    $news_item->news_item_id = isset($_GET['news_item_id']) ? $_GET['news_item_id'] : die();

    echo $news_item->getNewsItemById();

    

?>