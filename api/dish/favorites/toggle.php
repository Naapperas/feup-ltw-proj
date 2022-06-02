<?php
    declare(strict_types=1);

    require_once(dirname(dirname(dirname(__DIR__)))."/lib/util.php");

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0)
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/dish.php');

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user']))
        error(HTTPStatusCode::UNAUTHORIZED);

    $params = parseParams(post_params: [
        'dishId' => new IntParam(),
    ]);

    $user = User::getById($_SESSION['user']);
    $dish = Dish::getById($params['dishId']);

    if ($dish === null)
        error(HTTPStatusCode::NOT_FOUND);

    $isFavorite = $dish->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedDish' : 'addLikedDish';
    $success = $user->$action($dish->id);

    if (!$success)
        error(HTTPStatusCode::INTERNAL_SERVER_ERROR);

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>