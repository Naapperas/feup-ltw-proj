<?php 
    declare(strict_types = 1);

    require_once("../../lib/util.php");
    require_once("../../lib/api.php");
    require_once("../../lib/params.php");

    require_once("../../database/models/user.php");

    APIPage(
        get: function() {
            $params = parseParams(get_params: [
                'id' => new IntParam(optional: true),
            ]);

            if ($params['id']) {
                $user = User::getById($params['id']);

                if ($user === null || is_array($user))
                    APIError(HTTPStatusCode::NOT_FOUND, 'User with given id not found');

                echo json_encode($user);
            } else {
                $users = User::getAll();
                echo json_encode($users);
            }
        }
    );
?>
