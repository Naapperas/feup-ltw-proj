<?php 
    declare(strict_types=1);
    require_once(__DIR__."/util.php");
    require_once(__DIR__."/params.php");
    require_once(__DIR__."/session.php");

    function APIError(HTTPStatusCode $error_code, string $error_message) {
        error($error_code);
        echo json_encode(['error' => $error_message]);
        die;
    }

    function requireAuth(Session $session): void {
        if (!$session->isAuthenticated())
            APIError(HTTPStatusCode::UNAUTHORIZED, 'You are not logged in');
    }

    function requireAuthUser(Session $session): User {
        if (!$session->isAuthenticated() 
         || ($user = User::getById($session->get('user'))) === null
         || is_array($user))
            APIError(HTTPStatusCode::UNAUTHORIZED, 'You are not logged in');

        return $user;
    }

    function deleteModel($Model, ?callable $verification = null) {
        return function(Session $session) use ($Model, $verification) {
            list('id' => $id) = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $model = $Model::getById($id);

            if (!isset($model) || is_array($model))
                APIError(HTTPStatusCode::NOT_FOUND, "$Model not found");

            if ($verification)
                $verification($session, $model);

            return ['success' => $model->delete()];
        };
    }

    function postModel($Model, array $params, ?callable $verification = null, ?string $name = null) {
        $name ??= strtolower($Model);
        return function(Session $session) use ($Model, $name, $params, $verification) {
            list('id' => $id) = parseParams(query: [
                'id' => new IntParam(),
            ]);

            $model = $Model::getById($id);

            if (!isset($model) || is_array($model))
                APIError(HTTPStatusCode::NOT_FOUND, "$Model not found");

            if ($verification)
                $verification($session, $model);
            
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
        return function(Session $_) use ($Model, $name, $plural) {
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

        $session = new Session();

        header("Content-type: application/json");
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $get) {
            echo json_encode($get($session));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post) {
            echo json_encode($post($session));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $put) {
            parse_str(file_get_contents('php://input'), $_POST);
            echo json_encode($put($session));
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $delete) {
            parse_str(file_get_contents('php://input'), $_POST);
            echo json_encode($delete($session));
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
