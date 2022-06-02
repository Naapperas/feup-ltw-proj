<?php
declare(strict_types=1);
?>

<?php function createTextField(
    string $name, string $label, string $helperText = "",
    string $type = "text", string $autocomplete = "", string $pattern = "",
    string $class = "",
    int $maxlength = -1, int $minlength = -1,
    ?float $min = null, ?float $max = null, ?float $step = null,
    bool $optional = false,  bool $toggleVisibility = false,
    bool $characterCounter = false, bool $errorText = true,
    array $errors = [], string $value = ""
) { 
    $describedby = [];
    if ($helperText !== "") $describedby[] = "$name-helper-text";
    if ($errors !== [] || $errorText) $describedby[] = "$name-error-text";
    $describedby = implode(" ", $describedby);
    ?>
    <div class="textfield <?= $class ?>">
        <<?php if ($type === 'multiline') { ?>textarea<?php } else { ?>input
            type="<?= $type ?>" <?php } ?>
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
            <?php if ($value !== "") { ?>
            value="<?= $value ?>"    
            <?php } ?> 
            <?php if ($min !== null) { ?>
            min="<?= $min ?>"    
            <?php } ?> 
            <?php if ($max !== null) { ?>
            max="<?= $max ?>"    
            <?php } ?> 
            <?php if ($step !== null) { ?>
            step="<?= $step ?>"    
            <?php } ?> 
            <?php 
            if (!$optional)
                echo "required\n";

            if ($describedby !== "") 
                echo "aria-describedby=\"$describedby\"\n";
                
            if ($errors !== [] || $errorText) 
                echo "data-error-text=\"$name-error-text\"\n";
            ?>
        /><?php if ($type === 'multiline') { ?></textarea><?php } ?>
        <label for="<?= $name ?>"><?= $label ?></label>
        <?php if ($toggleVisibility) {
            createButton(
                type: ButtonType::ICON,
                text: "Toggle $name visibility",
                class: "toggle-visible"
            ); 
        } ?>
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
                echo "data-$key=\"$value\""
            ?>
        ></span>
        <?php } ?>
        <?php if ($characterCounter) { ?>
        <span class="character-counter" aria-hidden="true"></span>
        <?php } ?>
    </div>
<?php } ?>

<?php function createForm(
    string $method, string $name, string $action,
    callable ...$sections
) { ?>
    <form
        action="<?= $action ?>"
        method="<?= $method ?>"
        enctype="multipart/form-data"
        class="form<?php if (count($sections) > 1) echo ' sectioned' ?>"
        data-empower
    >
        <?php foreach (array_slice($sections, 1) as $section) { ?>
        <fieldset class="section" data-section>
            <?php $section() ?>
        </fieldset>
        <?php }

        $sections[0]();
        
        session_start();

        $error = $_SESSION["$name-error"];
        unset($_SESSION["$name-error"]);

        if (isset($error)) { ?>
        <span class="form-error"><?= $error ?></span>
        <?php } ?>
    </form>
<?php } ?>

<?php function createCheckBox(
    string $label, string $name, int $value, bool $checked
) { ?>

    <label>
        <input 
            type="checkbox"
            name="<?= $name ?>"
            value="<?= $value ?>" 
            class="checkbox" 
            <?php if($checked) echo "checked"; ?>
        >
        <?= $label ?>
    </label>

<?php } ?>

<?php function createCheckBoxList(array $values, string $title, string $class = "") {?>

    <fieldset class="selection-list <?= $class ?>">
        <?php if ($title) { ?>
        <legend class="h6"><?= $title ?></legend>
        <?php } ?>

        <?php foreach($values as $key) {
            createCheckBox(
                $key['label'],
                $key['name'],
                $key['value'],
                $key['checked']
            );
        } ?>
    </fieldset>

<?php } ?>

<?php function createSlider(string $name, string $labelText, string $defaultLabelValue, int $min, int $max, $startValue, float|int|string $step = null) { ?>
    <div class="slider">
        <label for="<?= $name ?>_slider">
            <?= $labelText ?> <span id="<?= $name ?>_value"><?= $defaultLabelValue ?></span>
        </label>
        <input
            class="slider"
            type="range"
            name="<?= $name ?>"
            id="<?= $name ?>_slider"
            min="<?= $min ?>"
            max="<?= $max ?>"
            value="<?= $startValue ?>"
            <?php if ($step !== null) { echo "step=\"$step\""; } ?>
        />
    </div>
<?php } ?>

<?php function createCategoriesDialog(Restaurant | Dish $model, string $name = 'categories[]', string $id = 'categories') { ?>
    <dialog class="dialog confirmation" id="<?= $id ?>">
        <header><h2 class="h5">Categories</h2></header>
        <?php createCheckBoxList(array_map(fn(Category $category) => [
            'label' => $category->name,
            'value' => $category->id,
            'name' => $name,
            'checked' => $model->hasCategory($category->id)
        ], Category::getAll()), '', 'content'); ?>
        <div class="actions">
            <button class="button text" type="button" data-close-dialog="#<?= $id ?>">Done</button>
        </div>
    </dialog>
<?php } ?>
