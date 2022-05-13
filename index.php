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
    <body class="centered medium medium-spacing single column layout">
        <header class="appbar">
            <a href="." class="homepage-link"><h1 class="h6 color logo"></h1></a>
            <div class="button">
                <input class="textfield"
                    type="text"
                    placeholder="Restaurants, dishes, review score..."
                    id="search"
                    name="search"
                />
            </div>

            <?php createUserButtons(); ?>

        </header>

        <section class="card" id="cards">
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