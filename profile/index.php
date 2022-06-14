<?php 
    declare(strict_types = 1);
    
    require_once('../templates/common.php');
    require_once('../templates/list.php');
    require_once('../templates/metadata.php');
    
    require_once('../database/models/user.php');
    
    require_once('../lib/params.php');
    require_once('../lib/page.php');
    require_once('../lib/util.php');
    require_once('../lib/session.php');

    $session = new Session();
    session_start();

    list('id' => $id) = parseParams(query: [
        'id' => new IntParam(
            default: $session->get('user'), 
            optional: true
        ),
    ]);

    if (!isset($id)) {
        header("Location: /");
        die();
    }

    $user = User::getById($id);

    if ($user === null)
        pageError(HTTPStatusCode::NOT_FOUND);

    $owned_restaurants = $user->getOwnedRestaurants();
    $favorite_restaurants = $user->getFavoriteRestaurants();
    $favorite_dishes = $user->getFavoriteDishes();
    $orders = $user->getOrders();
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(metadata: userMetadata($user),
        scripts: [
            "components/card.js",
            "components/dialog.js",
            "components/slider.js",
            "components/snackbar.js"
        ]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="profile medium medium-spacing column layout">
            <img
                class="avatar big"
                src="<?= $user->getImagePath() ?>"
                alt="<?= $user->name ?>'s profile picture"
                width="280"
                height="280"
            />
            <?php
                if ($user->id === $session->get('user')) {
                    createButton(
                        type: ButtonType::FAB,
                        text: "Edit",
                        icon: "edit",
                        href: "/profile/edit.php");
                }
            ?>

            <section class="profile-info">
                <header class="header">
                    <h2 class="title h4"><?=$user->name?>'s profile</h2>
                </header>

                <?php createIcon('email') ?><a href="mailto:<?=$user->email?>"><?=$user->email?></a>
                <?php createIcon('badge') ?><span><?=$user->full_name?></span>
                <?php createIcon('place') ?><span><?=$user->address?></span>
                <?php createIcon('phone') ?><span><?=$user->phone_number?></span>
            </section>

            <?php 
            createRestaurantList($owned_restaurants, vh: 'h5', title: 'Owned restaurants', edit: $user->id === $session->get('user'));
            createRestaurantList($favorite_restaurants, vh: 'h5', title: 'Favorite restaurants');
            createDishList($favorite_dishes, vh: 'h5', title: 'Favorite dishes');
            createOrderList($orders, vh: 'h5', title: 'Previous orders');
            ?>
        </main>
    </body>
</html>
