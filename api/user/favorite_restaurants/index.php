<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../database/models/user.php");

    function common() {
        $user = requireAuthUser();

        $params = parseParams(body: [
            'restaurantId' => new IntParam(),
        ]);

        $restaurant = Restaurant::getById($params['restaurantId']);

        if ($restaurant === null || is_array($restaurant))
            APIError(HTTPStatusCode::NOT_FOUND, 'Restaurant not found');
        
        return [$user, $restaurant];
    }

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
                'restaurantId' => new IntParam(optional: true),
            ]);

            $user = User::getById($params['id']);

            if ($user === null || is_array($user))
                APIError(HTTPStatusCode::NOT_FOUND, "User not found");

            if ($params['restaurantId']) {
                $restaurant = Restaurant::getById($params['restaurantId']);
        
                if ($restaurant === null || is_array($restaurant))
                    APIError(HTTPStatusCode::NOT_FOUND, 'Restaurant not found');
                
                return ['favorite' => $restaurant->isLikedBy($user)];
            } else {
                return ['favoriteRestaurants' => $user->getFavoriteRestaurants()];
            }
        },
        put: function() {
            list($user, $restaurant) = common();
            $success = $user->addLikedRestaurant($restaurant->id);

            return ['favorite' => true];
        },
        delete: function() {
            list($user, $restaurant) = common();
            $success = $user->removeLikedRestaurant($restaurant->id);

            return ['favorite' => false];
        },
        post: function() {
            list($user, $restaurant) = common();

            $isFavorite = $restaurant->isLikedBy($user);
            $action = $isFavorite ? 'removeLikedRestaurant' : 'addLikedRestaurant';
            $success = $user->$action($restaurant->id);

            return ['favorite' => !$isFavorite];
        }
    );
?>
