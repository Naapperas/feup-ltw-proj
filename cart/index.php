<?php
    declare(strict_types=1);

    require_once("../templates/common.php");
    require_once("../templates/list.php");
    require_once("../templates/metadata.php");

    require_once('../lib/util.php');
    require_once('../lib/session.php');
    
    require_once('../database/models/user.php');

    $session = new Session();
    session_start();

    if (!$session->isAuthenticated())
        pageError(HTTPStatusCode::UNAUTHORIZED);

    if ($session->get('cart') === null || $session->get('cart') === []) {
        header("Location: /");
        die;
    }

    $user = User::getById($session->get('user'));

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
            createCartList($session->get('cart'));
            ?>
        </main>
    </body>
</html>
