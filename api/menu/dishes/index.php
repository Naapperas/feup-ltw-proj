<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../lib/PARAMS.php");

    require_once("../../../database/models/menu.php");
    require_once("../../../database/models/dish.php");

    function common() {
        $user = requireAuth();

        $params = parseParams(query: [
            'id' => new IntParam(),
        ]);

        $menu = Menu::getById($params['id']);

        if ($menu === null || is_array($menu))
            APIError(HTTPStatusCode::NOT_FOUND, "Menu not found");

        $restaurant = $menu->getRestaurant();

        if ($user->id !== $restaurant->owner)
            APIError(HTTPStatusCode::FORBIDDEN, 'You are not the owner');

        $params = parseParams(body: [
            'dish' => new IntParam(),
        ]);

        $dish = Dish::getById($params['dish']);

        if ($dish === null || is_array($dish))
            APIError(HTTPStatusCode::NOT_FOUND, 'Dish not found');

        if ($menu->restaurant !== $dish->restaurant)
            APIError(HTTPStatusCode::FORBIDDEN, 'Menu and dish from different restaurants');
        
        return [$menu, $dish];
    }

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam()
            ]);

            $menu = Menu::getById($params['id']);

            if ($menu === null || is_array($menu))
                APIError(HTTPStatusCode::NOT_FOUND, 'Menu not found');

            return ['dishes' => $menu->getDishes()];
        },
        put: function() {
            list($menu, $dish) = common();
            $menu->addDish($dish->id);

            return ['added' => true];
        },
        delete: function() {
            list($menu, $dish) = common();
            $menu->removeDish($dish->id);

            return ['added' => true];
        }
    );

?>
