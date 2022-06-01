<?php
declare(strict_types=1);
?>

<?php function createHead(
    callable $metadata,
    array $styles = [], array $scripts = []
) { ?>
    <head>
        <?php $metadata() ?>
        
        <script src="/scripts/colorscheme.js"></script>

        <link rel="stylesheet" href="/style/index.css" />

        <?php foreach ($styles as $style) { ?>
            <link rel="stylesheet" href="<?= $style ?>" />
        <?php } ?>

        <?php foreach ($scripts as $script) { ?>
            <script src="/scripts/<?= $script ?>" defer type="module"></script>
        <?php } ?>
    </head>
<?php } ?>

<?php function baseMetadata(
    string $title = "", string $description = "",
    string $image = "/assets/logo.webp", string $type = "website",
    ?string $preview_title = null, ?string $preview_description = null
) { 
    return function() use ($title, $description, $image, $type) { ?>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        <?php if ($title === "") { ?>
            Xau Fome
        <?php } else { ?>
            <?= $title ?> - Xau Fome
        <?php } ?>
    </title>
    <meta name="description" content="<?= $description ?>" />

    <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
    <link rel="manifest" href="/assets/site.webmanifest">

    <meta name="og:title" content="<?= ($preview_title ?? $title) === '' ? 'Xau Fome' : $preview_title ?? $title ?>" />
    <meta name="og:description" content="<?= $preview_description ?? $description ?>" />
    <meta name="og:type" content="<?= $type ?>" />
    <meta name="og:image" content="<?= $image ?>" />
    <meta name="og:site_name" content="Xau Fome" />
<?php };} ?>

<?php function userMetadata(User $user) { 
    return function() use ($user) { 
        baseMetadata(
            title: $user->name,
            description: "$user->name's profile on Xau Fome.",
            image: $user->getImagePath(),
            type: "profile",
            preview_description: $user->full_name
        )(); ?>
        <meta name="profile:username" content="<?= $user->name ?>" />
<?php };} ?>

<?php function restaurantMetadata(Restaurant $restaurant) { 
    return function() use ($restaurant) { 
        baseMetadata(
            title: $restaurant->name,
            description: "$restaurant->name's page on Xau Fome",
            image: $restaurant->getImagePath(),
            preview_description: sprintf("%.2f ★ · %s", $restaurant->getReviewScore(), $restaurant->address)
        )(); ?>
<?php };} ?>
