<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");

    require_once("../../../database/models/restaurant.php");

    function common(callable $routeHandler): callable {
        return function() use ($routeHandler) {
            $user = requireAuth();

            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $restaurant = Restaurant::getById($params['id']);

            if ($restaurant === null || is_array($restaurant))
                APIError(HTTPStatusCode::NOT_FOUND, "Restaurant not found");
            
            if ($user->id !== $restaurant->owner)
                APIError(HTTPStatusCode::FORBIDDEN, "You are not the owner");

            return $routeHandler($restaurant);
        };
    }

    APIRoute(
        get: common(fn($restaurant) => ['orders' => $restaurant->getOrders()]),
        post: common(function($restaurant) {
            $params = parseParams(body: [
                'orderId' => new IntParam(),
                'state' => new StringParam(
                    pattern: '/^(canceled|in_progress|ready|delivered)$/'
                )
            ]);

            $order = Order::getById($params['orderId']);

            if ($order === null || is_array($order) || $order->restaurant !== $restaurant->id)
                APIError(HTTPStatusCode::NOT_FOUND, "Order not found");
            
            switch ($order->state) {
                case 'delivered':
                    APIError(HTTPStatusCode::FORBIDDEN, "Order was already delivered");
                case 'canceled':
                    APIError(HTTPStatusCode::FORBIDDEN, "Order was already canceled");
                case 'in_progress':
                    if ($params['state'] != 'delivered' && $params['state'] != 'ready')
                        APIError(HTTPStatusCode::FORBIDDEN, "Order was already in progress");
                    break;
                case 'ready':
                    if ($params['state'] != 'delivered')
                        APIError(HTTPStatusCode::FORBIDDEN, "Order was already ready");
                    break;
            }

            $order->state = $params['state'];
            $order->update();

            return ['order' => $order];
        })
    );
?>
