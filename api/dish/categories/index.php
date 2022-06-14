<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");

    require_once("../../../database/models/dish.php");

    function common() {
        $user = requireAuth();

        $params = parseParams(query: [
            'id' => new IntParam(),
        ]);

        $dish = Dish::getById($params['id']);

        if ($dish === null || is_array($dish))
            APIError(HTTPStatusCode::NOT_FOUND, "Dish not found");

        $restaurant = $dish->getRestaurant();

        if ($user->id !== $restaurant->owner)
            APIError(HTTPStatusCode::FORBIDDEN, 'You are not the owner');

        $params = parseParams(body: [
            'category' => new IntParam(),
        ]);

        $category = Category::getById($params['category']);

        if ($category === null || is_array($category))
            APIError(HTTPStatusCode::NOT_FOUND, 'Category not found');
        
        return [$dish, $category];
    }

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $dish = Dish::getById($params['id']);

            if ($dish === null || is_array($dish))
                APIError(HTTPStatusCode::NOT_FOUND, "Dish not found");

            return ['categories' => $dish->getCategories()];
        },
        put: function() {
            list($dish, $category) = common();
            $dish->addCategory($category->id);

            return ['added' => true];
        },
        delete: function() {
            list($dish, $category) = common();
            $dish->removeCategory($category->id);

            return ['added' => true];
        }
    );
?>
