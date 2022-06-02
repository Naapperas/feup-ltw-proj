<?php
    declare(strict_types=1);

    enum HTTPStatusCode: int {
        case BAD_REQUEST = 400;
        case UNAUTHORIZED = 401;
        case FORBIDDEN = 403;
        case NOT_FOUND = 404;
        case METHOD_NOT_ALLOWED = 405;
        case IM_A_TEAPOT = 418;
        case INTERNAL_SERVER_ERROR = 500;
    }

    function error(HTTPStatusCode $error_code) {
        http_response_code($error_code->value);
        require(dirname(__DIR__).'/error.php');
        die();
    }
?>