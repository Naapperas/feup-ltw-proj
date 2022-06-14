<?php 

    if (strcmp($_SERVER['REQUEST_METHOD'], "POST") !== 0) {
        header("Location: /");
        die;
    }

    require_once("../lib/params.php");
    require_once('../lib/page.php');
    require_once("../lib/files.php");
    require_once("../lib/session.php");

    require_once("../database/models/user.php");
    require_once("../database/models/restaurant.php");

    $params = parseParams(body: [
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
                    param_type: new IntParam(),
                    minLen: 2
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
                    param_type: new IntParam(),
                    minLen: 2
                ),
            ])
        ),
        'csrf'
    ]);

    $session = new Session();

    if ($session->get('csrf') !== $params['csrf'])
        pageError(HTTPStatusCode::BAD_REQUEST);

    if (!$session->isAuthenticated()) { // prevents edits from unauthenticated users
        header("Location: /restaurant?id=".$params['id']);
        die;
    }

    if (!$params['id']) {
        $restaurant = Restaurant::create([
            'name' => $params['name'],
            'address' => $params['address'],
            'phone_number' => $params['phone'],
            'website' => $params['website'],
            'opening_time' => $params['opening_time'],
            'closing_time' => $params['closing_time'],
            'owner' => $session->get('user')
        ]);
    } else {
        $restaurant = Restaurant::getById($params['id']);
    
        if ($restaurant === null) { // error fetching restaurant model
            header("Location: /restaurant?id=".$params['id']);
            die;
        }
    
        if($session->get('user') !== $restaurant->owner) { // prevents edits from everyone other than the restaurant owner
            header("Location: /restaurant?id=".$params['id']);
            die();
        }
    
        $restaurant->name = $params['name'];
        $restaurant->address = $params['address'];
        $restaurant->phone_number = $params['phone'];
        $restaurant->website = $params['website'];
        $restaurant->opening_time = $params['opening_time'];
        $restaurant->closing_time = $params['closing_time'];

        $restaurant->update();
    }

    $restaurant->setCategories($params['categories']);

    foreach ($params['dishes_to_edit'] as $id => $value) {
        $dish = Dish::getById($id);

        if ($dish == null || $dish->restaurant != $restaurant->id)
            continue;
        
        $dish->name = $value['name'];
        $dish->price = $value['price'];
        $dish->setCategories($value['categories']);

        $dish->update();

        uploadImage($_FILES['dishes_to_edit'], 'dish', $id, 1920, 1, $id);
    }

    foreach ($params['dishes_to_delete'] as $id) {
        $dish = Dish::getById($id);

        if ($dish == null || $dish->restaurant != $restaurant->id)
            continue;
        
        $dish->delete();
    }

    foreach ($params['dishes_to_add'] as $i => $arr) {
        $arr['restaurant'] = $restaurant->id;

        $categories = $arr['categories'];
        unset($arr['categories']);

        $dish = Dish::create($arr);

        $params['dishes_to_add'][$i] = $dish;

        if ($dish === null) continue;

        $dish->setCategories($categories);
        uploadImage($_FILES['dishes_to_add'], 'dish', $dish->id, 1920, 1, $i);
    }

    foreach ($params['menus_to_edit'] as $id => $value) {
        $menu = Menu::getById($id);

        if ($menu == null || $menu->restaurant != $restaurant->id)
            continue;

        $dishes = [];
        foreach ($value['dishes'] as $dish) {
            if ($dish > 0)
                $dishes[] = $dish;
            else if (isset($params['dishes_to_add'][-$dish]))
                $dishes[] = $params['dishes_to_add'][-$dish]->id;
        }
        
        $menu->name = $value['name'];
        $menu->price = $value['price'];
        $menu->setDishes($dishes);

        $menu->update();

        uploadImage($_FILES['menus_to_edit'], 'menu', $id, 1920, 1, $id);
    }

    foreach ($params['menus_to_delete'] as $id) {
        $menu = Menu::getById($id);

        if ($menu == null || $menu->restaurant != $restaurant->id)
            continue;
        
        $menu->delete();
    }

    foreach ($params['menus_to_add'] as $i => $arr) {
        $arr['restaurant'] = $restaurant->id;

        $dishes = [];
        foreach ($arr['dishes'] as $dish) {
            if ($dish > 0)
                $dishes[] = $dish;
            else if (isset($params['dishes_to_add'][-$dish]))
                $dishes[] = $params['dishes_to_add'][-$dish]->id;
        }

        unset($arr['dishes']);

        $menu = Menu::create($arr);

        if ($menu === null) continue;

        $menu->setDishes($dishes);
        uploadImage($_FILES['menus_to_add'], 'menu', $menu->id, 1920, 1, $i);
    }

    uploadImage($_FILES['thumbnail'], 'restaurant', $restaurant->id, 1920, 16/9);

    header("Location: /restaurant?id=".$restaurant->id);
?>
