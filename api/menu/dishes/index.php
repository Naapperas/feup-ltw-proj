<?php
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../lib/PARAMS.php");

    require_once("../../../database/models/menu.php");
    require_once("../../../database/models/dish.php");

    APIRoute(
        get: function() {

            $params = parseParams(query: [
                'menuId' => new IntParam()
            ]);

            $menu = Menu::getById($params['id']);

            if ($menu === null || is_array($menu))
                APIError(HTTPStatusCode::NOT_FOUND, 'Menu not found');

            return ['dishes' => $menu->getDishes()];
        }
    );

?>
