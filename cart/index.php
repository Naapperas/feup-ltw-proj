<?php
declare(strict_types=1);

require_once("../templates/components.php");
require_once("../templates/metadata.php");
require_once('../lib/params.php');

session_start();

list('id' => $id) = parseParams(get_params: [
    'id' => new IntParam(
        default: $_SESSION['user'], 
        optional: true
    ),
]);

if (!isset($id)) {
    header("Location: /");
    die();
}

$user = User::getById($id);

if ($user === null) {
    http_response_code(404);
    require("../error.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(description: "Cart for XauFome."),
        styles: ["/style/pages/main.css"]
    ); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <section class="dish-list">
            <header class="header">
                <h3 class="title h4">Cart</h3>
            </header>

            <?php createOrderCard() ?>
        </section>
    </body>
</html>
