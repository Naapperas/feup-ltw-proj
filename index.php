<?php 
declare(strict_types=1);

require_once("./templates/components.php");
require_once("./database/models/restaurant.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        description: "Home page for XauFome.", 
        scripts: ["components/form.js"],
        styles: ["/style/pages/main.css"]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <section class="restaurant-list">
                <header class="header">
                    <h2 class="title h6">Your favorites</h2>
                    <?php createButton(
                        type: ButtonType::TEXT, text: "See all",
                        class: "right",
                        component: "a", href: "/restaurants/"
                    ) ?>
                </header>
    
                <?php 
                for ($i = 0; $i < 9; ++$i) 
                    createRestaurantCard(new Restaurant()); 
                ?>
            </section>

            <hr class="divider">

            <section class="restaurant-list">
                <header class="header">
                    <h2 class="title h6">Recommended</h2>
                    <?php createButton(
                        type: ButtonType::TEXT, text: "See all",
                        class: "right",
                        component: "a", href: "/restaurants/"
                    ) ?>
                </header>
    
                <?php
                for ($i = 0; $i < 20; ++$i)
                    createRestaurantCard(new Restaurant());
                ?>
            </section>
        </main>
    </body>
</html>