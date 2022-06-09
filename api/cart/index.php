<?php
    declare(strict_types = 1);

    require_once("../../lib/util.php");
    require_once("../../lib/api.php");
    require_once("../../lib/params.php");
    require_once("../../database/models/dish.php");
    require_once("../../database/models/menu.php");

    APIRoute(
        get: function() {
            requireAuth();

            $_SESSION['cart']['dishes'] ??= [];
            $_SESSION['cart']['menus'] ??= [];

            return ['cart' => $_SESSION['cart']];
        },
        post: function() {
            $params = parseParams(body: [
                'productId' => new IntParam(),
                'productType' => new StringParam(
                    pattern: "/^(menu|dish)$/"
                )
            ]);

            $productToAdd = $params['productType'] === 'dish'
                            ? Dish::getById($params['productId'])
                            : Menu::getById($params['productId']);

            if ($productToAdd === null || is_array($productToAdd)) {
                APIError(HTTPStatusCode::NOT_FOUND, 'Product not found');
            } else {
                $productType = $params['productType'] === 'dish' ? 'dishes' : 'menus';

                $_SESSION['cart'][$productType][$productToAdd->id] ??= 0;
                $_SESSION['cart'][$productType][$productToAdd->id] += 1;

                if ($_SESSION['cart'][$productType][$productToAdd->id] >= 50) {
                    $_SESSION['easter-egg'] = true;
                }
            }

            return ['cart' => $_SESSION['cart']];
        }
    );
?>
