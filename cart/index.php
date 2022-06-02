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
        error(HTTPStatusCode::NOT_FOUND);

    $dishes = Dish::getById($_SESSION['cart']['dishes'] ?? []);
    $menus = Menu::getById($_SESSION['cart']['menus'] ?? []);
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(description: "Cart for XauFome."),
        scripts: ["components/card.js"]
    ); ?>
    <body class="top-app-bar layout">
        <?php
        createAppBar();
        createDishList($dishes);
        createMenuList($menus);
        ?>
    </body>
</html>
