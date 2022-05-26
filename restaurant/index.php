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
        scripts: ["pages/restaurant.js", "components/card.js"],
    );
    ?>
    <body class="top-app-bar restaurant layout">
        <?php createAppBar(); ?>

        <main class="restaurant-main">
            <header class="restaurant-header">
                <div class="carousel">
                    <img src="http://picsum.photos/1920/1080" alt="">
                </div>
                <h2 class="title"><?= $restaurant->name ?></h2>
            </header>

            <?php createRestaurantOwnedDishes($restaurant); ?>
            <?php createRestaurantOwnedMenus($restaurant); ?>
        </main>

        <aside class="restaurant-sidebar">
            <div class="restaurant-info">
                <header class="header">
                    <h3 class="title h4">About <?= $restaurant->name ?></h3>
                </header>

                <span class="icon">place</span>
                <span><?= $restaurant->address ?></span>
                <span class="icon">schedule</span>
                <span><?= $restaurant->opening_time ?> to <?= $restaurant->closing_time ?></span>
                <span class="icon">phone</span>
                <span><?= $restaurant->phone_number ?></span>
                <span class="icon">public</span>
                <span><?= $restaurant->website ?></span>
                <span class="icon">star</span>
                <span><?= $restaurant->getReviewScore() ?></span>

                <?php createRestaurantCategories($restaurant->getCategories()) ?>
            </div>

            <?php createForm(
                'POST', 'review', '/actions/create_review.php',
                function() {
                    ?><header class="header">
                        <h3 class="title h4">Leave a review</h3>
                    </header><?php
                    createTextField(name: 'content', label: 'Details', type: 'multiline');
                    createButton(text: 'Post', submit: true);
                }
            ) ?>
                
            <?php
                session_start();

                if (isset($_SESSION['user'])) {

                    $currentUser = User::get($_SESSION['user']);
                    
                    if($restaurant->owner === $currentUser->id) {
                        createButton(
                            type: ButtonType::FAB,
                            text: "Edit",
                            icon: "edit",
                            href: "/restaurant/edit.php?id=$restaurant->id");
                    } else {
                        if ($currentUser !== null && $restaurant->isLikedBy($currentUser)) {
                            $state = "on";
                            $text = "Unfavorite";
                        } else {
                            $state = "off";
                            $text = "Favorite";
                        }

                        createButton(
                            type: ButtonType::FAB, text: $text, class: "toggle",
                            attributes: 
                                "data-on-icon=\"favorite\"\n".
                                "data-off-icon=\"favorite_border\"\n".
                                "data-toggle-state=\"$state\"\n".
                                "data-restaurant-id=\"$restaurant->id\"".
                                "data-favorite-button"
                        );
                    }
                }
            ?>
        </aside>

    </body>

</html>
