<?php
    declare(strict_types=1);

    require_once("../templates/common.php");
    require_once("../templates/list.php");
    require_once("../templates/metadata.php");
    require_once('../lib/util.php');
    require_once('../database/models/user.php');

    session_start();

    if (!isset($_SESSION['user']))
        pageError(HTTPStatusCode::UNAUTHORIZED);

    if (!isset($_SESSION['cart']) || $_SESSION['cart'] === null || $_SESSION['cart'] === []) {
        header("Location: /"); // TODO: change this if we want different behavior
        die;
    }

    $user = User::getById($_SESSION['user']);

    if ($user === null)
        pageError(HTTPStatusCode::INTERNAL_SERVER_ERROR);

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(
            title: 'Cart',
            description: "Cart for XauFome."
        ),
        scripts: [
            "components/dialog.js",
            "components/slider.js",
            "components/snackbar.js",
            "components/form.js",
            "pages/cart.js"
        ]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>
        <main class="cart layout">
            <?php
            createCartList($_SESSION['cart']);
            ?>
        </main>
    </body>
</html>
