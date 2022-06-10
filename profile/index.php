<?php 
    declare(strict_types = 1);
    
    require_once('../templates/common.php');
    require_once('../templates/list.php');
    require_once('../templates/metadata.php');
    
    require_once('../database/models/user.php');
    
    require_once('../lib/params.php');
    require_once('../lib/util.php');

    session_start();

    list('id' => $id) = parseParams(query: [
        'id' => new IntParam(
            default: $_SESSION['user'], 
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
                if ($user->id === $_SESSION['user']) {
                    createButton(
                        type: ButtonType::FAB,
                        text: "Edit",
                        icon: "edit",
                        href: "/profile/edit.php");
                    // TODO: Maybe move this
                    createButton(
                        type: ButtonType::CONTAINED,
                        text: 'New restaurant',
                        icon: 'add',
                        href: '/restaurant/create.php'
                    );
                }
            ?>

            <section class="profile-info">
                <header class="header">
                    <h2 class="title h4"><?=$user->name?>'s profile</h2>
                </header>

                <?php createIcon('email') ?><a href="mailto:<?=$user->email?>"><?=$user->email?></a>
                <?php createIcon('badge') ?><span><?=$user->full_name?></span>
                <?php createIcon('phone') ?><span><?=$user->phone_number?></span>
            </section>

            <?php 
            createRestaurantList($owned_restaurants, vh: 'h5', title: 'Owned restaurants');
            createRestaurantList($favorite_restaurants, vh: 'h5', title: 'Favorite restaurants');
            createDishList($favorite_dishes, vh: 'h5', title: 'Favorite dishes');
            ?>
        </main>
    </body>
</html>
