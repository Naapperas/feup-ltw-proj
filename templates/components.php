<?php
declare(strict_types=1);

enum ButtonType: string {
    case CONTAINED = "contained";
    case OUTLINED = "outlined";
    case TEXT = "text";
    case ICON = "icon";
}
?>

<?php function createHead(
    string $title = "", string $description = "",
    array $styles = [], array $scripts = []
) { ?>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>
            <?php if (strcmp($title, "")) { ?>
            <?= $title ?> - Xau Fome
            <?php } else { ?>
                Xau Fome
            <?php } ?>
        </title>

        <link rel="stylesheet" href="/style/index.css" />

        <?php foreach ($styles as $style) { ?>
            <link rel="stylesheet" src="/style/<?= $style ?>" />
        <?php } ?>

        <?php foreach ($scripts as $script) { ?>
            <script src="/scripts/<?= $script ?>" defer></script>
        <?php } ?>

        <meta name="description" content="<?= $description ?>" />
    </head>
<?php } ?>

<?php function createButton(
    ButtonType $type = ButtonType::CONTAINED, string $text = "",
    string $icon = "", string $component = "button", string $class = "",
    bool $submit = false, bool $next = false, bool $back = false
) { ?>
    <<?= $component ?> 
        class="button <?= $type->value ?> <?= $class ?>" 
        type="<?= $submit ? "submit" : "button" ?>"
        <?php if ($next && !$back) echo "next "; if ($back && !$next) echo "back "; ?>
    >
        <?php if ($icon) createIcon($icon) ?>
        <?= $text ?>
    </<?= $component ?>>
<?php } ?>

<?php function createIcon(string $icon, string $component = "span") { ?>
    <<?= $component ?> class="icon"><?= $icon ?></<?= $component ?>>
<?php } ?>

<?php function createTextField(
    string $name, string $label, 
    string $helperText = "",
    string $type = "text", string $autocomplete = "", string $pattern = "",
    int $maxlength = -1, int $minlength = -1,
    bool $optional = false,  bool $toggleVisibility = false,
    bool $characterCounter = false, bool $errorText = true,
    array $errors = []
) { 
    $describedby = [];
    if ($helperText !== "") $describedby[] = "$name-helper-text";
    if ($errors !== [] || $errorText) $describedby[] = "$name-error-text";
    $describedby = implode(" ", $describedby);
    ?>
    <div class="textfield">
        <input
            type="<?= $type ?>"
            placeholder=" "
            id="<?= $name ?>"
            name="<?= $name ?>"
            <?php if ($autocomplete !== "") { ?>
            autocomplete="<?= $autocomplete ?>"
            <?php } ?>
            <?php if ($pattern !== "") { ?>
            pattern="<?= $pattern ?>"
            <?php } ?>
            <?php if ($minlength !== -1) { ?>
            minlength="<?= $minlength ?>"
            <?php } ?>
            <?php if ($maxlength !== -1) { ?>
            maxlength="<?= $maxlength ?>"
            <?php } ?>
            <?php 
            if (!$optional)
                echo "required\n";

            if ($describedby !== "") 
                echo "aria-describedby=\"$describedby\"\n";
                
            if ($errors !== [] || $errorText) 
                echo "error-text=\"$name-error-text\"\n";
            ?>
        />
        <label for="<?= $name ?>"><?= $label ?></label>
        <?php if ($toggleVisibility) { ?>
        <button
            type="button"
            class="toggle-visible icon button"
            aria-label="Toggle <?= $name ?> visibility"
        ></button>
        <?php } ?>
        <?php if ($helperText !== "") { ?>
        <span 
            class="error-text" 
            id="<?= $name ?>-helper-text"
        ><?= $helperText ?></span>
        <?php } ?>
        <?php if ($errors !== [] || $errorText) { ?>
        <span 
            class="error-text"
            aria-live="assertive"
            id="<?= $name ?>-error-text"
            <?php 
            foreach ($errors as $key => $value)
                echo "error:$key=\"$value\""
            ?>
        ></span>
        <?php } ?>
        <?php if ($characterCounter) { ?>
        <span class="character-counter" aria-hidden="true"></span>
        <?php } ?>
    </div>
<?php } ?>

<?php function createUserButtons() {
            
    session_start();

    if (isset($_SESSION['user'])) {?>

    <button type="button" class="button contained">Cart</button>
    <a type="button" class="button contained" href="/profile/">Profile</a>
    <a type="button" class="button contained" href="/actions/logout.php">Log out</a>

    <?php } else { ?>
    
    <a type="button" class="button contained" href="/login/">Login</a>
    <a type="button" class="button contained" href="/register/">Register</a>

    <?php } ?>
<?php } ?>

<?php function createMainPageCard(
    string $title = "Title goes here", string $secondary_text = "Secondary text",
    string $main_text = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor",
    bool $is_elevated = false, int $restaurant_id = -1 // unused for now
) { ?>
    <article class="card <?php if ($is_elevated) { echo "elevated"; } ?>">
        <header>
            <img src="https://picsum.photos/40" alt="" class="avatar" />
            <h3 class="h6"><?=$title?></h3>
            <span class="subtitle2 secondary"><?=$secondary_text?></span>
        </header>
        <img
            src="https://picsum.photos/316/194"
            width="316"
            height="194"
            alt=""
            class="full media"
        />
        <span class="body2 secondary"><?=$main_text?></span>
        <section class="actions">
            <button class="button text">Action 1</button>
            <button class="button text">Action 2</button>
        </section>
    </article>
<?php } ?>
