<?php

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /index.php");
        die();
    }

    session_start();

    require_once('../lib/params.php');

    $params = parseParams(post_params: [
        'email' => new StringParam(
            pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            min_len: 1
        ),
        'username' => new StringParam(min_len: 1),
        'password' => new StringParam(min_len: 8),
        'name' => new StringParam(min_len: 1),
        'address' => new StringParam(min_len: 1),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'referer'
    ]);

    require_once('../database/models/user.php');
    require_once('../lib/password.php');

    $user = User::create([
        'name' => $params['username'],
        'password' => hashPassword($params['password']),
        'email' => $params['email'],
        'address' => $params['address'],
        'phone_number' => $params['phone'],
        'full_name' => $params["name"]
    ]);

    if ($user === null) {
        header('Location: /register/');
        die();
    }
    
    header('Location: '.$params['referer']);
?>