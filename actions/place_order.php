<?php

    require_once("../lib/util.php");

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        pageError(HTTPStatusCode::METHOD_NOT_ALLOWED);
    }

    require_once("../lib/session.php");
    $session = new Session();

    if (!$session->isAuthenticated()) { // prevents order placement from unauthenticated users
        pageError(HTTPStatusCode::UNAUTHORIZED);
    }

    require_once("../database/models/user.php");

    $user = User::getById($session->get('user'));

    if ($user === null || is_array($user)) {
        pageError(HTTPStatusCode::INTERNAL_SERVER_ERROR);
    }

    require_once('../lib/params.php');
    require_once('../lib/page.php');

    $params = parseParams(body: [
        'restaurantId' => new IntParam(),
        'dishes_to_order' => new ArrayParam(
            default: [],
            param_type: new IntParam()
        ),
        'menus_to_order' => new ArrayParam(
            default: [],
            param_type: new IntParam()
        ),
        'csrf'
    ]);

    if ($session->get('csrf') !== $params['csrf'])
        pageError(HTTPStatusCode::BAD_REQUEST);

    require_once("../database/models/order.php");
    require_once("../database/models/dish.php");
    require_once("../database/models/menu.php");

    $order = Order::create([
        'state' => 'pending',
        'order_date' => date(DATE_ISO8601),
        'user' => $user->id,
        'restaurant' => $params['restaurantId']
    ]);

    if ($order === null || is_array($order)) {
        pageError(HTTPStatusCode::INTERNAL_SERVER_ERROR);
    }

    foreach ($params['dishes_to_order'] as $dishId => $amount) {

        $dish = Dish::getById($dishId);
        if ($dish->restaurant !== $params['restaurantId']) continue;

        if ($order->addDish($dishId, $amount)) {
            unset($session->get('cart')['dishes'][$dishId]);
        }
    }

    if (count($session->get('cart')['dishes'] ?? []) === 0)
        unset($session->get('cart')['dishes']);

    foreach ($params['menus_to_order'] as $menuId => $amount) {

        $menu = Menu::getById($menuId);
        if ($menu->restaurant !== $params['restaurantId']) continue;

        if ($order->addMenu($menuId, $amount)) {
            unset($session->get('cart')['menus'][$menuId]);
        }
    }

    if (count($session->get('cart')['menus'] ?? []) === 0)
        unset($session->get('cart')['menus']);

    header("Location: /");
?>