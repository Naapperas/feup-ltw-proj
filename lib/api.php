<?php 
    declare(strict_types=1);
    require_once(__DIR__."/util.php");
    require_once(__DIR__."/params.php");

    function requireAuth() {
        if (!isset($_SESSION['user']))
            APIError(HTTPStatusCode::UNAUTHORIZED, 'You are not logged in');
    }

    function getModel($Model, ?string $name = null, ?string $plural = null) {
        $name ??= strtolower($Model);
        $plural ??= "{$name}s";
        return function() use ($Model, $name, $plural) {
            $params = parseParams(query: [
                'id' => new IntParam(optional: true),
            ]);

            if ($params['id']) {
                $model = $Model::getById($params['id']);

                if ($model === null || is_array($model))
                    APIError(HTTPStatusCode::NOT_FOUND, "$Model not found");

                return [$name => $model];
            } else {
                $models = $Model::getAll();
                return [$plural => $models];
            }
        };
    }

    function APIRoute(
        callable $get = null, callable $post = null,
        callable $put = null, callable $delete = null
    ) {
        session_start();
        header("Content-type: application/json");
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $get) {
            echo json_encode($get());
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post) {
            echo json_encode($post());
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $put) {
            echo json_encode($put());
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $delete) {
            echo json_encode($delete());
            return;
        }

        APIError(HTTPStatusCode::METHOD_NOT_ALLOWED, "Method not allowed");
    }
?>