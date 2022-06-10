<?php
    declare(strict_types = 1);

    require_once("../../lib/util.php");
    require_once("../../lib/api.php");
    require_once("../../lib/params.php");
    require_once("../../database/models/dish.php");
    require_once("../../database/models/menu.php");

    function common(callable $routeHandler): callable {

        return function() use ($routeHandler) {
            requireAuth();

            $routeHandler();

            return ['cart' => $_SESSION['cart']];
        };
    }

    APIRoute(
        get: common(function () {
            $_SESSION['cart']['dishes'] ??= [];
            $_SESSION['cart']['menus'] ??= [];
        }),
        post: common(function () {
            $params = parseParams(body: [
                'productId' => new IntParam(),
                'productType' => new StringParam(
                    pattern: "/^(menu|dish)$/"
                ),
                'amount' => new IntParam(default: 1)
            ]);

            $productToAdd = $params['productType'] === 'dish'
                            ? Dish::getById($params['productId'])
                            : Menu::getById($params['productId']);

            if ($productToAdd === null || is_array($productToAdd)) {
                APIError(HTTPStatusCode::NOT_FOUND, 'Product not found');
            } else {
                $productType = $params['productType'] === 'dish' ? 'dishes' : 'menus';

                $_SESSION['cart'][$productType][$productToAdd->id] ??= 0;
                $_SESSION['cart'][$productType][$productToAdd->id] = max(
                    $_SESSION['cart'][$productType][$productToAdd->id] + $params['amount'],
                    0
                );

                if ($_SESSION['cart'][$productType][$productToAdd->id] >= 50) {
                    $_SESSION['easter-egg'] = true;
                }
            }
        })
    );
?>
