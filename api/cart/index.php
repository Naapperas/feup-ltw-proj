<?php

    declare(strict_types = 1);

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        http_response_code(405);
        require_once("../../../error.php");
        die;
    }

    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        require_once("../../../error.php");
        die;
    }

    $params = parseParams(post_params: [
        'productId' => new IntParam(),
        'productType' => new StringParam()
    ]);

    if ($params['productType'] === 'dish') {
        $dishToAdd = Dish::getById($params['productId']);
        $_SESSION['dishes'][] = $dishToAdd;
    } else if ($params['productType'] === 'menu') {
        $menuToAdd = Menu::getById($params['productId']);
        $_SESSION['menus'][] = $menuToAdd;
    }

?>
