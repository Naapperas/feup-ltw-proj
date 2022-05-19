<?php
declare(strict_types=1);

enum ButtonType: string {
    case CONTAINED = "contained";
    case OUTLINED = "outlined";
    case TEXT = "text";
    case ICON = "icon";
}

include_once(dirname(__DIR__)."/database/models/user.php");

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
            <script src="/scripts/<?= $script ?>" defer></script>
        <?php } ?>
    </head>
<?php } ?>

<?php function createButton(
    ButtonType $type = ButtonType::CONTAINED, string $text = "",
    string $icon = "", string $class = "", string $href = "",
    bool $submit = false, bool $next = false, bool $back = false,
    string $onClickHandler = null
) {
    $component = $href === '' ? 'button' : 'a';
    ?>

    <<?= $component ?>
        class="button <?= $type->value ?> <?= $class ?>"
        <?php if ($component === "button") { ?>
            type="<?= $submit ? "submit" : "button" ?>"
            <?php 
            if ($next && !$back)
                echo "data-next ";
            if ($back && !$next)
                echo "data-back ";
            ?>
        <?php } elseif ($component === "a") { ?>
            href="<?= $href ?>"
        <?php } ?>
        <?php if ($type == ButtonType::ICON && $text) { ?>
            aria-label="<?= $text ?>"
        <?php } ?>
        <?php if ($onClickHandler !== null) { ?>
            onclick="<?= $onClickHandler ?>(event)"
        <?php }?>
    >
        <?php if ($type == ButtonType::ICON) { ?>
            <?= $icon ?>
        <?php } else { ?>
            <?php if ($icon) createIcon($icon) ?>
            <?= $text ?>
        <?php } ?>
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
                echo "data-error-text=\"$name-error-text\"\n";
            ?>
        />
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

<?php function createUserButtons() {
    session_start();

    if (isset($_SESSION['user'])) {    
        createButton(
            type: ButtonType::ICON,
            text: "Shopping cart",
            icon: "shopping_cart");
        createButton(
            type: ButtonType::ICON, 
            text: "Profile",
            icon: "account_circle",
            href: "/profile/");
        createButton(
            type: ButtonType::ICON,
            text: "Logout",
            icon: "logout",
            href: "/actions/logout.php");
    } else {
        createButton(
            type: ButtonType::ICON,
            text: "Register",
            icon: "login",
            href: "/register/");
    }
} ?>

<?php function createColorSchemeToggle() {
    createButton(
        type: ButtonType::ICON, 
        text: "Toggle color scheme", 
        class: "color-scheme-toggle"
    );
} ?>

<?php function createRestaurantCard(Restaurant $restaurant) { ?>
    <a 
        href="/restaurant/?id=<?= $restaurant->id ?>" 
        data-card-type="restaurant" 
        data-restaurant-id="<?= $restaurant->id ?>"
    >
        <article class="card responsive interactive">
            <img
                src="https://picsum.photos/316/194"
                width="320"
                height="180"
                alt="Profile picture for <?= $restaurant->name ?>"
                class="full media"
            />
            <header class="header">
                <h3 class="title h6"><?= $restaurant->name ?></h3>
                <span class="subtitle subtitle2 secondary"><?= $restaurant->address ?></span>
                <?php if (($avgScore = $restaurant->getReviewScore()) !== null) { ?>
                <span class="chip right"><?php createIcon(icon: "star") ?><?= $avgScore ?></span>
                <?php } ?>
            </header>
            <?php
            if (($categories = $restaurant->getCategories()) !== []){
                echo '<hr class="divider" />';
                createRestaurantCategories($categories, 'h4');
            }
            
            session_start();

            if (isset($_SESSION['user'])) {

                $currentUser = User::get($_SESSION['user']);
    
                if ($currentUser !== null && $restaurant->isLikedBy($currentUser)) {
                    $icon = "favorite";
                    $text = "Unfavorite";
                } else {
                    $icon = "favorite_border";
                    $text = "Favorite";
                }
    
                createButton(
                    type: ButtonType::ICON, icon: $icon,
                    text: $text, class: "top-right",
                    onClickHandler:"toggleRestaurantLikedStatus"
                );
            } ?>
        </article>
    </a>
<?php } ?>

<?php function createAppBar() { ?>
    <header class="appbar elevated fixed">
        <a href="/" class="title homepage-link"><h1 class="h6 color logo"></h1></a>

        <form class="search" action="/search/" method="GET">
            <input
                type="search"
                placeholder="Search"
                id="search"
                name="q"
            />
            <label for="search">search</label>
            <?php createButton(
                type: ButtonType::ICON,
                icon: "search",
                text: "Search",
                submit: true
            ) ?>
        </form>

        <?php
        createColorSchemeToggle();
        createUserButtons();
        ?>

    </header>
<?php } ?>

<?php function createRestaurantCategories(array $categories, string $h) { ?>
    <section class="chip-list wrap">
        <<?= $h ?> class="subtitle2" >Categories</<?= $h ?>>
        <?php foreach($categories as $category) { ?>
            <span class="chip"><?= $category->name ?></span>
        <?php } ?>
    </section>
<?php } ?>
