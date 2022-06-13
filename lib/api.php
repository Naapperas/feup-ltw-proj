<?php 
    declare(strict_types=1);
    require_once(__DIR__."/util.php");
    require_once(__DIR__."/params.php");

    function requireAuth() {
        if (!isset($_SESSION['user']))
            APIError(HTTPStatusCode::UNAUTHORIZED, 'You are not logged in');
    }

    function requireAuthUser() {
        if (!isset($_SESSION['user']) 
         || ($user = User::getById($_SESSION['user'])) === null
         || is_array($user))
            APIError(HTTPStatusCode::UNAUTHORIZED, 'You are not logged in');

        return $user;
    }

    function deleteModel($Model, ?callable $verification = null) {
        return function() use ($Model, $verification) {
            list('id' => $id) = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $model = $Model::getById($id);

            if (!isset($model) || is_array($model))
                APIError(HTTPStatusCode::NOT_FOUND, "$Model not found");

            if ($verification)
                $verification($model);

            return ['success' => $model->delete()];
        };
    }

    function postModel($Model, array $params, ?callable $verification = null, ?string $name = null) {
        $name ??= strtolower($Model);
        return function() use ($Model, $name, $params, $verification) {
            list('id' => $id) = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $model = $Model::getById($id);

            if (!isset($model) || is_array($model))
                APIError(HTTPStatusCode::NOT_FOUND, "$Model not found");

            if ($verification)
                $verification($model);
            
            $values = parseParams(body: $params);

            foreach ($values as $key => $value) {
                if ($value)
                    $model->{$key} = $value;
            }
            $model->update();

            return [$name => $model];
        };
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
            parse_str(file_get_contents('php://input'), $_POST);
            echo json_encode($put());
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $delete) {
            parse_str(file_get_contents('php://input'), $_POST);
            echo json_encode($delete());
            return;
        }

        $methods[] = 'OPTIONS';
        if ($get) $methods[] = 'GET';
        if ($post) $methods[] = 'POST';
        if ($put) $methods[] = 'PUT';
        if ($delete) $methods[] = 'DELETE';

        $methods = implode(', ', $methods);
        header("Allow: $methods");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        APIError(HTTPStatusCode::METHOD_NOT_ALLOWED, "Method not allowed");
    }
?>
