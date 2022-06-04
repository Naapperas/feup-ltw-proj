<?php
    declare(strict_types = 1);

    require_once("../../lib/util.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST'
     && $_SERVER['REQUEST_METHOD'] !== 'GET') {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
     }
    
    session_start();

    // prevents requests from un-authenticated sources
    if (!isset($_SESSION['user']))
        APIError(HTTPStatusCode::UNAUTHORIZED, 'The user needs to be authenticated to perform this action');

    $_SESSION['cart']['dishes'] ??= [];
    $_SESSION['cart']['menus'] ??= [];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode($_SESSION['cart']);
        die;
    }

    require_once("../../lib/params.php");

    $params = parseParams(post_params: [
        'productId' => new IntParam(),
        'productType' => new StringParam(
            pattern: "/^(menu|dish)$/"
        )
    ]);

    require_once("../../database/models/dish.php");
    require_once("../../database/models/menu.php");

    $productToAdd = $params['productType'] === 'dish'
                  ? Dish::getById($params['productId'])
                  : Menu::getById($params['productId']);

    if ($productToAdd === null || is_array($productToAdd)) {
        APIError(HTTPStatusCode::NOT_FOUND, sprintf('Product of type \'%s\' with id %d not found', $params['productType'], $params['productId']));
    } else {
        $productType = $params['productType'] === 'dish' ? 'dishes' : 'menus';

        $_SESSION['cart'][$productType][$productToAdd->id] ??= 0;
        $_SESSION['cart'][$productType][$productToAdd->id] += 1;

        if ($_SESSION['cart'][$productType][$productToAdd->id] >= 50) {
            $_SESSION['easter-egg'] = true;
        }
    }

    echo json_encode(['cart' => $_SESSION['cart']]);
?>
