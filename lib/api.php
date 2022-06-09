<?php 
    declare(strict_types=1);

    function APIPage(
        callable $get = null, callable $post = null,
        callable $put = null, callable $delete = null
    ) {
        session_start();
        header("Content-type: application/json");
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $get) {
            $get();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post) {
            $post();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $put) {
            $put();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $delete) {
            $delete();
            return;
        }

        APIError(HTTPStatusCode::METHOD_NOT_ALLOWED, "Method not allowed");
    }
?>
