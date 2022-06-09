<?php
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../database/models/restaurant.php");

    // TODO: Categories, dishes, menus

    APIRoute(
        get: getModel(Restaurant::class)
    );
?>
