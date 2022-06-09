<?php
    declare(strict_types=1);

    // FIXME: Move to /api/user/favorite_restaurants
    // TODO: Use APIRoute

    require_once(dirname(dirname(dirname(__DIR__)))."/lib/util.php");

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
    }

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/dish.php');

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user']))
        apiError(HTTPStatusCode::UNAUTHORIZED, 'User has to be authenticated');

    $params = parseParams(body: [
        'dishId' => new IntParam(),
    ]);

    $user = User::getById($_SESSION['user']);

    if ($user === null || is_array($user))
        APIError(HTTPStatusCode::NOT_FOUND, 'Could not find user to perform this action: TOGGLE_LIKED_DISH');

    $dish = Dish::getById($params['dishId']);

    if ($dish === null || is_array($dish))
        APIError(HTTPStatusCode::NOT_FOUND, 'Could not find dish to perform this action: TOGGLE_LIKED_DISH');

    $isFavorite = $dish->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedDish' : 'addLikedDish';
    $success = $user->$action($dish->id);

    if (!$success)
        APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error when attempting to update restaurant\'s liked status for current user');

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>