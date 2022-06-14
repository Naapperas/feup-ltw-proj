<?php
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/session.php");

    require_once("../../database/models/restaurant.php");

    APIRoute(
        get: getModel(Restaurant::class),
        post: postModel(Restaurant::class, [
            'name' => new StringParam(
                min_len: 1,
                optional: true
            ),
            'address' => new StringParam(
                min_len: 1,
                optional: true
            ),
            'phone_number' => new StringParam(
                pattern: '/^\d{9}$/',
                optional: true
            ),
            'website' => new StringParam(
                pattern: '/^https?:\/\/.+\..+$/',
                case_insensitive: true,
                optional: true
            ),
            'opening_time' => new StringParam(
                pattern: '/^([01]\d|2[0-3]):[0-5]\d$/',
                optional: true
            ),
            'closing_time' => new StringParam(
                pattern: '/^([01]\d|2[0-3]):[0-5]\d$/',
                optional: true
            )], function(Session $session, Restaurant $restaurant) {
                if ($restaurant->owner !== requireAuthUser($session)->id)
                    APIError(HTTPStatusCode::FORBIDDEN, "That restaurant is not yours");
            }),
        delete: deleteModel(Restaurant::class, function(Session $session, Restaurant $restaurant) {
            if ($restaurant->owner !== requireAuthUser($session)->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That restaurant is not yours");
        })
    );
?>
