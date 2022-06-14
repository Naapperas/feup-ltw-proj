<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once('../lib/session.php');
    $session = new Session();

    if (!$session->isAuthenticated()) { // prevents reviews from unauthenticated users
        header("Location: /");
        die;
    }

    require_once('../lib/params.php');
    require_once('../lib/page.php');

    $params = parseParams(body: [
        'score' => new IntParam(
            max: 5,
            min: 0
        ),
        'content' => new StringParam(),
        'restaurantId' => new IntParam(),
        'csrf'
    ]);

    if ($session->get('csrf') !== $params['csrf'])
        pageError(HTTPStatusCode::BAD_REQUEST);

    require_once('../database/models/restaurant.php');
    require_once('../database/models/user.php');

    if (($restaurant = Restaurant::getById($params['restaurantId'])) === null) {
        header("Location: /");
        die;
    }
    
    if ($restaurant->owner === $session->get('user')) { // owner cant post reviews
        header("Location: /restaurant?id=".$params['restaurantId']);
        die;
    }

    require_once('../database/models/review.php');

    Review::create([
        'text' => $params['content'],
        'score' => round($params['score'], 1),
        'restaurant' => $params['restaurantId'],
        'client' => $session->get('user'),
        'review_date' => date(DATE_ISO8601)
    ]);

    header("Location: /restaurant?id=".$params['restaurantId']);
?>