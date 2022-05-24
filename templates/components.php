<?php
declare(strict_types=1);

enum ButtonType: string {
    case CONTAINED = "contained";
    case OUTLINED = "outlined";
    case TEXT = "text";
    case ICON = "icon";
}

include_once(dirname(__DIR__)."/database/models/user.php");
include_once(dirname(__DIR__)."/database/models/restaurant.php");
include_once(dirname(__DIR__)."/database/models/dish.php");
include_once(dirname(__DIR__)."/database/models/menu.php");

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

<?php function createButton(
    ButtonType $type = ButtonType::CONTAINED, string $text = "",
    string $icon = "", string $class = "", string $href = "",
    bool $submit = false, bool $next = false, bool $back = false,
    string $attributes = ""
) {
    $component = $href === '' ? 'button' : 'a';
    ?>

    <<?= $component ?>
        class="button <?= $type->value ?> <?= $class ?>"
        <?= $attributes ?>
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
    array $errors = [], string $value = ""
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
            <?php if ($value !== "") { ?>
            value="<?= $value ?>"    
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
            type: ButtonType::TEXT,
            text: "Login",
            href: "/login/");
        createButton(
            type: ButtonType::TEXT,
            text: "Register",
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
            if (($categories = $restaurant->getCategories()) !== []) {
                echo '<hr class="divider" />';
                createRestaurantCategories($categories, 'h4');
            }
            
            session_start();

            if (isset($_SESSION['user'])) {

                $currentUser = User::get($_SESSION['user']);
    
                if ($currentUser !== null && $restaurant->isLikedBy($currentUser)) {
                    $state = "on";
                    $text = "Unfavorite";
                } else {
                    $state = "off";
                    $text = "Favorite";
                }
    
                createButton(
                    type: ButtonType::ICON, text: $text, class: "top-right toggle",
                    attributes: 
                        "data-on-icon=\"favorite\"\n".
                        "data-off-icon=\"favorite_border\"\n".
                        "data-toggle-state=\"$state\"\n".
                        "data-favorite-button"
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

<?php function createFavoriteRestaurants(User $user) {
    $favorites = $user->getFavoriteRestaurants();

    ?>
    <section class="restaurant-list">
        <header class="header">
            <h2 class="title h6">Your favorites</h2>
            <?php createButton(
                type: ButtonType::TEXT, text: "See all",
                class: "right",
                href: "/restaurants/"
            ) ?>
        </header>

        <?php 
        foreach($favorites as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
    <hr class="divider">
<?php } ?>

<?php function createProfileOwnedRestaurants(User $user) {
    $owned = $user->getOwnedRestaurants();

    ?>
    <hr class="divider">
    <section class="restaurant-list">
        <header class="header">
            <h2 class="title h6">Owned Restaurants</h2>
        </header>

        <?php 
        foreach($owned as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createProfileFavoriteRestaurants(User $user) {
    $favorites = $user->getFavoriteRestaurants();

    ?>
    <hr class="divider">
    <section class="restaurant-list">
        <header class="header">
            <h2 class="title h6">Favorite Restaurants</h2>
        </header>

        <?php 
        foreach($favorites as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createDishCard(Dish $dish) { 

    $restaurant = $dish->getRestaurant();

    if ($restaurant === null) return;
?>
    <a 
        href="/dish/?id=<?= $dish->id ?>" 
        data-card-type="dish" 
        data-dish-id="<?= $dish->id ?>"
    >
        <article class="card responsive interactive">
            <img
                src="https://picsum.photos/316/194"
                width="320"
                height="180"
                alt="Dish picture for <?= $dish->name ?>"
                class="full media"
            />
            <header class="header">
                <h3 class="title h6"><?= $dish->name ?></h3>
                <span class="subtitle subtitle2 secondary"><?= $restaurant->name ?></span>
                <span class="chip right"><?php createIcon(icon: "euro") ?><?= $dish->price ?></span>
            </header>
            <?php
            if (($categories = $dish->getCategories()) !== []) {
                echo '<hr class="divider" />';
                // createDishCategories($categories, 'h4');
            }
            
            session_start();

            if (isset($_SESSION['user'])) {

                $currentUser = User::get($_SESSION['user']);
    
                if ($currentUser !== null && $dish->isLikedBy($currentUser)) {
                    $state = "on";
                    $text = "Unfavorite";
                } else {
                    $state = "off";
                    $text = "Favorite";
                }
    
                createButton(
                    type: ButtonType::ICON, text: $text, class: "top-right toggle",
                    attributes: 
                        "data-on-icon=\"favorite\"\n".
                        "data-off-icon=\"favorite_border\"\n".
                        "data-toggle-state=\"$state\"\n".
                        "data-favorite-button" // TODO: change to different kind of button
                );
            } ?>
        </article>
    </a>
<?php } ?>

<?php function createProfileFavoriteDishes(User $user) {
    $favorites = $user->getFavoriteDishes();

    ?>
    <hr class="divider">
    <section class="dish-list">
        <header class="header">
            <h2 class="title h6">Favorite Dishes</h2>
        </header>

        <?php 
        foreach($favorites as $dish) {
            createDishCard($dish);
        }
        ?>
    </section>
<?php } ?>

<?php function createRestaurantOwnedDishes(Restaurant $restaurant) {
    $owned = $restaurant->getOwnedDishes();

    ?>
    <hr class="divider">
    <section class="dish-list">
        <header class="header">
            <h2 class="title h6">Available Dishes</h2>
        </header>

        <?php 
        foreach($owned as $dish) {
            createDishCard($dish);
        }
        ?>
    </section>
<?php } ?>

<?php function createMenuCard(Menu $menu) { 

    $restaurant = $menu->getRestaurant();

    if ($restaurant === null) return;
?>
    <a 
        href="/menu/?id=<?= $menu->id ?>" 
        data-card-type="dish" 
        data-dish-id="<?= $menu->id ?>"
    >
        <article class="card responsive interactive">
            <img
                src="https://picsum.photos/316/194"
                width="320"
                height="180"
                alt="Menu picture for <?= $menu->name ?>"
                class="full media"
            />
            <header class="header">
                <h3 class="title h6"><?= $menu->name ?></h3>
                <span class="subtitle subtitle2 secondary"><?= $restaurant->name ?></span>
                <span class="chip right"><?php createIcon(icon: "euro") ?><?= $menu->price ?></span>
            </header>
        </article>
    </a>
<?php } ?>

<?php function createRestaurantOwnedMenus(Restaurant $restaurant) {
    $owned = $restaurant->getOwnedMenus();

    ?>
    <hr class="divider">
    <section class="menu-list">
        <header class="header">
            <h2 class="title h6">Available Menus</h2>
        </header>

        <?php 
        foreach($owned as $dish) {
            createMenuCard($dish);
        }
        ?>
    </section>
<?php } ?>
