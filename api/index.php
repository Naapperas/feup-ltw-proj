<?php
    declare(strict_types = 1);

    require_once("../lib/api.php");
    require_once("../lib/session.php");

    APIRoute(get: function() { return ['ok' => true]; });  // health check
?>