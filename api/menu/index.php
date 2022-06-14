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
            )], function(Session $session, Menu $menu) {
                if ($menu->getRestaurant()->owner !== requireAuthUser($session)->id)
                    APIError(HTTPStatusCode::FORBIDDEN, "That menu is not yours");
            }),
        delete: deleteModel(Menu::class, function(Session $session, Menu $menu) {
            if ($menu->getRestaurant()->owner !== requireAuthUser($session)->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That menu is not yours");
        })
    );
?>
