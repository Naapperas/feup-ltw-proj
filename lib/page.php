<?php
    declare(strict_types=1);

    require_once(__DIR__.'/util.php');

    function pageError(HTTPStatusCode $error_code) {
        error($error_code);
        require(dirname(__DIR__).'/error.php');
        die;
    }
?>