<?php 

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once("../lib/params.php");
    require_once("../database/models/user.php");
    require_once("../database/models/restaurant.php");

    $params = parseParams(post_params: [
        'id' => new IntParam(),
        'name' => new StringParam(),
        'address' => new StringParam(),
        'categories' => new ArrayParam(
            optional: true,
        ),
    ]);

    session_start();

    if (!isset($_SESSION['user'])) { // prevents edits from unauthenticated users
        header("Location: /restaurant?id=".$params['id']);
        die;
    }

    $restaurant = Restaurant::get($params['id']);

    if ($restaurant === null) { // error fetching restaurant model
        header("Location: /restaurant?id=".$params['id']);
        die;
    }

    if($_SESSION['user'] !== $restaurant->owner) { // prevents edits from everyone other than the restaurant owner
        header("Location: /restaurant?id=".$params['id']);
        die();
    }

    $restaurant->name = $params['name'];
    $restaurant->address = $params['address'];

    $restaurant->setCategories($params['categories'] ?? []);

    $restaurant->update();

    header("Location: /restaurant?id=".$params['id']);
?>
