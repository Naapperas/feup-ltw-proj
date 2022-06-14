<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");

    require_once("../../../database/models/restaurant.php");

    function common() {
        $user = requireAuth();

        $params = parseParams(query: [
            'id' => new IntParam(),
        ]);

        $restaurant = Restaurant::getById($params['id']);

        if ($restaurant === null || is_array($restaurant))
            APIError(HTTPStatusCode::NOT_FOUND, "Restaurant not found");

        if ($user->id !== $restaurant->owner)
            APIError(HTTPStatusCode::FORBIDDEN, 'You are not the owner');

        $params = parseParams(body: [
            'category' => new IntParam(),
        ]);

        $category = Category::getById($params['category']);

        if ($category === null || is_array($category))
            APIError(HTTPStatusCode::NOT_FOUND, 'Category not found');
        
        return [$restaurant, $category];
    }

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $restaurant = Restaurant::getById($params['id']);

            if ($restaurant === null || is_array($restaurant))
                APIError(HTTPStatusCode::NOT_FOUND, "Restaurant not found");

            return ['categories' => $restaurant->getCategories()];
        },
        put: function() {
            list($restaurant, $category) = common();
            $restaurant->addCategory($category->id);

            return ['added' => true];
        },
        delete: function() {
            list($restaurant, $category) = common();
            $restaurant->removeCategory($category->id);

            return ['added' => true];
        }
    );
?>
