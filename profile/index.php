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

    $profilePicSrc = "../assets/pictures/profile/$user->id.webp";
    
    if (!file_exists($profilePicSrc)) {
        $profilePicSrc = "../assets/pictures/profile/default.webp";
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

        <main class="medium medium-spacing column layout">
            <header class="profile-header">
                <img
                    class="avatar big"
                    src="<?= $profilePicSrc ?>"
                    alt="<?= $user->name ?>'s profile picture"
                    width="240px"
                    height="240px"
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
                <h2 class="h4"><?=$user->name?>'s profile</h2>
            </header>

            <section class="profile-info">
                <header class="header">
                    <h3 class="title h6">Personal information</h3>
                </header>

                <p><span>Email: </span><span><?=$user->email?></span></p>
                <p><span>Full name: </span><span><?=$user->full_name?></span></p>
                <p><span>Phone number: </span><span><?=$user->phone_number?></span></p>
            </section>

            <?php createProfileOwnedRestaurants($user); ?>
            <?php createProfileFavoriteRestaurants($user); ?>
            <?php createProfileFavoriteDishes($user); ?>
        </main>
    </body>
</html>
