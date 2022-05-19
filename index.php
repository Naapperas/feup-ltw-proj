<?php 
declare(strict_types=1);

require_once("./templates/components.php");
require_once("./templates/metadata.php");
require_once("./database/models/restaurant.php");

session_start();

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(description: "Home page for XauFome."),
        scripts: ["components/form.js", "api/restaurant.js"],
        styles: ["/style/pages/main.css"]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="centered large medium-spacing single column layout">

            <?php if (isset($_SESSION['user'])) {
                createFavoriteRestaurants(User::get($_SESSION['user']));
            } ?>

            <section class="restaurant-list">
                <header class="header">
                    <h2 class="title h6">Recommended</h2>
                    <?php createButton(
                        type: ButtonType::TEXT, text: "See all",
                        class: "right",
                        href: "/restaurants/"
                    ) ?>
                </header>
    
                <?php
                foreach (Restaurant::get() as $restaurant)
                    createRestaurantCard($restaurant);
                ?>
            </section>
        </main>
    </body>
</html>