<?php
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/session.php");

    require_once("../../database/models/menu.php");

    APIRoute(
        get: getModel(Menu::class),
        post: postModel(Menu::class, [
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
        ], function($menu) {
            if ($menu->getRestaurant()->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That menu is not yours");
        }, function($values) {
            $rest = Restaurant::getById($values['restaurant']);
            if (!isset($rest) || $rest->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That restaurant is not yours");
        }),
        delete: deleteModel(Menu::class, function($menu) {
            if ($menu->getRestaurant()->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That menu is not yours");
        })
    );
?>
