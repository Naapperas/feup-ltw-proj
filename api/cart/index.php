<?php
    declare(strict_types = 1);

    require_once("../../lib/util.php");
    require_once("../../lib/api.php");
    require_once("../../lib/params.php");
    require_once("../../lib/session.php");

    require_once("../../database/models/dish.php");
    require_once("../../database/models/menu.php");

    function common(callable $routeHandler): callable {
        return function(Session $session) use ($routeHandler) {
            requireAuth($session);

            $routeHandler($session);

            $size = 0;
            $total = [];

            foreach ($session->get('cart')['dishes'] as $dish => $amount) {
                $size += $amount;
                $dish = Dish::getById($dish);
                $total[$dish->restaurant] += $amount * $dish->price;
            }

            foreach ($session->get('cart')['menus'] as $menu => $amount) {
                $size += $amount;
                $menu = Menu::getById($menu);
                $total[$menu->restaurant] += $amount * $menu->price;
            }

            return ['cart' => array_merge($session->get('cart'), [
                'total' => $total,
                'size' => $size
            ])];
        };
    }

    APIRoute(
        get: common(function (Session $session) {
            $session->get('cart')['dishes'] ??= [];
            $session->get('cart')['menus'] ??= [];
        }),
        post: common(function (Session $session) {
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

                $session->get('cart')[$productType][$productToAdd->id] ??= 0;
                $session->get('cart')[$productType][$productToAdd->id] = max(
                    $session->get('cart')[$productType][$productToAdd->id] + $params['amount'],
                    0
                );

                if ($session->get('cart')[$productType][$productToAdd->id] >= 50) {
                    $session->set('easter-egg', true);
                } else if ($session->get('cart')[$productType][$productToAdd->id] === 0) {
                    unset($session->get('cart')[$productType][$productToAdd->id]);
                }

                if (count($session->get('cart')[$productType]) === 0)
                    unset($session->get('cart')[$productType]);
            }
        })
    );
?>
