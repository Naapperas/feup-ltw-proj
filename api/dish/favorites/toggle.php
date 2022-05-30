<?php
    declare(strict_types=1);

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        http_response_code(405);
        require_once("../../../error.php");
        die;
    }

    require_once(dirname(dirname(dirname(__DIR__))).'/lib/params.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/user.php');
    require_once(dirname(dirname(dirname(__DIR__))).'/database/models/dish.php');

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        require_once("../../../error.php");
        die;
    }

    $params = parseParams(post_params: [
        'dishId' => new IntParam(),
    ]);

    $user = User::getById($_SESSION['user']);
    $dish = Dish::getById($params['dishId']);

    if ($dish === null) {
        http_response_code(404);
        require_once("../../../error.php");
        die;
    }

    $isFavorite = $dish->isLikedBy($user);

    $action = $isFavorite ? 'removeLikedDish' : 'addLikedDish';
    $success = $user->$action($dish->id);

    if (!$success) {
        http_response_code(500);
        require_once("../../../error.php");
        die;
    }

    echo json_encode([
        "favorite" => !$isFavorite
    ]);
?>