<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    session_start();

    if (!isset($_SESSION['user'])) { // prevents order placement from unauthenticated users
        header("Location: /");
        die;
    }

    require_once('../lib/params.php');

    $params = parseParams(body: [
        'reviewResponse' => new StringParam(),
        'restaurantId' => new IntParam(),
        'reviewId' => new IntParam()
    ]);

    header("Location: /");
?>