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
        <title><?= $title ?> - Xau Fome</title>

        <link rel="stylesheet" href="../style/index.css" />

        <?php foreach ($styles as $style) { ?>
            <link rel="stylesheet" src="../style/<?= $style ?>" />
        <?php } ?>

        <?php foreach ($scripts as $script) { ?>
            <script src="../scripts/<?= $script ?>" defer></script>
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
    string $helperText = "", string $errorText = "",
    string $type = "text", string $autocomplete = "", string $pattern = "",
    int $maxlength = -1, int $minlength = -1,
    bool $optional = false,  bool $toggleVisibility = false, bool $characterCounter = false
) { ?>
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
            <?php if (!$optional) echo "required" ?>
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
        <span class="error-text"><?= $helperText ?></span>
        <?php } ?>
        <?php if ($errorText !== "") { ?>
        <span class="error-text"><?= $errorText ?></span>
        <?php } ?>
        <?php if ($characterCounter) { ?>
        <span class="character-counter"></span>
        <?php } ?>
    </div>
<?php } ?>
