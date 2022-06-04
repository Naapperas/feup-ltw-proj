<?php 
    declare(strict_types = 1);

    require_once("../../lib/util.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
    }

    require_once("../../lib/params.php");

    $params = parseParams(get_params: [
        'userId' => new IntParam(),
    ]);

    require_once("../../database/models/user.php");

    $user = User::getById($params['userId']);

    if ($user === null || is_array($user))
        APIError(HTTPStatusCode::NOT_FOUND, 'User with given id not found');

    echo json_encode(['user' => $user, 'userPhotoPath' => $user?->getImagePath()]);
?>
