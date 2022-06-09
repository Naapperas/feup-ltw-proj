<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../database/models/user.php");

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $user = User::getById($params['id']);

            if ($user === null || is_array($user))
                APIError(HTTPStatusCode::NOT_FOUND, "User not found");

            return ['favoriteRestaurants' => $user->getFavoriteRestaurants()];
        }
    );
?>
