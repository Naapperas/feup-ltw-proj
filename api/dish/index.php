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
            )
        ], [
            'name' => new StringParam(
                min_len: 1
            ),
            'price' => new FloatParam(
                min: 0
            ),
            'restaurant' => new IntParam()
        ], function($dish) {
            if ($dish->getRestaurant()->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That dish is not yours");
        }, function($values) {
            $rest = Restaurant::getById($values['restaurant']);
            if (!isset($rest) || $rest->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That restaurant is not yours");
        }),
        delete: deleteModel(Dish::class, function($dish) {
            if ($dish->getRestaurant()->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That dish is not yours");
        })
    );
?>
