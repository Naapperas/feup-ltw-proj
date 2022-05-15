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

        <main class="centered medium medium-spacing single column layout">
            <section class="card centered medium medium-spacing single column layout">
                <header class="header">
                    <h2 class="h6">Your favorites</h2>
                </header>
    
                <?php for ($i = 0; $i < 10; ++$i) createMainPageCard(); ?>
            </section>
        </main>
    </body>
</html>