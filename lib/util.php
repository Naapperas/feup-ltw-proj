<?php
    declare(strict_types=1);

    enum HTTPStatusCode: int {
        case BAD_REQUEST = 400;
        case UNAUTHORIZED = 401;
        case FORBIDDEN = 403;
        case NOT_FOUND = 404;
        case METHOD_NOT_ALLOWED = 405;
        case IM_A_TEAPOT = 418;
        case UNPROCESSABLE_ENTITY = 422;
        case INTERNAL_SERVER_ERROR = 500;
    }

    // use $_SESSION directly to avoid having to require a whole new file, this use case is a valid exception
    function error(HTTPStatusCode $error_code) {
        http_response_code($_SESSION['easter-egg'] ? HTTPStatusCode::IM_A_TEAPOT->value : $error_code->value);
    }

    function generate_random_token() {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
?>