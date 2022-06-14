<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    session_start();

    if (!isset($_SESSION['user'])) { // prevents reviews from unauthenticated users
        header("Location: /");
        die;
    }

    require_once('../lib/params.php');
    require_once('../lib/page.php');

    $params = parseParams(body: [
        'reviewResponse' => new StringParam(),
        'restaurantId' => new IntParam(),
        'reviewId' => new IntParam()
    ]);

    require_once('../database/models/restaurant.php');
    require_once('../database/models/review.php');

    if (($restaurant = Restaurant::getById($params['restaurantId'])) === null || ($review = Review::getById($params['reviewId'])) === null) {
        header("Location: /");
        die;
    }
    
    if ($restaurant->owner !== $_SESSION['user']) { // only owner can post review responses
        header("Location: /restaurant?id=".$params['restaurantId']);
        die;
    }

    require_once('../database/models/response.php');

    Response::create([
        'text' => $params['reviewResponse'],
        'review' => $params['reviewId'],
        'response_date' => date(DATE_ISO8601)
    ]);

    header("Location: /restaurant?id=".$params['restaurantId']);
?>