<?php
declare(strict_types=1);
?>

<?php function baseMetadata(
    string $title = "", string $description = "",
    string $image = "/assets/logo.webp", string $type = "website"
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

    <meta name="og:title" content="<?= $title === '' ? 'Xau Fome' : $title ?>" />
    <meta name="og:description" content="<?= $description ?>" />
    <meta name="og:type" content="<?= $type ?>" />
    <meta name="og:image" content="<?= $image ?>" />
    <meta name="og:site_name" content="Xau Fome" />
<?php };} ?>

<?php function userMetadata(User $user) { 
    return function() use ($user) { 
        baseMetadata(
            title: "$user->name's profile",
            description: "$user->name's profile on Xau Fome.",
            image: "http://picsum.photos/360", // TODO
            type: "profile"
        )(); ?>
        <meta name="profile:username" content="<?= $user->name ?>" />
<?php };} ?>

<?php function restaurantMetadata(Restaurant $restaurant) { 
    return function() use ($restaurant) { 
        baseMetadata(
            title: $restaurant->name,
            description: "$restaurant->name's page on Xau Fome.",
            image: "http://picsum.photos/360" // TODO
        )(); ?>
<?php };} ?>
