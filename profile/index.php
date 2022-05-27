<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');
    
    require_once('../database/models/user.php');
    
    require_once('../lib/params.php');

    session_start();

    list('id' => $id) = parseParams(get_params: [
        'id' => new IntParam(
            default: $_SESSION['user'], 
            optional: true
        ),
    ]);

    if (!isset($id)) {
        header("Location: /");
        die();
    }

    $user = User::get($id);

    if ($user === null) {
        http_response_code(404);
        require("../error.php");
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(metadata: userMetadata($user),
        scripts: ["components/card.js"],
        styles: ["/style/pages/profile.css"]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="profile medium medium-spacing column layout">
            <img
                class="avatar big"
                src="<?= $user->getProfilePic() ?>"
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
                }
            ?>

            <section class="profile-info">
                <header class="header">
                    <h2 class="title h4"><?=$user->name?>'s profile</h2>
                </header>

                <?php createIcon('email') ?><span><?=$user->email?></span>
                <?php createIcon('badge') ?><span><?=$user->full_name?></span>
                <?php createIcon('phone') ?><span><?=$user->phone_number?></span>
            </section>

            <?php createProfileOwnedRestaurants($user); ?>
            <?php createProfileFavoriteRestaurants($user); ?>
            <?php createProfileFavoriteDishes($user); ?>
        </main>
    </body>
</html>
