<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    session_start();

    require_once('../lib/params.php');

    $params = parseParams(post_params: [
        'username' => new StringParam(min_len: 1),
        'password' => new StringParam(min_len: 1),
        'referer'
    ]);

    require_once('../database/models/user.php');

    $user = User::get(array("name" => $params['username']))[0];

    if ($user === null || !$user->validatePassword($params['password'])){
        header('Location: /login/');
        die();
    }

    $_SESSION['user'] = $user->id;
    header('Location: ' . $params['referer']); 
?>