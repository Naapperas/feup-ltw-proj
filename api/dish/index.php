<?php
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../database/models/dish.php");

    APIRoute(
        get: getModel(Dish::class, plural: 'dishes')
    );
?>
