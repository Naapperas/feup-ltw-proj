<?php 

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /profile/edit.php");
        die;
    }

    include_once("../lib/params.php");
    include_once("../lib/files.php");
    include_once("../database/models/user.php");

    $params = parseParams(post_params: [
        'id' => new IntParam(),
        'email' => new StringParam(
            pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            min_len: 1,
            case_insensitive: true
        ),
        'name' => new StringParam(),
        'address' => new StringParam(),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'username' => new StringParam()
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
    $user->name = $params['username'];

    $user->update();

    $uploadedPhoto = $_FILES['profile_picture'];

    if ($uploadedPhoto['error'] !== 0) {
        $_SESSION['profile-edit-error'] = "Error uploading profile picture";
        header('Location: /profile/');
        die;
    }

    uploadProfilePicture($uploadedPhoto, $user->id);

    header('Location: /profile/');
?>