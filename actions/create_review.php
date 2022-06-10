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

    $params = parseParams(body: [
        'score' => new IntParam(
            max: 5,
            min: 0
        ),
        'content' => new StringParam(),
        'restaurantId' => new IntParam(),
        'userId' => new IntParam(
            default: $_SESSION['user']
        )
    ]);

    if ($_SESSION['user'] !== $params['userId']) { // prevents reviews from other users on our behalf
        header("Location: /");
        die;
    }

    require_once('../database/models/restaurant.php');
    require_once('../database/models/user.php');

    if (($restaurant = Restaurant::getById($params['restaurantId'])) === null || User::getById($params['userId']) === null) {
        header("Location: /");
        die;
    }
    
    if ($restaurant->owner === $params['userId']) { // owner cant post reviews
        header("Location: /restaurant?id=".$params['restaurantId']);
        die;
    }

    require_once('../database/models/review.php');

    Review::create([
        'text' => $params['content'],
        'score' => round($params['score'], 1),
        'restaurant' => $params['restaurantId'],
        'client' => $params['userId'],
        'review_date' => date(DATE_ISO8601)
    ]);

    header("Location: /restaurant?id=".$params['restaurantId']);
?>