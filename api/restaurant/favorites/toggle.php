<?php
    declare(strict_types=1);

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/restaurant.php');

    session_start();

    if (!isset($_SESSION['user'])) { echo json_encode(false); return; } // prevents requests from un-authenticated sources

    $params = parseParams(post_params: [
        'restaurantId' => new IntParam(),
    ]);

    $user = User::get($_SESSION['user']);
    $restaurant = Restaurant::get($params['restaurantId']);

    if ($restaurant === null) { echo json_encode(false); return; }

    $action = $restaurant->isLikedBy($user) ? 'removeLikedRestaurant' : 'addLikedRestaurant';

    $success = $user->$action($restaurant->id);

    echo json_encode($success)
?>