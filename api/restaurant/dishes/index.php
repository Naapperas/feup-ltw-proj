<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");

    require_once("../../../database/models/restaurant.php");

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $restaurant = Restaurant::getById($params['id']);

            if ($restaurant === null || is_array($restaurant))
                APIError(HTTPStatusCode::NOT_FOUND, "Restaurant not found");

            return ['dishes' => $restaurant->getOwnedDishes()];
        }
    );
?>
