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
        $profile = $_SESSION['user'];
    } else {
        // error
    }

    if (count($profile) === 0) {
        header('Location: /profile/no_such_profile.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "Profile", description: "The user's profile page",
        styles: ["/profile/profile.css"]
    );
    ?>
    <body>
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <h2 class="h4">Profile</h2>

            <div class="user-profile-data">
                <section class="profile-pic">
                    <img src="https://picsum.photos/300" alt="Profile picture for user with id=<?=$profile['id']?>" />
                </section>
                <section class="section">
                    <span class="user-data"><?=$profile['email']?></span>
                    <!-- <span class="user-data">password</span> -->
                    <span class="user-data"><?=$profile['name']?></span>
                    <span class="user-data"><?=$profile['full_name']?></span>
                    <span class="user-data"><?=$profile['phone_number']?></span>
                </section>
            </div>
        </main>

    </body>
</html>
