<?php

declare(strict_types = 1);

require_once("../templates/components.php");
require_once("../templates/metadata.php");
require_once("../database/models/restaurant.php");
    
require_once('../lib/params.php');

session_start();

list('id' => $id) = parseParams(get_params: [
    'id' => new IntParam(
        optional: true
    ),
]);

if (!isset($id)) {
    header("Location: /");
    die();
}

$restaurant = Restaurant::get($id);

if ($restaurant === null) {
    http_response_code(404);
    require("../error.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: restaurantMetadata($restaurant),
        styles: ["/style/pages/restaurant.css"]
    );
    ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <h2 class="h4"><?= $restaurant->name ?></h2>

            <div class="restaurant-data">
                <section class="restaurant-pics">
                    <span>Restaurant Pictures</span>
                </section>
                <?php //createRestaurantCategories($categories); ?>
                <section class="restaurant-addr">
                    <span><?= $restaurant->address ?></span>
                </section>
            </div>

            <section class="card centered medium medium-spacing single column layout">
                <header class="header">
                    <h2 class="h6">Menus</h2>
                </header>
    
                <?php // for ($i = 0; $i < 3; ++$i) createMainPageCard(); ?>
            </section>
            
            <section class="card centered medium medium-spacing single column layout">
                <header class="header">
                    <h2 class="h6">Dishes</h2>
                </header>
    
                <?php // for ($i = 0; $i < 3; ++$i) createMainPageCard(); ?>
            </section>
        </main>
    </body>

</html>
