<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../database/models/user.php");

    function common() {
        $user = requireAuthUser();

        $params = parseParams(body: [
            'dishId' => new IntParam(),
        ]);

        $dish = Dish::getById($params['dishId']);

        if ($dish === null || is_array($dish))
            APIError(HTTPStatusCode::NOT_FOUND, 'Dish not found');
        
        return [$user, $dish];
    }

    APIRoute(
        get: function() {
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
        put: function() {
            list($user, $dish) = common();
            $success = $user->addLikedDish($dish->id);

            if (!$success)
                APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error trying to favorite dish');
            
            return ['favorite' => true];
        },
        delete: function() {
            list($user, $dish) = common();
            $success = $user->removeLikedDish($dish->id);

            if (!$success)
                APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error trying to unfavorite dish');
            
            return ['favorite' => false];
        },
        post: function() {
            list($user, $dish) = common();

            $isFavorite = $dish->isLikedBy($user);
            $action = $isFavorite ? 'removeLikedDish' : 'addLikedDish';
            $success = $user->$action($dish->id);

            if (!$success)
                APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error trying to favorite dish');
            
            return ['favorite' => !$isFavorite];
        }
    );
?>
