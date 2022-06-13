<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../database/models/user.php");

    APIRoute(
        get: getModel(User::class),
        post: postModel(User::class, [
            'email' => new StringParam(
                pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
                case_insensitive: true,
                optional: true
            ),
            'name' => new StringParam(
                min_len: 1,
                optional: true
            ),
            'full_name' => new StringParam(
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
        ], function($model) {
            if ($model->id !== requireAuthUser()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "Can't modify other users");
        }),
        delete: deleteModel(User::class, function($model) {
            if ($model->id !== requireAuthUser()->id)
                APIError(HTTPStatusCode::FORBIDDEN, "Can't delete other users");
        })
    );
?>
