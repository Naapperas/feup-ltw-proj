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

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        die;
    }

    $params = parseParams(post_params: [
        'restaurantId' => new IntParam(),
    ]);

    $user = User::get($_SESSION['user']);
    $restaurant = Restaurant::get($params['restaurantId']);

    if ($restaurant === null) {
        http_response_code(404);
        die;
    }

    $isFavorite = $restaurant->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedRestaurant' : 'addLikedRestaurant';
    $success = $user->$action($restaurant->id);

    if (!$success) {
        http_response_code(500);
        die;
    }

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>