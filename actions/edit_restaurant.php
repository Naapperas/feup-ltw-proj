<?php 

    include_once("../lib/params.php");
    include_once("../database/models/user.php");

    $params = parseParams(post_params: [
        'id' => new IntParam(),
        'name' => new StringParam(),
        'address' => new StringParam(),
    ]);

    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: /restaurant?id=$id");
        die;
    }

    $restaurant = Restaurant::get($params['id']);

    if($_SESSION['user'] !== 'id') {
        header("Location: /restaurant?id=$id");
        die();
    }

    $restaurant->name = $params['name'];
    $restaurant->address = $params['address'];

    $restaurant->update();

    header("Location: /restaurant?id=$id");
?>
