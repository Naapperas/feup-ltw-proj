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

    $params = parseParams(post_params: [
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

    if (Restaurant::get($params['restaurantId']) === null || User::get($params['userId']) === null) {
        header("Location: /");
        die;
    }
    
    require_once('../database/models/review.php');

    Review::create([
        'text' => $params['content'],
        'score' => round($params['score'], 1),
        'restaurant' => $params['restaurantId'],
        'client' => $params['userId'],
    ]);

    header("Location: /restaurant?id=".$params['restaurantId']);
?>