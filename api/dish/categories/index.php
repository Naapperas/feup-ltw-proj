<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");

    require_once("../../../database/models/dish.php");

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $dish = Dish::getById($params['id']);

            if ($dish === null || is_array($dish))
                APIError(HTTPStatusCode::NOT_FOUND, "Dish not found");

            return ['categories' => $dish->getCategories()];
        }
    );
?>
