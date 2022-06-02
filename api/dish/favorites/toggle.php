<?php
    declare(strict_types=1);

    require_once(dirname(dirname(dirname(__DIR__)))."/lib/util.php");

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0)
        error(405);

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/dish.php');

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user']))
        error(401);

    $params = parseParams(post_params: [
        'dishId' => new IntParam(),
    ]);

    $user = User::getById($_SESSION['user']);
    $dish = Dish::getById($params['dishId']);

    if ($dish === null)
        error(404);

    $isFavorite = $dish->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedDish' : 'addLikedDish';
    $success = $user->$action($dish->id);

    if (!$success)
        error(500);

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>