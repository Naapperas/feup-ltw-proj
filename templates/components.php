<?php
declare(strict_types=1);

enum ButtonType: string {
    case CONTAINED = "contained";
    case OUTLINED = "outlined";
    case TEXT = "text";
    case ICON = "icon";
    case FAB = "icon fab";
    case MINI_FAB = "icon fab mini";
}

require_once(dirname(__DIR__)."/database/models/user.php");
require_once(dirname(__DIR__)."/database/models/restaurant.php");
require_once(dirname(__DIR__)."/database/models/dish.php");
require_once(dirname(__DIR__)."/database/models/menu.php");
require_once(dirname(__DIR__)."/database/models/review.php");

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
    $is_icon = $type === ButtonType::ICON || $type === ButtonType::FAB || $type === ButtonType::MINI_FAB;
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
        <?php if ($is_icon && $text) { ?>
            aria-label="<?= $text ?>"
        <?php } ?>
    >
        <?php if ($is_icon) { ?>
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
                <span class="chip right"><?php createIcon(icon: "star") ?><?= round($avgScore, 1) ?></span>
                <?php } ?>
            </header>
            <?php
            if (($categories = $restaurant->getCategories()) !== []) {
                createRestaurantCategories($categories);
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

<?php function createRestaurantCategories(array $categories) { ?>
    <section class="chip-list wrap">
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

    if (!$owned)
        return;

    ?>
    <hr class="divider">
    <section class="restaurant-list">
        <header class="header">
            <h3 class="title h6">Owned Restaurants</h3>
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

    if (!$favorites)
        return;

    ?>
    <hr class="divider">
    <section class="restaurant-list">
        <header class="header">
            <h3 class="title h6">Favorite Restaurants</h3>
        </header>

        <?php 
        foreach($favorites as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createDishCard(Dish $dish, bool $show_restaurant = false) { 
    if ($show_restaurant && ($restaurant = $dish->getRestaurant()) === null) return;
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
                <span class="subtitle subtitle2 secondary">
                    <?= $show_restaurant ? $restaurant->name : sprintf('%.2f€', $dish->price) ?>
                </span>
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
                        "data-dish-id=\"$dish->id\"\n".
                        "data-favorite-button" // TODO: change to different kind of button
                );
            } ?>
        </article>
    </a>
<?php } ?>

<?php function createProfileFavoriteDishes(User $user) {
    $favorites = $user->getFavoriteDishes();

    if (!$favorites)
        return;

    ?>
    <hr class="divider">
    <section class="dish-list">
        <header class="header">
            <h3 class="title h6">Favorite Dishes</h3>
        </header>

        <?php 
        foreach($favorites as $dish) {
            createDishCard($dish, true);
        }
        ?>
    </section>
<?php } ?>

<?php function createRestaurantOwnedDishes(Restaurant $restaurant) {
    $owned = $restaurant->getOwnedDishes();

    ?>
    <section class="dish-list">
        <header class="header">
            <h3 class="title h4">Dishes</h3>
        </header>

        <?php 
        foreach($owned as $dish) {
            createDishCard($dish);
        }
        ?>
    </section>
<?php } ?>

<?php function createMenuCard(Menu $menu, bool $show_restaurant = false) { 
    if ($show_restaurant && ($restaurant = $menu->getRestaurant()) === null) return;
?>
    <a 
        href="/menu/?id=<?= $menu->id ?>" 
        data-card-type="menu" 
        data-menu-id="<?= $menu->id ?>"
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
                <span class="subtitle subtitle2 secondary">
                    <?= $show_restaurant ? $restaurant->name : sprintf('%.2f€', $menu->price) ?>
                </span>
            </header>
        </article>
    </a>
<?php } ?>

<?php function createRestaurantOwnedMenus(Restaurant $restaurant) {
    $owned = $restaurant->getOwnedMenus();

    ?>
    <section class="menu-list">
        <header class="header">
            <h2 class="title h4">Menus</h2>
        </header>

        <?php 
        foreach($owned as $dish) {
            createMenuCard($dish);
        }
        ?>
    </section>
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

<?php function createCheckBoxList(array $values, string $title) {?>

    <fieldset class="selection-list">
        <legend class="h6"><?= $title ?></legend>

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

<?php function printReview(Review $review) { 

    $user = User::get($review->client);

    if ($user == null) return;
?>
    <article class="review">
        <header class="review-user-info">
            <a href="/profile/?id=<?= $user->id ?>"><img src="" alt="Review profile image for user <?=$user->id?>"></a>
            <span><?= $user->name ?></span>
            <?php createIcon("star"); ?><span><?= round($review->score, 1) ?></span>
        </header>
        <span class="review-content"><?= $review->text ?></span>
    </article>
<?php } ?>

<?php function createRestaurantReviewList(Restaurant $restaurant) { 

    $reviews = $restaurant->getReviews(7);
?>
    <section class="review-list">
        <header class="header">
            <h4 class="title h4">Reviews</h4>
            <div class="select">
                <select name="options" id="options"> <!-- to be dealt with in JavaScript + AJAX -->
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
                <label for="options">Sort</label>
            </div>
        </header>
        <div>
        <?php foreach($reviews as $review) {
            printReview($review);
        } ?>
        </div>
    </section>
<?php } ?>
