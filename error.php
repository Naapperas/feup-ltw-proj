<?php
declare(strict_types = 1);

require_once("templates/components.php");
require_once("templates/metadata.php");

$response_code = http_response_code();

$emoticon = [
    400 => "(>_<)",
    401 => "",
    403 => "ヽ(ｏ`皿′ｏ)ﾉ",
    404 => "¯\_(ツ)_/¯",
    405 => "(°_°)ゞ",
][$response_code] ?? "";

$description = [
    400 => "Bad request",
    401 => "Unauthorized",
    403 => "Forbidden",
    404 => "Page not found",
    405 => "Method not allowed",
][$response_code] ?? "Unknown response code";
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(metadata: baseMetadata(title: "$response_code $description")) ?>
    <body class="top-app-bar layout">
        <?php createAppBar() ?>
        <main class="centered large-spacing single column layout">
            <span class="h2" aria-hidden="true"><?= $emoticon ?></span>
            <h2 class="h3"><?= $response_code ?> <?= $description ?></h2>
        </main>
    </body>
</html>