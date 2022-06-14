<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../lib/session.php");

    require_once("../../../database/models/user.php");

    function common(Session $session) {
        $user = requireAuthUser($session);

        $params = parseParams(body: [
            'dishId' => new IntParam(),
        ]);

        $dish = Dish::getById($params['dishId']);

        if ($dish === null || is_array($dish))
            APIError(HTTPStatusCode::NOT_FOUND, 'Dish not found');
        
        return [$user, $dish];
    }

    APIRoute(
        get: function(Session $_) {
            $params = parseParams(query: [
                'id' => new IntParam(),
                'dishId' => new IntParam(optional: true),
            ]);

            $user = User::getById($params['id']);

            if ($user === null || is_array($user))
                APIError(HTTPStatusCode::NOT_FOUND, "User not found");

            if ($params['dishId']) {
                $dish = Dish::getById($params['dishId']);
        
                if ($dish === null || is_array($dish))
                    APIError(HTTPStatusCode::NOT_FOUND, 'Dish not found');
                
                return ['favorite' => $dish->isLikedBy($user)];
            } else {
                return ['favoriteDishes' => $user->getFavoriteDishes()];
            }
        },
        put: function(Session $session) {
            list($user, $dish) = common($session);
            $success = $user->addLikedDish($dish->id);
            return ['favorite' => true];
        },
        delete: function(Session $session) {
            list($user, $dish) = common($session);
            $success = $user->removeLikedDish($dish->id);

            return ['favorite' => false];
        },
        post: function(Session $session) {
            list($user, $dish) = common($session);

            $isFavorite = $dish->isLikedBy($user);
            $action = $isFavorite ? 'removeLikedDish' : 'addLikedDish';
            $success = $user->$action($dish->id);

            return ['favorite' => !$isFavorite];
        }
    );
?>
