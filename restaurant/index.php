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
        styles: ["/style/pages/restaurant.css"],
        scripts: ["pages/restaurant.js"]
    );
    ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <section>
                <h2 class="h4"><?= $restaurant->name ?></h2>
                <?php if (($avgScore = $restaurant->getReviewScore()) !== null) { ?>
                <span class="chip right"><?php createIcon(icon: "star") ?><?= $avgScore ?></span>
                <?php } ?>
            </section>

            <?php
                session_start();

                if (isset($_SESSION['user'])) {

                    $currentUser = User::get($_SESSION['user']);
        
                    if ($currentUser !== null && $restaurant->isLikedBy($currentUser)) {
                        $state = "on";
                        $text = "Unfavorite";
                    } else {
                        $state = "off";
                        $text = "Favorite";
                    }
                    if($restaurant->$owner === $currentUser->$id) {
                        createButton(
                            type: ButtonType::ICON,
                            text: "Edit",
                            icon: "edit",
                            href: "/restaurant/edit.php?id=$restaurant->id");
                    }
        
                    createButton(
                        type: ButtonType::ICON, text: $text, class: "toggle",
                        attributes: 
                            "data-on-icon=\"favorite\"\n".
                            "data-off-icon=\"favorite_border\"\n".
                            "data-toggle-state=\"$state\"\n".
                            "data-restaurant-id=\"$restaurant->id\"".
                            "data-favorite-button"
                    );
                }
            ?>

            <div class="restaurant-data">
                <section class="restaurant-pics">
                    <span>Restaurant Pictures</span>
                </section>
                <?php 
                
                if (($categories = $restaurant->getCategories()) !== [])
                    createRestaurantCategories($categories, 'h2');
                ?>
                <section class="restaurant-addr">
                    <span><?= $restaurant->address ?></span>
                </section>
            </div>

            <section class="card centered medium medium-spacing single column layout">
                <header class="header">
                    <h2 class="h6">Menus</h2>
                    <?php 
                        createButton(
                            type: ButtonType::ICON,
                            text: "Add",
                            icon: "add",
                        );
                    ?>
                </header>
    
                <?php // for ($i = 0; $i < 3; ++$i) createMainPageCard(); ?>
            </section>
            
            <section class="card centered medium medium-spacing single column layout">
                <header class="header">
                    <h2 class="h6">Dishes</h2>
                    <?php 
                        createButton(
                            type: ButtonType::ICON,
                            text: "Add",
                            icon: "add",
                        );
                    ?>
                </header>
    
                <?php // for ($i = 0; $i < 3; ++$i) createMainPageCard(); ?>
            </section>
        </main>
    </body>

</html>
