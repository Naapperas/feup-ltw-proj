<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once("../lib/session.php");
    $session = new Session();

    if (!$session->isAuthenticated()) { // prevents reviews from unauthenticated users
        header("Location: /");
        die;
    }

    require_once('../lib/params.php');
    require_once('../lib/page.php');

    $params = parseParams(body: [
        'reviewResponse' => new StringParam(),
        'restaurantId' => new IntParam(), // found out later that this param is not necessary since restaurantId can come from the review
        'reviewId' => new IntParam(),
        'csrf'
    ]);

    if ($session->get('csrf') !== $params['csrf'])
        pageError(HTTPStatusCode::BAD_REQUEST);

    require_once('../database/models/restaurant.php');
    require_once('../database/models/review.php');

    if (($restaurant = Restaurant::getById($params['restaurantId'])) === null || ($review = Review::getById($params['reviewId'])) === null || $review->restaurant !== $restaurant->id) {
        header("Location: /");
        die;
    }
    
    if ($restaurant->owner !== $session->get('user')) { // only owner can post review responses
        header("Location: /restaurant?id=".$params['restaurantId']);
        die;
    }

    require_once('../database/models/response.php');

    Response::create([
        'text' => $params['reviewResponse'],
        'review' => $review->id,
        'response_date' => date(DATE_ISO8601)
    ]);

    header("Location: /restaurant?id=".$params['restaurantId']);
?>