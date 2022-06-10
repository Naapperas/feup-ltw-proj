<?php
    declare(strict_types=1);

    require_once("../templates/common.php");
    require_once("../templates/list.php");
    require_once("../templates/metadata.php");
    require_once('../lib/params.php');
    require_once('../lib/util.php');

    session_start();

    $user = User::getById($_SESSION['user']);

    if ($user === null)
        pageError(HTTPStatusCode::NOT_FOUND);

    $dishes = Dish::getById(array_keys($_SESSION['cart']['dishes'] ?? []));
    $menus = Menu::getById(array_keys($_SESSION['cart']['menus'] ?? []));
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(
            title: 'Cart',
            description: "Cart for XauFome."
        ),
        scripts: ["components/card.js", "components/dialog.js", "components/snackbar.js"]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>
        <main class="medium medium-spacing column layout">
            <?php
            createDishList($dishes);
            createMenuList($menus);
            ?>
        </main>
    </body>
</html>
