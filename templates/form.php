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
    $id = str_replace("[", "-", str_replace("]", "", $name));
    
    $describedby = [];
    if ($helperText !== "") $describedby[] = "$id-helper-text";
    if ($errors !== [] || $errorText) $describedby[] = "$id-error-text";
    $describedby = implode(" ", $describedby);
    ?>
    <div class="textfield <?= $class ?>">
        <<?php if ($type === 'multiline') { ?>textarea<?php } else { ?>input
            type="<?= $type ?>" <?php } ?>
            placeholder=" "
            id="<?= $id ?>"
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
                echo "data-error-text=\"$id-error-text\"\n";
            ?>
        /><?php if ($type === 'multiline') { ?></textarea><?php } ?>
        <label for="<?= $id ?>"><?= $label ?></label>
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
            id="<?= $id ?>-helper-text"
        ><?= $helperText ?></span>
        <?php } ?>
        <?php if ($errors !== [] || $errorText) { ?>
        <span 
            class="error-text"
            aria-live="assertive"
            id="<?= $id ?>-error-text"
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

<?php function createSlider(
    string $name, string $labelText, float|int $min, float|int $max,
    float|int|null $startValue = null, float|int|string $step = 'any',
    ?bool $ranged = false, ?float $startValueB = null
) { 
    if ($ranged) { ?>
    <fieldset class="slider">
        <legend>
            <?= $labelText ?>: <span data-slider-preview></span>
        </legend>
        <input
            type="range"
            name="min_<?= $name ?>"
            id="min_<?= $name ?>"
            min="<?= $min ?>"
            max="<?= $max ?>"
            value="<?= $startValue ?? $min ?>"
            step="<?= $step ?>"
        />
        <input
            type="range"
            name="max_<?= $name ?>"
            id="max_<?= $name ?>"
            min="<?= $min ?>"
            max="<?= $max ?>"
            value="<?= $startValueB ?? $max ?>"
            step="<?= $step ?>"
        />
        <label for="min_<?= $name ?>" class="visually-hidden">
            Minimum <?= $labelText ?>
        </label>
        <label for="max_<?= $name ?>" class="visually-hidden">
            Maximum <?= $labelText ?>
        </label>
    </fieldset>
    <?php } else { ?>
    <div class="slider">
        <label>
            <?= $labelText ?>: <span data-slider-preview></span>
        </label>
        <input
            type="range"
            name="<?= $name ?>"
            id="<?= $name ?>"
            min="<?= $min ?>"
            max="<?= $max ?>"
            value="<?= $startValue ?? $min ?>"
            step="<?= $step ?>"
        />
    </div>
    <?php }
} ?>

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

<?php function createDishesDialog(Menu $model, string $name = 'dishes[]', string $id = 'dishes') { ?>
    <dialog class="dialog confirmation" id="<?= $id ?>">
        <header><h2 class="h5">Dishes</h2></header>
        <?php createCheckBoxList(array_map(fn(Dish $dish) => [
            'label' => $dish->name,
            'value' => $dish->id,
            'name' => $name,
            'checked' => $model->hasDish($dish->id)
        ], Dish::getAll()), '', 'content'); ?>
        <div class="actions">
            <button class="button text" type="button" data-close-dialog="#<?= $id ?>">Done</button>
        </div>
    </dialog>
<?php } ?>
