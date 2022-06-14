<?php 
    declare(strict_types=1);

    require_once("./templates/common.php");
    require_once("./templates/list.php");
    require_once("./templates/metadata.php");

    require_once("./database/models/restaurant.php");

    require_once("./lib/session.php");

    $session = new Session();

    session_start();

    if ($session->isAuthenticated()) {
        $user = User::getById($session->get('user'));
        $favorite_restaurants = $user->getFavoriteRestaurants();
        $favorite_dishes = $user->getFavoriteDishes();
    }

    $recommended_restaurants = Restaurant::getAll();
    $recommended_dishes = DIsh::getAll();
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(description: "Home page for XauFome."),
        scripts: [
            "components/form.js",
            "components/card.js",
            "components/dialog.js",
            "components/slider.js",
            "components/snackbar.js"
        ]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="large medium-spacing column layout">
            <?php
            createRestaurantList($favorite_restaurants, 2, title: 'Your favorite restaurants');
            createDishList($favorite_dishes, 2, title: 'Your favorite dishes', show_restaurant: true);
            createRestaurantList($recommended_restaurants, 2, title: 'Recommended restaurants');
            createDishList($recommended_dishes, 2, title: 'Recommended dishes', show_restaurant: true);
            ?>
        </main>
    </body>
</html>