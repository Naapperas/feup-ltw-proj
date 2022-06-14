<?php
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/session.php");

    require_once("../../database/models/dish.php");

    APIRoute(
        get: getModel(Dish::class, plural: 'dishes'),
        post: postModel(Dish::class, [
            'name' => new StringParam(
                min_len: 1,
                optional: true
            ),
            'price' => new FloatParam(
                min: 0,
                optional: true
            )], function(Session $session, Dish $dish) {
                if ($dish->getRestaurant()->owner !== requireAuthUser($session)->id)
                    APIError(HTTPStatusCode::FORBIDDEN, "That dish is not yours");
            }),
        delete: deleteModel(Dish::class, function(Session $session, Dish $dish) {
            if ($dish->getRestaurant()->owner !== requireAuthUser($session)->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That dish is not yours");
        })
    );
?>
