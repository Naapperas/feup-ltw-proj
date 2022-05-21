<?php 

    include_once("../lib/params.php");
    include_once("../database/models/user.php");

    print_r($_FILES);
    die;

    $params = parseParams(post_params: [
        'id' => new IntParam(),
        'email' => new StringParam(
            pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            min_len: 1
        ),
        'name' => new StringParam(),
        'address' => new StringParam(),
        'phone' => new StringParam(pattern: '/^\d{9}$/')
    ]);

    session_start();
    if ($_SESSION['user'] !== $params['id']) {
        header("Location: /profile/");
        die;
    }

    $user = User::get($params['id']);

    if ($user === null) {
        header("Location: /profile/");
        die;
    }

    $user->email = $params['email'];
    $user->full_name = $params['name'];
    $user->phone_number = $params['phone'];
    $user->address = $params['address'];

    $user->update();

    header('Location: /profile/');
?>