<?php 
declare(strict_types = 1);

require_once('../templates/components.php');
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "Profile", description: "The user's profile page",
        styles: ["/profile/profile.css"]
    );
    ?>
    <body class="centered medium medium-spacing single column layout">
        <?php createAppBar(); ?>

        <div>
            <span class="title">Profile</span>
            <div class="user-profile-data">
                <section class="profile-pic">
                    <span>
                    <img src="https://picsum.photos/300" alt="" />
                    </span>
                </section>
                <section class="section">
                    <span class="user-data">email</span>
                    <span class="user-data">password</span>
                    <span class="user-data">username</span>
                    <span class="user-data">full name</span>
                    <span class="user-data">phone number</span>
                </section>
            </div>
        </div>

    </body>
</html>
