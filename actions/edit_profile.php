<?php 

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /profile/edit.php");
        die;
    }

    require_once("../lib/params.php");
    require_once('../lib/page.php');
    require_once("../lib/files.php");

    require_once("../database/models/user.php");

    require_once("../lib/session.php");
    $session = new Session();

    if (!$session->isAuthenticated()) { // user has to be authenticated
        header("Location: /");
        die;
    }

    $params = parseParams(body: [
        'email' => new StringParam(
            pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            min_len: 1,
            case_insensitive: true
        ),
        'name' => new StringParam(min_len: 1),
        'password' => new StringParam(min_len: 8, optional: true),
        'address' => new StringParam(min_len: 1),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'username' => new StringParam(min_len: 1),
        'csrf'
    ]);

    if ($session->get('csrf') !== $params['csrf'])
        pageError(HTTPStatusCode::BAD_REQUEST);

    $user = User::getById($session->get('user'));

    if ($user === null) { // in case there was an error fetching the current user object from the DB
        header("Location: /profile/");
        die;
    }

    $user->email = $params['email'];
    $user->full_name = $params['name'];
    $user->phone_number = $params['phone'];
    $user->address = $params['address'];
    $user->name = $params['username'];
    
    if (isset($params['password'])) {
        $user->password = hashPassword($params['password']);
    }

    $user->update();

    uploadImage($_FILES['profile_picture'], 'user', $user->id, 512, 1);

    header('Location: /profile/');
?>