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
        'name' => new StringParam(min_len: 1),
        'address' => new StringParam(min_len: 1),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'website' => new StringParam(
            pattern: '/^https?:\/\/.+\..+$/',
            case_insensitive: true
        ),
        'opening_time' => new StringParam(
            pattern: '/^([01]\d|2[0-3]):[0-5]\d$/'
        ),
        'closing_time' => new StringParam(
            pattern: '/^([01]\d|2[0-3]):[0-5]\d$/'
        ),
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
    $restaurant->phone_number = $params['phone'];
    $restaurant->website = $params['website'];
    $restaurant->opening_time = $params['opening_time'];
    $restaurant->closing_time = $params['closing_time'];

    $restaurant->setCategories($params['categories'] ?? []);

    $restaurant->update();

    header("Location: /restaurant?id=".$params['id']);
?>
