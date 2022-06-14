<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    
    require_once("../../database/models/review.php");

    APIRoute(
        get: getModel(Review::class)
    );
?>
