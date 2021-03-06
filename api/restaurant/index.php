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
            )
        ], [
            'name' => new StringParam(
                min_len: 1
            ),
            'address' => new StringParam(
                min_len: 1
            ),
            'phone_number' => new StringParam(
                pattern: '/^\d{9}$/'
            ),
            'website' => new StringParam(
                pattern: '/^https?:\/\/.+\..+$/',
                case_insensitive: true
            ),
            'opening_time' => new StringParam(
                pattern: '/^([01]\d|2[0-3]):[0-5]\d$/'
            ),
            'closing_time' => new StringParam(
                pattern: '/^([01]\d|2[0-3]):[0-5]\d$/'
            )
        ], function($restaurant) {
            if ($restaurant->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That restaurant is not yours");
        }, function(&$values) {
            $values['owner'] = requireAuth()->id;
        }),
        delete: deleteModel(Restaurant::class, function($restaurant) {
            if ($restaurant->owner !== requireAuth()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "That restaurant is not yours");
        })
    );
?>
