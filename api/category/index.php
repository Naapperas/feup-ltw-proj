<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../database/models/category.php");

    APIRoute(
        get: getModel(Category::class, plural: 'categories')
    );
?>
