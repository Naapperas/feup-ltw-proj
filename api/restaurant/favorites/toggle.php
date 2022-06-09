<?php
    declare(strict_types=1);

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/util.php');

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
    }

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/restaurant.php');

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user']))
        apiError(HTTPStatusCode::UNAUTHORIZED, 'User has to be authenticated');

    $params = parseParams(body: [
        'restaurantId' => new IntParam(),
    ]);

    $user = User::getById($_SESSION['user']);

    if ($user === null || is_array($user))
        APIError(HTTPStatusCode::NOT_FOUND, 'Could not find user to perform this action: TOGGLE_LIKED_RESTAURANT');

    $restaurant = Restaurant::getById($params['restaurantId']);

    if ($restaurant === null || is_array($restaurant))
        APIError(HTTPStatusCode::NOT_FOUND, 'Could not find restaurant to perform this action: TOGGLE_LIKED_RESTAURANT');

    $isFavorite = $restaurant->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedRestaurant' : 'addLikedRestaurant';
    $success = $user->$action($restaurant->id);

    if (!$success)
        APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error when attempting to update restaurant\'s liked status for current user');

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>