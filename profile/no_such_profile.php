<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "Profile", description: "The user's profile page",
    );
    ?>
    <body>
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            Sorry, but that profile does not exist yet on our platform.
        </main>
    </body>
</html>
