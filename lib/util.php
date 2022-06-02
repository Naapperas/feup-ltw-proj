<?php
declare(strict_types=1);

function error(int $error_code) {
    http_response_code($error_code);
    require(dirname(__DIR__).'/error.php');
    die();
}
?>