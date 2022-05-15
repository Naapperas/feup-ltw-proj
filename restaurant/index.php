<?php

declare(strict_types = 1);

require_once("../templates/components.php");
require_once("../database/models/restaurant.php");
require_once("../lib/category.php");

session_start();

if(isset($_GET['id'])) {
    if (htmlspecialchars($_GET['id'])) { // sanitize id
        // error
    }

    $restaurant = Restaurant::get(intval($_GET['id']));
    $categories = getCategories(intval($_GET['id']));
    print_r($categories);
} else {
    $restaurant = array("name" => "teste");
}

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "Restaurant Name", description: "Page for restaurants to present their products",
        styles: ["restaurant/restaurant.css"]
    );
    ?>
    <body>
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <h2 class="h4"><?= $restaurant['name']?></h2>

            <div class="restaurant-data">
                <section class="restaurant-pics">
                    <span>Restaurant Pictures</span>
                </section>
                <?php createRestaurantCategories($categories); ?>
                <section class="restaurant-addr">
                    <span><?= $restaurant['address']?></span>
                </section>
            </div>

            <section class="card centered medium medium-spacing single column layout">
                <header class="header">
                    <h2 class="h6">Menus</h2>
                </header>
    
                <?php for ($i = 0; $i < 3; ++$i) createMainPageCard(); ?>

                <header class="header">
                    <h2 class="h6">Dishes</h2>
                </header>
    
                <?php for ($i = 0; $i < 3; ++$i) createMainPageCard(); ?>
            </section>
        </main>
    </body>

</html>
