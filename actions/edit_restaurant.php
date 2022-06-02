<?php 

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once("../lib/params.php");
    require_once("../lib/files.php");
    require_once("../database/models/user.php");
    require_once("../database/models/restaurant.php");

    $params = parseParams(post_params: [
        'id' => new IntParam(),
        'name' => new StringParam(min_len: 1),
        'address' => new StringParam(min_len: 1),
        'phone' => new StringParam(pattern: '/^\d{9}$/'),
        'website' => new StringParam(
            pattern: '/^https?:\/\/.+\..+$/',
            case_insensitive: true
        ),
        'opening_time' => new StringParam(
            pattern: '/^([01]\d|2[0-3]):[0-5]\d$/'
        ),
        'closing_time' => new StringParam(
            pattern: '/^([01]\d|2[0-3]):[0-5]\d$/'
        ),
        'categories' => new ArrayParam(
            default: [],
            param_type: new IntParam()
        ),
        'dishes_to_edit' => new ArrayParam(
            default: [],
            param_type: new ObjectParam([
                'name' => new StringParam(min_len: 1),
                'price' => new FloatParam(min: 0),
                'categories' => new ArrayParam(
                    default: [],
                    param_type: new IntParam()
                ),
            ])
        ),
        'dishes_to_delete' => new ArrayParam(
            default: [],
            param_type: new IntParam()
        ),
        'dishes_to_add' => new ArrayParam(
            default: [],
            param_type: new ObjectParam([
                'name' => new StringParam(min_len: 1),
                'price' => new FloatParam(min: 0),
                'categories' => new ArrayParam(
                    default: [],
                    param_type: new IntParam()
                ),
            ])
        ),
        'menus_to_edit' => new ArrayParam(
            default: [],
            param_type: new ObjectParam([
                'name' => new StringParam(min_len: 1),
                'price' => new FloatParam(min: 0),
                'dishes' => new ArrayParam(
                    default: [],
                    param_type: new IntParam()
                ),
            ])
        ),
        'menus_to_delete' => new ArrayParam(
            default: [],
            param_type: new IntParam()
        ),
        'menus_to_add' => new ArrayParam(
            default: [],
            param_type: new ObjectParam([
                'name' => new StringParam(min_len: 1),
                'price' => new FloatParam(min: 0),
                'dishes' => new ArrayParam(
                    default: [],
                    param_type: new IntParam()
                ),
            ])
        )
    ]);

    session_start();

    if (!isset($_SESSION['user'])) { // prevents edits from unauthenticated users
        header("Location: /restaurant?id=".$params['id']);
        die;
    }

    $restaurant = Restaurant::getById($params['id']);

    if ($restaurant === null) { // error fetching restaurant model
        header("Location: /restaurant?id=".$params['id']);
        die;
    }

    if($_SESSION['user'] !== $restaurant->owner) { // prevents edits from everyone other than the restaurant owner
        header("Location: /restaurant?id=".$params['id']);
        die();
    }

    $restaurant->name = $params['name'];
    $restaurant->address = $params['address'];
    $restaurant->phone_number = $params['phone'];
    $restaurant->website = $params['website'];
    $restaurant->opening_time = $params['opening_time'];
    $restaurant->closing_time = $params['closing_time'];

    $restaurant->setCategories($params['categories']);

    $restaurant->update();

    foreach ($params['dishes_to_edit'] as $id => $value) {
        $dish = Dish::getById($id);

        if ($dish == null || $dish->restaurant != $restaurant->id)
            continue;
        
        $dish->name = $value['name'];
        $dish->price = $value['price'];
        $dish->setCategories($value['categories']);

        $dish->update();

        uploadImage($_FILES['dishes_to_edit'], 'dish', $id, 1920, index: $id);
    }

    foreach ($params['dishes_to_delete'] as $id) {
        $dish = Dish::getById($id);

        if ($dish == null || $dish->restaurant != $restaurant->id)
            continue;
        
        $dish->delete();
    }

    foreach ($params['dishes_to_add'] as $i => $arr) {
        $arr['restaurant'] = $restaurant->id;
        $dish = Dish::create($arr);
        $dish->setCategories($arr['categories']);
        uploadImage($_FILES['dishes_to_add'], 'dish', $dish->id, 1920, index: $i);
    }

    foreach ($params['menus_to_edit'] as $id => $value) {
        $menu = Menu::getById($id);

        if ($menu == null || $menu->restaurant != $restaurant->id)
            continue;
        
        $menu->name = $value['name'];
        $menu->price = $value['price'];
        $menu->setDishes($value['dishes']);

        $menu->update();

        uploadImage($_FILES['menus_to_edit'], 'menu', $id, 1920, index: $id);
    }

    foreach ($params['menus_to_delete'] as $id) {
        $menu = Menu::getById($id);

        if ($menu == null || $menu->restaurant != $restaurant->id)
            continue;
        
        $menu->delete();
    }

    foreach ($params['menus_to_add'] as $i => $arr) {
        $arr['restaurant'] = $restaurant->id;
        $menu = Menu::create($arr);
        $menu->setDishes($arr['dishes']);
        uploadImage($_FILES['menus_to_add'], 'menu', $menu->id, 1920, index: $i);
    }

    uploadImage($_FILES['thumbnail'], 'restaurant', $restaurant->id, 1920);

    header("Location: /restaurant?id=".$params['id']);
?>
