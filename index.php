<?php 
declare(strict_types=1);

require_once("./templates/components.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        description: "Home page for XauFome.", 
        scripts: ["components/form.js"],
    ); ?>
    <body>
        <?php createAppBar(); ?>

        <section class="card centered medium medium-spacing single column layout">
            <h2 class="h6">Your favorites</h2>

            <?php createMainPageCard(); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
            <?php createMainPageCard(is_elevated: true); ?>
        </section>
    </body>
</html>