<?php 

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /profile/edit.php");
        die;
    }

    require_once("../lib/params.php");
    require_once("../lib/files.php");
    require_once("../database/models/user.php");

    session_start();

    if (!isset($_SESSION['user'])) { // user has to be authenticated
        header("Location: /");
        die;
    }

    $params = parseParams(post_params: [
        'id' => new IntParam(
            default: $_SESSION['user'] // default to current user
        ),
        'email' => new StringParam(
            pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
            min_len: 1,
            case_insensitive: true
        ),
        'name' => new StringParam(min_len: 1),
        'address' => new StringParam(min_len: 1),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'username' => new StringParam(min_len: 1)
    ]);

    if ($_SESSION['user'] !== $params['id']) { // trying to edit another user's profile
        header("Location: /profile/"); // since we already verify that we are authenticated, redirecting to the profile page should give no errors
        die;
    }

    $user = User::get($params['id']);

    if ($user === null) { // in case there was an error fetching the current user object from the DB
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

    if ($uploadedPhoto['error'] === 0)
        uploadProfilePicture($uploadedPhoto, $user->id);

    header('Location: /profile/');
?>