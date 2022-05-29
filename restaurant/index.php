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
                <img src="<?= $restaurant->getThumbnail() ?>" alt="">
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
                <a href="https://www.google.pt/maps/place/<?= rawurlencode(html_entity_decode($restaurant->address)) ?>" target="_blank">
                    <?= $restaurant->address ?>
                </a>
                <span class="icon">schedule</span>
                <span>
                    <time datetime="<?= $restaurant->opening_time ?>"><?= preg_replace('/^0/', '', $restaurant->opening_time, 1) ?></time>
                    to
                    <time datetime="<?= $restaurant->opening_time ?>"><?= preg_replace('/^0/', '', $restaurant->closing_time, 1) ?></time>
                </span>
                <span class="icon">phone</span>
                <a href="tel:<?= $restaurant->phone_number ?>"><span><?= $restaurant->phone_number ?></span></a>
                <span class="icon">public</span>
                <a href="<?= $restaurant->website ?>" target="_blank">
                    <?= preg_replace('/^www\./', '', parse_url($restaurant->website, PHP_URL_HOST), 1) ?>
                </a>
                <?php if (($score = $restaurant->getReviewScore()) != null) {
                createIcon("star");?><span><?= round($score, 1) ?></span>
                <?php } ?>

                <?php createRestaurantCategories($restaurant->getCategories()) ?>
            </div>

            <?php

                createRestaurantReviewList($restaurant);

                session_start();

                if (isset($_SESSION['user'])) {

                    $currentUser = User::get($_SESSION['user']);

                    if ($currentUser !== null) {
                        if($restaurant->owner === $currentUser->id) {
                            createButton(
                                type: ButtonType::FAB,
                                text: "Edit",
                                icon: "edit",
                                href: "/restaurant/edit.php?id=$restaurant->id");
                        } else {
                            createForm(
                                'POST', 'review', '/actions/create_review.php',
                                function() use ($restaurant, $currentUser) {
                                    ?><header class="header">
                                        <h3 class="title h4">Leave a review</h3>
                                    </header>
                                    <fieldset class="score">
                                        <input class="radio" type="radio" name="score" value="0" checked>
                                        <input class="radio" type="radio" name="score" value="1">
                                        <input class="radio" type="radio" name="score" value="2">
                                        <input class="radio" type="radio" name="score" value="3">
                                        <input class="radio" type="radio" name="score" value="4">
                                        <input class="radio" type="radio" name="score" value="5">
                                    </fieldset>
                                    <input type="hidden" name="restaurantId" value="<?= $restaurant->id ?>">
                                    <input type="hidden" name="userId" value="<?= $currentUser->id ?>"><?php
                                    createTextField(name: 'content', label: 'Details', type: 'multiline');
                                    createButton(text: 'Post', submit: true);
                                }
                            );

                            if ($restaurant->isLikedBy($currentUser)) {
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
                }
            ?>
        </aside>

    </body>

</html>
