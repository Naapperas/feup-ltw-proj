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

    require_once("../../lib/params.php");

    $params = parseParams(post_params: [
        'productId' => new IntParam(),
        'productType' => new StringParam(
            pattern: "/^(menu|dish)$/"
        )
    ]);

    $ok = true;

    require_once("../../database/models/dish.php");
    require_once("../../database/models/menu.php");

    $productToAdd = (strcmp($params['productType'], 'dish') !== 0) ? Menu::getById($params['productId']) : Dish::getById($params['productId']);

    if ($productToAdd === null || is_array($productToAdd))
        $ok = false;
    else {
        $productType = strcmp($params['productType'], 'dish') ? 'menus' : 'dishes';

        if (!isset($_SESSION['cart'][$productType])) $_SESSION['cart'][$productType] = [];

        $_SESSION['cart'][$productType][] = $productToAdd->id;
    }

    echo json_encode(['ok' => $ok, 'product' => $productToAdd, $_SESSION['cart']]);
?>
