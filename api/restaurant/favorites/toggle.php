<?php
    declare(strict_types=1);

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/util.php');

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0)
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/restaurant.php');

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user']))
        error(HTTPStatusCode::UNAUTHORIZED);

    $params = parseParams(post_params: [
        'restaurantId' => new IntParam(),
    ]);

    $user = User::getById($_SESSION['user']);
    $restaurant = Restaurant::getById($params['restaurantId']);

    if ($restaurant === null)
        error(HTTPStatusCode::NOT_FOUND);

    $isFavorite = $restaurant->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedRestaurant' : 'addLikedRestaurant';
    $success = $user->$action($restaurant->id);

    if (!$success)
        error(HTTPStatusCode::INTERNAL_SERVER_ERROR);

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>