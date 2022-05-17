<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    
    require_once('../database/models/user.php');

    session_start();

    if (isset($_GET['id'])) {

        if (htmlspecialchars($_GET['id'])) { // sanitize id
            // error
        }

        $profile = User::get(intval($_GET['id']));
    } else if (isset($_SESSION['user'])) {
        $profile = User::get($_SESSION['user']);
    } else {
        header("Location: /");
        die();
    }

    if ($profile === null) {
        require("./no_such_profile.php");
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "{$profile->name}'s profile",
        description: "{$profile->name}'s profile page on XauFome",
        styles: ["/profile/profile.css"]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <header>
                <img
                    class="avatar big"
                    src="https://picsum.photos/240"
                    alt="<?= $profile->name ?>'s profile picture"
                    width="240px"
                    height="240px"
                />
                <h2 class="h4"><?=$profile->name?>'s profile</h2>
            </header>

            <section>
                <h3 class="h5">Personal information</h3>

                <p><span>Email: </span><span><?=$profile->email?></span></p>
                <p><span>Full name: </span><span><?=$profile->full_name?></span></p>
                <p><span>Phone number: </span><span><?=$profile->phone_number?></span></p>
            </section>
        </main>

    </body>
</html>
